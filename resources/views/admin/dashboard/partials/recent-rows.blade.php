@php
    $dashboardRows = [];
    foreach ($recentMovements as $movement) {
        $dashboardRows[] = [
            'movement' => $movement,
            'type' => 'out',
        ];

        if ($movement->checked_in_at) {
            $dashboardRows[] = [
                'movement' => $movement,
                'type' => 'in',
            ];
        }
    }
@endphp

@forelse ($dashboardRows as $row)
    @php
        /** @var \App\Models\TrolleyMovement $movement */
        $movement = $row['movement'];
        $isOut = $row['type'] === 'out';
        $badgeClass = $isOut
            ? 'border-rose-400/40 bg-rose-500/10 text-rose-200'
            : 'border-emerald-400/40 bg-emerald-500/10 text-emerald-200';
        $statusLabel = $isOut ? 'OUT' : 'IN';
        $timestamp = $isOut
            ? optional($movement->checked_out_at)->format('d M Y H:i')
            : optional($movement->checked_in_at)->format('d M Y H:i');
        $location = $isOut
            ? ($movement->destination ?? '-')
            : ($movement->return_location ?? $movement->destination ?? '-');
    @endphp
    <tr class="transition hover:bg-slate-900/60">
        <td class="px-6 py-3 font-medium text-white">{{ $movement->trolley->code }}</td>
        <td class="px-6 py-3 text-slate-400">{{ $movement->mobileUser?->name ?? '-' }}</td>
        <td class="px-6 py-3">
            <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold uppercase {{ $badgeClass }}">
                {{ $statusLabel }}
            </span>
        </td>
        <td class="px-6 py-3 text-slate-400">{{ $location }}</td>
        <td class="px-6 py-3 text-slate-400">{{ $timestamp ?? '—' }}</td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="px-6 py-10 text-center text-slate-500">Belum ada data pergerakan.</td>
    </tr>
@endforelse
