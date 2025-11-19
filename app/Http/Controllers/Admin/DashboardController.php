<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MobileUser;
use App\Models\Trolley;
use App\Models\TrolleyMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = $this->getStats();
        $recentMovements = $this->getRecentMovements();
        $pendingUsers = $this->getPendingUsers();

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentMovements' => $recentMovements,
            'pendingUsers' => $pendingUsers,
            'statusPills' => [
                'in' => 'border-emerald-400/40 bg-emerald-500/10 text-emerald-200',
                'out' => 'border-rose-400/40 bg-rose-500/10 text-rose-200',
            ],
        ]);
    }

    public function realtime(): JsonResponse
    {
        $stats = $this->getStats();
        $recentMovements = $this->getRecentMovements();

        return response()->json([
            'stats' => [
                'in' => $stats['trolleys']['in'],
                'out' => $stats['trolleys']['out'],
                'approved' => $stats['mobile_users']['approved'],
                'pending' => $stats['mobile_users']['pending'],
                'kinds' => $stats['trolleys']['kinds'],
                'kinds_out' => $stats['trolleys']['kinds_out'],
            ],
            'table' => view('admin.dashboard.partials.recent-rows', [
                'recentMovements' => $recentMovements,
            ])->render(),
        ]);
    }

    protected function getStats(): array
    {
        $trolleyKinds = [
            'reinforce' => 'reinforce',
            'backplate' => 'backplate',
            'compbase' => 'compbase',
        ];

        $outCounts = Trolley::query()
            ->selectRaw('COALESCE(kind, "unknown") as kind, COUNT(*) as total')
            ->where('status', 'out')
            ->groupBy('kind')
            ->pluck('total', 'kind')
            ->toArray();

        $kindsOut = [];
        foreach ($trolleyKinds as $label => $kindKey) {
            $kindsOut[$label] = (int) ($outCounts[$kindKey] ?? 0);
        }

        return [
            'mobile_users' => [
                'pending' => MobileUser::query()->where('status', 'pending')->count(),
                'approved' => MobileUser::query()->where('status', 'approved')->count(),
            ],
            'trolleys' => [
                'total' => Trolley::query()->count(),
                'types' => [
                    'internal' => Trolley::query()->where('type', 'internal')->count(),
                    'external' => Trolley::query()->where('type', 'external')->count(),
                ],
                'kinds' => [
                    'reinforce' => Trolley::query()->where('kind', 'reinforce')->count(),
                    'backplate' => Trolley::query()->where('kind', 'backplate')->count(),
                    'compbase' => Trolley::query()->where('kind', 'compbase')->count(),
                ],
                'kinds_out' => $kindsOut,
                'in' => Trolley::query()->where('status', 'in')->count(),
                'out' => Trolley::query()->where('status', 'out')->count(),
            ],
        ];
    }

    protected function getRecentMovements()
    {
        return TrolleyMovement::query()
            ->with(['trolley', 'mobileUser'])
            ->latest('checked_out_at')
            ->limit(20)
            ->get();
    }

    protected function getPendingUsers()
    {
        return MobileUser::query()
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get();
    }
}
