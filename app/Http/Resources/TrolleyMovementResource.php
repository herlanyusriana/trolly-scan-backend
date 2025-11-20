<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\TrolleyMovement */
class TrolleyMovementResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'trolley_id' => $this->trolley_id,
            'status' => $this->status,
            'sequence_number' => $this->sequence_number,
            'checked_out_at' => $this->checked_out_at,
            'checked_in_at' => $this->checked_in_at,
            'expected_return_at' => $this->expected_return_at,
            'destination' => $this->destination,
            'return_location' => $this->return_location,
            'notes' => $this->notes,
            'vehicle_id' => $this->vehicle_id,
            'driver_id' => $this->driver_id,
            'vehicle_snapshot' => $this->vehicle_snapshot,
            'driver_snapshot' => $this->driver_snapshot,
            'trolley' => $this->whenLoaded('trolley', function () {
                return [
                    'id' => $this->trolley?->id,
                    'code' => $this->trolley?->code,
                    'type' => $this->trolley?->type,
                    'kind' => $this->trolley?->kind,
                ];
            }),
            'mobile_user' => $this->whenLoaded('mobileUser', function () {
                return [
                    'id' => $this->mobileUser?->id,
                    'name' => $this->mobileUser?->name,
                    'phone' => $this->mobileUser?->phone,
                ];
            }),
            'vehicle' => $this->whenLoaded('vehicle', function () {
                return [
                    'id' => $this->vehicle?->id,
                    'plate_number' => $this->vehicle?->plate_number,
                    'name' => $this->vehicle?->name,
                ];
            }),
            'driver' => $this->whenLoaded('driver', function () {
                return [
                    'id' => $this->driver?->id,
                    'name' => $this->driver?->name,
                    'phone' => $this->driver?->phone,
                ];
            }),
        ];
    }
}
