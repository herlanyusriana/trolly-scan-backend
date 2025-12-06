<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DurationCategoryExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $movements;

    public function __construct($movements)
    {
        $this->movements = $movements;
    }

    public function collection()
    {
        // Group movements by sequence_number
        $groupedMovements = [];
        
        foreach ($this->movements as $movement) {
            $seqNum = $movement->sequence_number ?? 'no_seq_' . $movement->id;
            if (!isset($groupedMovements[$seqNum])) {
                $groupedMovements[$seqNum] = [];
            }
            $groupedMovements[$seqNum][] = $movement;
        }

        // Convert to collection of grouped data
        $result = [];
        foreach ($groupedMovements as $seqNum => $groupMovements) {
            $result[] = [
                'seq_num' => $seqNum,
                'movements' => $groupMovements,
            ];
        }

        return collect($result);
    }

    public function headings(): array
    {
        return [
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
        ];
    }

    public function map($row): array
    {
        $firstMovement = $row['movements'][0];
        
        // Collect all trolley codes and types
        $trolleyCodes = [];
        $trolleyTypes = [];
        $trolleyKinds = [];
        
        foreach ($row['movements'] as $movement) {
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

        return [
            str_starts_with($row['seq_num'], 'no_seq_') ? '-' : str_pad((string) $row['seq_num'], 2, '0', STR_PAD_LEFT),
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
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 25,
            'C' => 20,
            'D' => 15,
            'E' => 20,
            'F' => 15,
            'G' => 25,
            'H' => 15,
            'I' => 20,
            'J' => 20,
            'K' => 12,
            'L' => 30,
        ];
    }
}
