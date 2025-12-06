@php
    // Group movements by sequence_number
    $groupedMovements = [];
    foreach ($movements as $movement) {
        $seqNum = $movement->sequence_number ?? 'no_seq';
        if (!isset($groupedMovements[$seqNum])) {
            $groupedMovements[$seqNum] = [];
        }
        $groupedMovements[$seqNum][] = $movement;
    }

    // Sort groups by newest first
    uasort($groupedMovements, static function (array $a, array $b): int {
        $aTime = optional($a[0]->checked_out_at ?? $a[0]->created_at)->getTimestamp();
        $bTime = optional($b[0]->checked_out_at ?? $b[0]->created_at)->getTimestamp();
        return ($bTime ?? 0) <=> ($aTime ?? 0);
    });
@endphp

@forelse ($groupedMovements as $seqNum => $groupMovements)
    @php
        /** @var \App\Models\TrolleyMovement $firstMovement */
        $firstMovement = $groupMovements[0];
        
        // Calculate duration in days for OUT status (use first movement)
        $durationDays = 0;
        $rowBgClass = '';
        $statusBadgeClasses = '';
        $durationIcon = '';
        
        if ($firstMovement->status === 'out' && $firstMovement->checked_out_at) {
            $durationDays = $firstMovement->checked_out_at->diffInDays(now());
            
            if ($durationDays > 6) {
                // > 6 days - Rose/Red
                $statusBadgeClasses = 'border-rose-500/60 bg-rose-500/20 text-rose-200 font-bold';
                $rowBgClass = 'bg-rose-950/30';
                $durationIcon = '🚨';
            } elseif ($durationDays >= 3) {
                // 3-6 days - Amber/Yellow
                $statusBadgeClasses = 'border-amber-500/60 bg-amber-500/20 text-amber-200 font-bold';
                $rowBgClass = 'bg-amber-950/20';
                $durationIcon = '⚠️';
            } else {
                // < 3 days - Blue (normal OUT)
                $statusBadgeClasses = 'border-blue-400/40 bg-blue-500/10 text-blue-200';
                $rowBgClass = '';
                $durationIcon = '';
            }
        } else {
            // IN status
            $statusBadgeClasses = 'border-emerald-400/40 bg-emerald-500/10 text-emerald-200';
        }
        
        $statusLabel = strtoupper($firstMovement->status);
        $checkedAt = optional($firstMovement->checked_out_at ?? $firstMovement->created_at)->format('d M Y H:i');
        $location = $firstMovement->status === 'out'
            ? ($firstMovement->destination ?? '—')
            : ($firstMovement->return_location ?? $firstMovement->destination ?? '—');
            
        // Collect all trolley codes
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
    @endphp
    <tr class="transition hover:bg-slate-900/60 {{ $rowBgClass }}">
        <td class="px-4 py-3 text-slate-300">
            {{ $seqNum !== 'no_seq' ? str_pad((string) $seqNum, 2, '0', STR_PAD_LEFT) : '—' }}
        </td>
        <td class="px-4 py-3 font-semibold text-white">
            <div class="flex flex-wrap gap-1">
                @foreach($trolleyCodes as $code)
                    <span class="inline-block rounded border border-slate-700 bg-slate-800/50 px-2 py-0.5 font-mono text-xs">{{ $code }}</span>
                @endforeach
                @if(empty($trolleyCodes))
                    <span class="text-slate-500">—</span>
                @endif
            </div>
        </td>
        <td class="px-4 py-3 text-slate-300">
            @if(!empty($trolleyTypes))
                {{ implode(', ', $trolleyTypes) }}
            @else
                —
            @endif
        </td>
        <td class="px-4 py-3 text-slate-300">
            @if(!empty($trolleyKinds))
                {{ implode(', ', $trolleyKinds) }}
            @else
                —
            @endif
        </td>
        <td class="px-4 py-3">
            <span class="inline-flex items-center gap-1 rounded-full border px-3 py-1 text-xs font-semibold uppercase {{ $statusBadgeClasses }}">
                {{ $statusLabel }}
                @if($durationIcon)
                    <span class="ml-1">{{ $durationIcon }}</span>
                @endif
            </span>
            @if($firstMovement->status === 'out' && $durationDays > 0)
                <div class="mt-1 text-xs text-slate-500">
                    {{ $durationDays }} hari keluar
                </div>
            @endif
        </td>
        <td class="px-4 py-3 text-slate-300">{{ $firstMovement->mobileUser?->name ?? '—' }}</td>
        <td class="px-4 py-3 text-slate-300">
            {{ $firstMovement->vehicle?->plate_number ?? $firstMovement->vehicle_snapshot ?? '—' }}
        </td>
        <td class="px-4 py-3 text-slate-300">
            {{ $firstMovement->driver?->name ?? $firstMovement->driver_snapshot ?? '—' }}
        </td>
        <td class="px-4 py-3 text-slate-300">{{ $location }}</td>
        <td class="px-4 py-3 text-slate-500">{{ $firstMovement->notes ?? '—' }}</td>
        <td class="px-4 py-3 text-slate-400">
            {{ $checkedAt ?? '—' }}
        </td>
    </tr>
@empty
    <tr>
        <td colspan="11" class="px-6 py-12 text-center text-slate-500">
            Belum ada data pergerakan sesuai filter.
        </td>
    </tr>
@endforelse
