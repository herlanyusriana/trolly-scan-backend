<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TrolleyMovementResource;
use App\Models\Driver;
use App\Models\Trolley;
use App\Models\TrolleyMovement;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class TrolleyMovementController extends Controller
{
    public function checkout(Request $request, Trolley $trolley): JsonResponse
    {
        $data = $request->validate([
            'destination' => ['required', 'string', 'max:255'],
            'expected_return_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'vehicle_id' => ['nullable', 'integer', 'exists:vehicles,id'],
            'driver_id' => ['nullable', 'integer', 'exists:drivers,id'],
            'vehicle_snapshot' => ['nullable', 'string', 'max:120'],
            'driver_snapshot' => ['nullable', 'string', 'max:120'],
        ]);

        if ($trolley->type === 'internal') {
            return response()->json([
                'message' => 'Troli internal tidak diperbolehkan keluar area.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($trolley->status === 'out') {
            return response()->json([
                'message' => 'Troli sedang tidak tersedia.',
            ], Response::HTTP_CONFLICT);
        }

        /** @var \App\Models\MobileUser $user */
        $user = $request->user();

        $vehicle = null;
        $driver = null;

        if (! empty($data['vehicle_id'])) {
            $vehicle = Vehicle::query()->find($data['vehicle_id']);
            if ($vehicle && $vehicle->status !== 'available') {
                return response()->json([
                    'message' => 'Kendaraan tidak tersedia untuk digunakan.',
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        if (! empty($data['driver_id'])) {
            $driver = Driver::query()->find($data['driver_id']);
            if ($driver && $driver->status !== 'active') {
                return response()->json([
                    'message' => 'Driver tidak aktif.',
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        $vehicleSnapshot = $data['vehicle_snapshot'] ?? $vehicle?->plate_number;
        $driverSnapshot = $data['driver_snapshot'] ?? $driver?->name;

        $movement = DB::transaction(function () use (
            $trolley,
            $user,
            $data,
            $vehicle,
            $driver,
            $vehicleSnapshot,
            $driverSnapshot
        ) {
            $now = Carbon::now();

            $periodStart = $now->copy()->setTime(6, 0);
            if ($now->lt($periodStart)) {
                $periodStart->subDay();
            }
            $periodEnd = $periodStart->copy()->addDay();

            $lastSequence = TrolleyMovement::query()
                ->whereBetween('checked_out_at', [$periodStart, $periodEnd])
                ->max('sequence_number') ?? 0;

            $movement = TrolleyMovement::query()->create([
                'trolley_id' => $trolley->id,
                'mobile_user_id' => $user->id,
                'status' => 'out',
                'sequence_number' => $lastSequence + 1,
                'checked_out_at' => $now,
                'expected_return_at' => data_get($data, 'expected_return_at'),
                'destination' => data_get($data, 'destination'),
                'notes' => data_get($data, 'notes'),
                'vehicle_id' => $vehicle?->id,
                'driver_id' => $driver?->id,
                'vehicle_snapshot' => $vehicleSnapshot,
                'driver_snapshot' => $driverSnapshot,
            ]);

            $trolley->update([
                'status' => 'out',
                'location' => $data['destination'],
            ]);

            return $movement;
        });

        return (new TrolleyMovementResource($movement->load(['mobileUser', 'vehicle', 'driver'])))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function checkin(Request $request, Trolley $trolley): JsonResponse
    {
        $data = $request->validate([
            'location' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $movement = $trolley->movements()
            ->where('status', 'out')
            ->latest('checked_out_at')
            ->first();

        if (! $movement) {
            return response()->json([
                'message' => 'Tidak ada data checkout aktif untuk troli ini.',
            ], Response::HTTP_CONFLICT);
        }

        $movement->update([
            'status' => 'in',
            'checked_in_at' => Carbon::now(),
            'notes' => $data['notes'] ?? $movement->notes,
        ]);

        $trolley->update([
            'status' => 'in',
            'location' => $data['location'],
        ]);

        return (new TrolleyMovementResource($movement->fresh()->load(['mobileUser', 'vehicle', 'driver'])))->response();
    }

    public function history(Trolley $trolley): AnonymousResourceCollection
    {
        return TrolleyMovementResource::collection(
            $trolley->movements()
                ->with(['mobileUser', 'vehicle', 'driver'])
                ->latest('checked_out_at')
                ->limit(20)
                ->get()
        );
    }
}
