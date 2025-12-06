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
        $overdueMovements = $this->getOverdueMovements();

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentMovements' => $recentMovements,
            'pendingUsers' => $pendingUsers,
            'overdueMovements' => $overdueMovements,
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
                'overdue' => $stats['trolleys']['overdue'],
                'approved' => $stats['mobile_users']['approved'],
                'pending' => $stats['mobile_users']['pending'],
                'kinds' => $stats['trolleys']['kinds'],
                'kinds_out' => $stats['trolleys']['kinds_out'],
                'duration_categories' => $stats['trolleys']['duration_categories'],
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

        $threeDaysAgo = now()->subDays(3);
        $sixDaysAgo = now()->subDays(6);
        
        // Count by duration categories
        $lessThan3Days = TrolleyMovement::query()
            ->where('status', 'out')
            ->where('checked_out_at', '>', $threeDaysAgo)
            ->whereNull('checked_in_at')
            ->count();
            
        $between3And6Days = TrolleyMovement::query()
            ->where('status', 'out')
            ->where('checked_out_at', '<=', $threeDaysAgo)
            ->where('checked_out_at', '>', $sixDaysAgo)
            ->whereNull('checked_in_at')
            ->count();
            
        $moreThan6Days = TrolleyMovement::query()
            ->where('status', 'out')
            ->where('checked_out_at', '<=', $sixDaysAgo)
            ->whereNull('checked_in_at')
            ->count();
        
        $overdueCount = TrolleyMovement::query()
            ->where('status', 'out')
            ->where('checked_out_at', '<=', $threeDaysAgo)
            ->whereNull('checked_in_at')
            ->count();

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
                'overdue' => $overdueCount,
                'duration_categories' => [
                    'less_than_3' => $lessThan3Days,
                    'between_3_and_6' => $between3And6Days,
                    'more_than_6' => $moreThan6Days,
                ],
            ],
        ];
    }

    protected function getRecentMovements()
    {
        return TrolleyMovement::query()
            ->with(['trolley', 'mobileUser'])
            ->orderByRaw('COALESCE(checked_in_at, checked_out_at, created_at) DESC')
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

    protected function getOverdueMovements()
    {
        $threeDaysAgo = now()->subDays(3);

        return TrolleyMovement::query()
            ->with(['trolley', 'mobileUser'])
            ->where('status', 'out')
            ->where('checked_out_at', '<=', $threeDaysAgo)
            ->whereNull('checked_in_at')
            ->orderBy('checked_out_at', 'asc')
            ->get()
            ->map(function ($movement) {
                $movement->days_out = now()->diffInDays($movement->checked_out_at);
                return $movement;
            });
    }
}
