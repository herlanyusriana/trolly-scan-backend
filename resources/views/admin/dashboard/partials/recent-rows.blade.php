@php
    $dashboardRows = [];
    foreach ($recentMovements as $movement) {
        $dashboardRows[] = [
            'movement' => $movement,
            'type' => 'out',
            'sortKey' => $movement->checked_out_at ?? $movement->created_at,
        ];

        if ($movement->checked_in_at) {
            $dashboardRows[] = [
                'movement' => $movement,
                'type' => 'in',
                'sortKey' => $movement->checked_in_at ?? $movement->checked_out_at ?? $movement->created_at,
            ];
        }
    }

    usort($dashboardRows, static function (array $a, array $b): int {
        $aTime = $a['sortKey'] ? $a['sortKey']->getTimestamp() : 0;
        $bTime = $b['sortKey'] ? $b['sortKey']->getTimestamp() : 0;
        return $bTime <=> $aTime;
    });
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
        <td class="px-3 py-2 font-medium text-white sm:px-6 sm:py-3">{{ $movement->trolley->code }}</td>
        <td class="px-3 py-2 text-slate-400 sm:px-6 sm:py-3">{{ $movement->mobileUser?->name ?? '-' }}</td>
        <td class="px-3 py-2 sm:px-6 sm:py-3">
            <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-xs font-semibold uppercase sm:px-3 sm:py-1 {{ $badgeClass }}">
                {{ $statusLabel }}
            </span>
        </td>
        <td class="px-3 py-2 text-slate-400 sm:px-6 sm:py-3">{{ $location }}</td>
        <td class="px-3 py-2 text-slate-400 sm:px-6 sm:py-3">{{ $timestamp ?? '—' }}</td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="px-3 py-8 text-center text-slate-500 sm:px-6 sm:py-10">Belum ada data pergerakan.</td>
    </tr>
@endforelse
