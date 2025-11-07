<?php

namespace App\Models;

use App\Models\TrolleyMovement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read string $plate_number
 */

class Vehicle extends Model
{
    use HasFactory;

    public const STATUSES = ['available', 'maintenance', 'inactive'];

    public const STATUS_LABELS = [
        'available' => 'Available',
        'maintenance' => 'Maintenance',
        'inactive' => 'Inactive',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'plate_number',
        'name',
        'category',
        'status',
        'notes',
    ];

    public function movements(): HasMany
    {
        return $this->hasMany(TrolleyMovement::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst($this->status);
    }
}
