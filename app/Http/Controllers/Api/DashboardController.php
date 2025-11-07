<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MobileUser;
use App\Models\Trolley;
use App\Models\TrolleyMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function mobileSummary(): JsonResponse
    {
        $today = Carbon::today();

        return response()->json([
            'users' => [
                'approved' => MobileUser::query()->where('status', 'approved')->count(),
                'pending' => MobileUser::query()->where('status', 'pending')->count(),
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
            'movements' => [
                'out_today' => TrolleyMovement::query()
                    ->whereDate('checked_out_at', $today)
                    ->count(),
                'in_today' => TrolleyMovement::query()
                    ->whereDate('checked_in_at', $today)
                    ->count(),
            ],
        ]);
    }
}
