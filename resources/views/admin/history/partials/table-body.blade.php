@php
    $eventRows = [];
    $hideInEvents = request()->filled('sequence_number');
    foreach ($movements as $movement) {
        $eventRows[] = ['movement' => $movement, 'type' => 'out'];
        if ($movement->checked_in_at && ! $hideInEvents) {
            $eventRows[] = ['movement' => $movement, 'type' => 'in'];
        }
    }

    // Sort combined events newest first
    usort($eventRows, static function (array $a, array $b): int {
        $aTime = $a['type'] === 'out'
            ? optional($a['movement']->checked_out_at)->getTimestamp()
            : optional($a['movement']->checked_in_at ?? $a['movement']->checked_out_at)->getTimestamp();
        $bTime = $b['type'] === 'out'
            ? optional($b['movement']->checked_out_at)->getTimestamp()
            : optional($b['movement']->checked_in_at ?? $b['movement']->checked_out_at)->getTimestamp();
        return ($bTime ?? 0) <=> ($aTime ?? 0);
    });
@endphp

@forelse ($eventRows as $event)
    @php
        /** @var \App\Models\TrolleyMovement $movement */
        $movement = $event['movement'];
        $isOutEvent = $event['type'] === 'out';
        $statusBadgeClasses = $isOutEvent
            ? 'border-rose-400/40 bg-rose-500/10 text-rose-200'
            : 'border-emerald-400/40 bg-emerald-500/10 text-emerald-200';
        $statusLabel = $isOutEvent ? 'OUT' : 'IN';
        $checkedOutAt = optional($movement->checked_out_at)->format('d M Y H:i');
        $checkedInAt = optional($movement->checked_in_at)->format('d M Y H:i');
        $location = $isOutEvent
            ? ($movement->destination ?? '—')
            : ($movement->return_location ?? $movement->destination ?? '—');
    @endphp
    <tr class="transition hover:bg-slate-900/60">
        <td class="px-4 py-3 text-slate-300">
            {{ $isOutEvent && $movement->sequence_number ? str_pad((string) $movement->sequence_number, 2, '0', STR_PAD_LEFT) : '—' }}
        </td>
        <td class="px-4 py-3 font-semibold text-white">{{ $movement->trolley?->code ?? '-' }}</td>
        <td class="px-4 py-3 text-slate-300">{{ $movement->trolley?->type_label ?? '—' }}</td>
        <td class="px-4 py-3 text-slate-300">{{ $movement->trolley?->kind_label ?? '—' }}</td>
        <td class="px-4 py-3">
            <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold uppercase {{ $statusBadgeClasses }}">
                {{ $statusLabel }}
            </span>
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
            {{ $checkedOutAt ?? '—' }}
        </td>
        <td class="px-4 py-3 text-slate-500">
            {{ $checkedInAt ?? '—' }}
        </td>
    </tr>
@empty
    <tr>
        <td colspan="12" class="px-6 py-12 text-center text-slate-500">
            Belum ada data pergerakan sesuai filter.
        </td>
    </tr>
@endforelse
