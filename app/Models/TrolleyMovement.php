<?php

namespace App\Models;

use App\Models\Admin;
use App\Models\Driver;
use App\Models\MobileUser;
use App\Models\Trolley;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrolleyMovement extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'trolley_id',
        'mobile_user_id',
        'checked_out_by_admin_id',
        'checked_in_by_admin_id',
        'status',
        'sequence_number',
        'checked_out_at',
        'expected_return_at',
        'checked_in_at',
        'destination',
        'return_location',
        'notes',
        'vehicle_id',
        'driver_id',
        'vehicle_snapshot',
        'driver_snapshot',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'checked_out_at' => 'datetime',
            'expected_return_at' => 'datetime',
            'checked_in_at' => 'datetime',
        ];
    }

    public function trolley(): BelongsTo
    {
        return $this->belongsTo(Trolley::class);
    }

    public function mobileUser(): BelongsTo
    {
        return $this->belongsTo(MobileUser::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function checkedOutBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'checked_out_by_admin_id');
    }

    public function checkedInBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'checked_in_by_admin_id');
    }
}
