<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrolleyMovement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
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
            ->latest('checked_out_at')
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
            ->latest('checked_out_at');

        $filename = 'trolley-history-' . now()->format('Ymd_His') . '.csv';

        return Response::streamDownload(function () use ($query): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Sequence',
                'Trolley',
                'Status',
                'Destination / Location',
                'Checked Out At',
                'Checked In At',
                'Operator',
                'Vehicle',
                'Driver',
                'Notes',
            ]);

            $query->chunkById(500, function ($chunk) use ($handle): void {
                foreach ($chunk as $movement) {
                    $baseRow = [
                        $movement->sequence_number ?? '-',
                        $movement->trolley?->code ?? '-',
                        $movement->destination ?? '-',
                        optional($movement->checked_out_at)->format('d M Y H:i'),
                        optional($movement->checked_in_at)->format('d M Y H:i'),
                        $movement->mobileUser?->name ?? '-',
                        $movement->vehicle?->plate_number ?? $movement->vehicle_snapshot ?? '-',
                        $movement->driver?->name ?? $movement->driver_snapshot ?? '-',
                        $movement->notes ?? '-',
                    ];

                    fputcsv($handle, array_merge(
                        [$baseRow[0], $baseRow[1], 'OUT'],
                        array_slice($baseRow, 2)
                    ));

                    if ($movement->checked_in_at) {
                        fputcsv($handle, array_merge(
                            [$baseRow[0], $baseRow[1], 'IN'],
                            array_slice($baseRow, 2)
                        ));
                    }
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
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
            ->latest('checked_out_at')
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
        ]);

        if (! filled($filters['date_from'] ?? null) && ! filled($filters['date_to'] ?? null)) {
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

        return $query;
    }
}
