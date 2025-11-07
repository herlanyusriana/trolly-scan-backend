<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MobileUser;
use App\Models\Trolley;
use App\Models\TrolleyMovement;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
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
                'in' => Trolley::query()->where('status', 'in')->count(),
                'out' => Trolley::query()->where('status', 'out')->count(),
            ],
        ];

        $recentMovements = TrolleyMovement::query()
            ->with(['trolley', 'mobileUser'])
            ->latest('checked_out_at')
            ->limit(20)
            ->get();

        $pendingUsers = MobileUser::query()
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get();

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
}
