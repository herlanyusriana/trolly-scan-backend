<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MovementHistoryExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
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
            'Status',
            'Operator',
            'Kendaraan',
            'Driver',
            'Tujuan / Lokasi',
            'Catatan',
            'Waktu',
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
        
        $time = optional($firstMovement->checked_out_at ?? $firstMovement->created_at)->format('Y-m-d H:i:s') ?: '-';
        $location = $firstMovement->status === 'out'
            ? ($firstMovement->destination ?? '-')
            : ($firstMovement->return_location ?? $firstMovement->destination ?? '-');

        return [
            str_starts_with($row['seq_num'], 'no_seq_') ? '-' : str_pad((string) $row['seq_num'], 2, '0', STR_PAD_LEFT),
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
            'E' => 10,
            'F' => 20,
            'G' => 15,
            'H' => 20,
            'I' => 25,
            'J' => 30,
            'K' => 20,
        ];
    }
}
