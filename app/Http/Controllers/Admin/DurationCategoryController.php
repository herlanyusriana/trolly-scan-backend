<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrolleyMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DurationCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $category = $request->input('category', 'less_than_3');
        
        $categoryConfig = $this->getCategoryConfig($category);
        
        $query = $this->buildQuery($category);
        
        $movements = $query
            ->with(['trolley', 'mobileUser', 'vehicle', 'driver'])
            ->orderBy('checked_out_at', 'asc')
            ->paginate(20)
            ->appends(['category' => $category]);
        
        $stats = [
            'total' => (clone $query)->count(),
        ];
        
        // Add days_out to each movement
        $movements->getCollection()->transform(function ($movement) {
            if ($movement->checked_out_at) {
                $movement->days_out = $movement->checked_out_at->diffInDays(now());
            }
            return $movement;
        });
        
        return view('admin.duration-category.index', [
            'movements' => $movements,
            'stats' => $stats,
            'category' => $category,
            'categoryConfig' => $categoryConfig,
        ]);
    }
    
    public function export(Request $request): StreamedResponse
    {
        $category = $request->input('category', 'less_than_3');
        $categoryConfig = $this->getCategoryConfig($category);
        
        $query = $this->buildQuery($category)
            ->with(['trolley', 'mobileUser', 'vehicle', 'driver'])
            ->orderBy('checked_out_at', 'asc');
        
        $filename = 'trolley-' . $category . '-' . now()->format('Ymd_His') . '.csv';
        
        return Response::streamDownload(function () use ($query): void {
            $handle = fopen('php://output', 'w');
            
            fputcsv($handle, [
                'No. Urut',
                'Troli',
                'Jenis',
                'Tipe',
                'User',
                'Phone',
                'Tujuan',
                'Kendaraan',
                'Driver',
                'Waktu Keluar',
                'Durasi (Hari)',
                'Catatan',
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
                
                $daysOut = $firstMovement->checked_out_at ? $firstMovement->checked_out_at->diffInDays(now()) : 0;
                
                fputcsv($handle, [
                    str_starts_with($seqNum, 'no_seq_') ? '-' : str_pad((string) $seqNum, 2, '0', STR_PAD_LEFT),
                    !empty($trolleyCodes) ? implode(', ', $trolleyCodes) : '-',
                    !empty($trolleyTypes) ? implode(', ', $trolleyTypes) : '-',
                    !empty($trolleyKinds) ? implode(', ', $trolleyKinds) : '-',
                    $firstMovement->mobileUser?->name ?? '-',
                    $firstMovement->mobileUser?->phone ?? '-',
                    $firstMovement->destination ?? '-',
                    $firstMovement->vehicle?->plate_number ?? $firstMovement->vehicle_snapshot ?? '-',
                    $firstMovement->driver?->name ?? $firstMovement->driver_snapshot ?? '-',
                    optional($firstMovement->checked_out_at)->format('Y-m-d H:i:s') ?? '-',
                    $daysOut,
                    $firstMovement->notes ?? '-',
                ]);
            }
            
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
    
    protected function buildQuery(string $category)
    {
        $query = TrolleyMovement::query()
            ->where('status', 'out')
            ->whereNull('checked_in_at');
        
        $now = now();
        
        match($category) {
            'less_than_3' => $query->where('checked_out_at', '>', $now->copy()->subDays(3)),
            'between_3_and_6' => $query->whereBetween('checked_out_at', [
                $now->copy()->subDays(6),
                $now->copy()->subDays(3)
            ]),
            'more_than_6' => $query->where('checked_out_at', '<=', $now->copy()->subDays(6)),
            default => $query
        };
        
        return $query;
    }
    
    protected function getCategoryConfig(string $category): array
    {
        return match($category) {
            'less_than_3' => [
                'title' => '✅ Kurang dari 3 Hari',
                'description' => 'Troli yang keluar kurang dari 3 hari - Kondisi normal',
                'color' => 'emerald',
                'border' => 'border-emerald-500/60',
                'bg' => 'bg-emerald-500/20',
                'text' => 'text-emerald-200',
                'icon' => '✅',
            ],
            'between_3_and_6' => [
                'title' => '⚠️ Antara 3-6 Hari',
                'description' => 'Troli yang keluar antara 3-6 hari - Perlu perhatian khusus',
                'color' => 'amber',
                'border' => 'border-amber-500/60',
                'bg' => 'bg-amber-500/20',
                'text' => 'text-amber-200',
                'icon' => '⚠️',
            ],
            'more_than_6' => [
                'title' => '🚨 Lebih dari 6 Hari',
                'description' => 'Troli yang keluar lebih dari 6 hari - Segera tindak lanjut!',
                'color' => 'rose',
                'border' => 'border-rose-500/60',
                'bg' => 'bg-rose-500/20',
                'text' => 'text-rose-200',
                'icon' => '🚨',
            ],
            default => [
                'title' => 'Kategori Durasi',
                'description' => 'Troli berdasarkan kategori durasi keluar',
                'color' => 'slate',
                'border' => 'border-slate-500/60',
                'bg' => 'bg-slate-500/20',
                'text' => 'text-slate-200',
                'icon' => '📊',
            ],
        };
    }
}
