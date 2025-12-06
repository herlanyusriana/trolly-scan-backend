<?php

namespace App\Http\Controllers\Admin;

use App\Exports\MovementHistoryExport;
use App\Http\Controllers\Controller;
use App\Models\TrolleyMovement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MovementHistoryController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->validateFilters($request);

        $query = $this->buildQuery($filters);

        $activeFilters = array_filter(
            $filters,
            static fn ($value) => $value !== null && $value !== ''
        );

        $movements = (clone $query)
            ->with(['trolley', 'mobileUser', 'vehicle', 'driver'])
            ->orderByRaw('COALESCE(checked_in_at, checked_out_at, created_at) DESC')
            ->paginate(15)
            ->appends($activeFilters);

        $stats = [
            'total' => (clone $query)->count(),
            'out' => (clone $query)->where('status', 'out')->count(),
            'in' => (clone $query)->where('status', 'in')->count(),
        ];

        return view('admin.history.index', [
            'filters' => $filters,
            'movements' => $movements,
            'stats' => $stats,
            'activeFilters' => $activeFilters,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $filters = $this->validateFilters($request);
        $query = $this->buildQuery($filters)
            ->with(['trolley', 'mobileUser', 'vehicle', 'driver'])
            ->orderByRaw('COALESCE(checked_in_at, checked_out_at, created_at) DESC');

        $filename = 'trolley-history-' . now()->format('Ymd_His') . '.csv';

        return Response::streamDownload(function () use ($query): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'No. Urut',
                'Troli',
                'Jenis',
                'Tipe',
                'Status',
                'Operator',
                'Kendaraan',
                'Driver',
                'Tujuan / Lokasi',
                'Catatan',
                'Waktu',
            ]);

            // Get all movements and group by sequence_number
            $allMovements = $query->get();
            $groupedMovements = [];
            
            foreach ($allMovements as $movement) {
                $seqNum = $movement->sequence_number ?? 'no_seq_' . $movement->id;
                if (!isset($groupedMovements[$seqNum])) {
                    $groupedMovements[$seqNum] = [];
                }
                $groupedMovements[$seqNum][] = $movement;
            }

            // Export each group as one row
            foreach ($groupedMovements as $seqNum => $groupMovements) {
                $firstMovement = $groupMovements[0];
                
                // Collect all trolley codes and types
                $trolleyCodes = [];
                $trolleyTypes = [];
                $trolleyKinds = [];
                
                foreach ($groupMovements as $movement) {
                    if ($movement->trolley) {
                        $trolleyCodes[] = $movement->trolley->code;
                        $type = $movement->trolley->type_label ?? '';
                        $kind = $movement->trolley->kind_label ?? '';
                        if ($type && !in_array($type, $trolleyTypes)) {
                            $trolleyTypes[] = $type;
                        }
                        if ($kind && !in_array($kind, $trolleyKinds)) {
                            $trolleyKinds[] = $kind;
                        }
                    }
                }
                
                $time = optional($firstMovement->checked_out_at ?? $firstMovement->created_at)->format('Y-m-d H:i:s') ?: '-';
                $location = $firstMovement->status === 'out'
                    ? ($firstMovement->destination ?? '-')
                    : ($firstMovement->return_location ?? $firstMovement->destination ?? '-');

                fputcsv($handle, [
                    str_starts_with($seqNum, 'no_seq_') ? '-' : str_pad((string) $seqNum, 2, '0', STR_PAD_LEFT),
                    !empty($trolleyCodes) ? implode(', ', $trolleyCodes) : '-',
                    !empty($trolleyTypes) ? implode(', ', $trolleyTypes) : '-',
                    !empty($trolleyKinds) ? implode(', ', $trolleyKinds) : '-',
                    strtoupper($firstMovement->status),
                    $firstMovement->mobileUser?->name ?? '-',
                    $firstMovement->vehicle?->plate_number ?? $firstMovement->vehicle_snapshot ?? '-',
                    $firstMovement->driver?->name ?? $firstMovement->driver_snapshot ?? '-',
                    $location,
                    $firstMovement->notes ?? '-',
                    $time,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportXlsx(Request $request): BinaryFileResponse
    {
        $filters = $this->validateFilters($request);
        $query = $this->buildQuery($filters)
            ->with(['trolley', 'mobileUser', 'vehicle', 'driver'])
            ->orderByRaw('COALESCE(checked_in_at, checked_out_at, created_at) DESC');

        $movements = $query->get();
        $filename = 'trolley-history-' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new MovementHistoryExport($movements), $filename);
    }

    public function refresh(Request $request): JsonResponse
    {
        $filters = $this->validateFilters($request);
        $query = $this->buildQuery($filters);

        $activeFilters = array_filter(
            $filters,
            static fn ($value) => $value !== null && $value !== ''
        );

        $movements = (clone $query)
            ->with(['trolley', 'mobileUser', 'vehicle', 'driver'])
            ->orderByRaw('COALESCE(checked_in_at, checked_out_at, created_at) DESC')
            ->paginate(15)
            ->appends($activeFilters);

        $stats = [
            'total' => (clone $query)->count(),
            'out' => (clone $query)->where('status', 'out')->count(),
            'in' => (clone $query)->where('status', 'in')->count(),
        ];

        return response()->json([
            'stats' => $stats,
            'table' => view('admin.history.partials.table-body', ['movements' => $movements])->render(),
            'pagination' => $movements->links()->render(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function validateFilters(Request $request): array
    {
        $filters = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'sequence_number' => ['nullable', 'integer', 'min:1'],
            'status' => ['nullable', 'in:in,out'],
            'duration' => ['nullable', 'in:less_than_3,between_3_and_6,more_than_6,more_than_3'],
        ]);

        if (! filled($filters['date_from'] ?? null) && ! filled($filters['date_to'] ?? null) && ! filled($filters['duration'] ?? null)) {
            $filters['date_from'] = now()->subDays(7)->toDateString();
        }

        return $filters;
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    protected function buildQuery(array $filters): Builder
    {
        $query = TrolleyMovement::query();

        if ($from = Arr::get($filters, 'date_from')) {
            $query->whereDate('checked_out_at', '>=', $from);
        }

        if ($to = Arr::get($filters, 'date_to')) {
            $query->whereDate('checked_out_at', '<=', $to);
        }

        if ($sequence = Arr::get($filters, 'sequence_number')) {
            $query->where('sequence_number', (int) $sequence);
        }

        if ($status = Arr::get($filters, 'status')) {
            $query->where('status', $status);
        }

        if ($duration = Arr::get($filters, 'duration')) {
            $query->where('status', 'out')->whereNull('checked_in_at');
            
            $now = now();
            
            match($duration) {
                'less_than_3' => $query->where('checked_out_at', '>', $now->copy()->subDays(3)),
                'between_3_and_6' => $query->whereBetween('checked_out_at', [
                    $now->copy()->subDays(6),
                    $now->copy()->subDays(3)
                ]),
                'more_than_6' => $query->where('checked_out_at', '<=', $now->copy()->subDays(6)),
                'more_than_3' => $query->where('checked_out_at', '<=', $now->copy()->subDays(3)),
                default => null
            };
        }

        return $query;
    }
}
