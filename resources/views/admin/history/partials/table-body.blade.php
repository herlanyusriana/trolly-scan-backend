@php
    $eventRows = [];
    foreach ($movements as $movement) {
        $eventRows[] = ['movement' => $movement];
    }

    // Sort newest first by checked_out_at fallback created_at
    usort($eventRows, static function (array $a, array $b): int {
        $aTime = optional($a['movement']->checked_out_at ?? $a['movement']->created_at)->getTimestamp();
        $bTime = optional($b['movement']->checked_out_at ?? $b['movement']->created_at)->getTimestamp();
        return ($bTime ?? 0) <=> ($aTime ?? 0);
    });
@endphp

@forelse ($eventRows as $event)
    @php
        /** @var \App\Models\TrolleyMovement $movement */
        $movement = $event['movement'];
        
        // Calculate duration in days for OUT status
        $durationDays = 0;
        $rowBgClass = '';
        $statusBadgeClasses = '';
        $durationIcon = '';
        
        if ($movement->status === 'out' && $movement->checked_out_at) {
            $durationDays = $movement->checked_out_at->diffInDays(now());
            
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
        
        $statusLabel = strtoupper($movement->status);
        $checkedAt = optional($movement->checked_out_at ?? $movement->created_at)->format('d M Y H:i');
        $location = $movement->status === 'out'
            ? ($movement->destination ?? '—')
            : ($movement->return_location ?? $movement->destination ?? '—');
    @endphp
    <tr class="transition hover:bg-slate-900/60 {{ $rowBgClass }}">
        <td class="px-4 py-3 text-slate-300">
            {{ $movement->sequence_number ? str_pad((string) $movement->sequence_number, 2, '0', STR_PAD_LEFT) : '—' }}
        </td>
        <td class="px-4 py-3 font-semibold text-white">{{ $movement->trolley?->code ?? '-' }}</td>
        <td class="px-4 py-3 text-slate-300">{{ $movement->trolley?->type_label ?? '—' }}</td>
        <td class="px-4 py-3 text-slate-300">{{ $movement->trolley?->kind_label ?? '—' }}</td>
        <td class="px-4 py-3">
            <span class="inline-flex items-center gap-1 rounded-full border px-3 py-1 text-xs font-semibold uppercase {{ $statusBadgeClasses }}">
                {{ $statusLabel }}
                @if($durationIcon)
                    <span class="ml-1">{{ $durationIcon }}</span>
                @endif
            </span>
            @if($movement->status === 'out' && $durationDays > 0)
                <div class="mt-1 text-xs text-slate-500">
                    {{ $durationDays }} hari keluar
                </div>
            @endif
        </td>
        <td class="px-4 py-3 text-slate-300">{{ $movement->mobileUser?->name ?? '—' }}</td>
        <td class="px-4 py-3 text-slate-300">
            {{ $movement->vehicle?->plate_number ?? $movement->vehicle_snapshot ?? '—' }}
        </td>
        <td class="px-4 py-3 text-slate-300">
            {{ $movement->driver?->name ?? $movement->driver_snapshot ?? '—' }}
        </td>
        <td class="px-4 py-3 text-slate-300">{{ $location }}</td>
        <td class="px-4 py-3 text-slate-500">{{ $movement->notes ?? '—' }}</td>
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
