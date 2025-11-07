<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status');

        $query = Vehicle::query()->orderBy('plate_number');

        if ($status && $status !== 'all') {
            if (! in_array($status, Vehicle::STATUSES, true)) {
                return response()->json([
                    'message' => 'Status kendaraan tidak valid.',
                ], 422);
            }

            $query->where('status', $status);
        } else {
            $query->where('status', '!=', 'inactive');
        }

        $vehicles = $query->get([
            'id',
            'plate_number',
            'name',
            'category',
            'status',
        ]);

        return response()->json([
            'data' => $vehicles,
        ]);
    }
}
