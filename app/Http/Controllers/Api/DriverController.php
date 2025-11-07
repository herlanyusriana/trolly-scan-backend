<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status');

        $query = Driver::query()->orderBy('name');

        if ($status && $status !== 'all') {
            if (! in_array($status, Driver::STATUSES, true)) {
                return response()->json([
                    'message' => 'Status driver tidak valid.',
                ], 422);
            }

            $query->where('status', $status);
        } else {
            $query->where('status', 'active');
        }

        $drivers = $query->get([
            'id',
            'name',
            'phone',
            'license_number',
            'status',
        ]);

        return response()->json([
            'data' => $drivers,
        ]);
    }
}
