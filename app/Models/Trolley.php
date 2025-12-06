<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

class Trolley extends Model
{
    use HasFactory;

    public const TYPES = ['internal', 'external'];

    public const TYPE_LABELS = [
        'internal' => 'Internal',
        'external' => 'External',
    ];

    public const KINDS = ['reinforce', 'backplate', 'compbase'];

    public const KIND_LABELS = [
        'reinforce' => 'Reinforce',
        'backplate' => 'Backplate',
        'compbase' => 'CompBase',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'type',
        'kind',
        'status',
        'capacity',
        'location',
        'notes',
        'qr_code_path',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
        ];
    }

    public function movements(): HasMany
    {
        return $this->hasMany(TrolleyMovement::class);
    }

    public function latestMovement(): HasOne
    {
        return $this->hasOne(TrolleyMovement::class)->latestOfMany();
    }

    public function activeMovements(): HasMany
    {
        return $this->movements()->where('status', 'out');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPE_LABELS[$this->type] ?? ucfirst($this->type);
    }

    public function getKindLabelAttribute(): string
    {
        return self::KIND_LABELS[$this->kind] ?? ucfirst($this->kind);
    }

    public function getStatusSinceAttribute(): ?Carbon
    {
        $movement = $this->latestMovement;

        if (! $movement) {
            return null;
        }

        return $movement->status === 'out'
            ? ($movement->checked_out_at ?? $movement->created_at)
            : ($movement->checked_in_at ?? $movement->created_at);
    }

    public function getLastMovementAtAttribute(): ?Carbon
    {
        $movement = $this->latestMovement;

        if (! $movement) {
            return null;
        }

        return $movement->status === 'out'
            ? ($movement->checked_out_at ?? $movement->created_at)
            : ($movement->checked_in_at ?? $movement->checked_out_at ?? $movement->created_at);
    }

    public function getLastMovementStatusAttribute(): ?string
    {
        return $this->latestMovement?->status;
    }

    public function getStatusDurationLabelAttribute(): ?string
    {
        $since = $this->status_since;

        if (! $since) {
            return null;
        }

        $diff = $since->diffInSeconds(now());

        $days = intdiv($diff, 86400);

        // Jika lebih dari atau sama dengan 24 jam, tampilkan hanya dalam hari (D)
        if ($days > 0) {
            return $days . ' D';
        }

        // Jika kurang dari 24 jam, tampilkan "0 D"
        return '0 D';
    }
}
