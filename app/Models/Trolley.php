<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}
