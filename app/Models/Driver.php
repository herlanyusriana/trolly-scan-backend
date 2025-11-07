<?php

namespace App\Models;

use App\Models\TrolleyMovement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    use HasFactory;

    public const STATUSES = ['active', 'inactive'];

    public const STATUS_LABELS = [
        'active' => 'Active',
        'inactive' => 'Inactive',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'phone',
        'license_number',
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
