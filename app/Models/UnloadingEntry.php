<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnloadingEntry extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'unit_id',
        'truck_no',
        'purchase_type',
        'sourced_from',
        'moisture',
        'fm',
        'dm',
        'status',
        'latitude',
        'longitude',
        'gps_accuracy',
        'gross_weight',
        'tare_weight',
        'net_weight',
        'remarks',
        'operator_name',
        'lab_name',
        'lab_test_status',
        'lab_moisture',
        'lab_fm',
        'lab_dm',
        'lab_recorded_at',
        'lab_recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'lab_recorded_at' => 'datetime',
        ];
    }

    public function hasLabTest(): bool
    {
        return $this->lab_name !== null && $this->lab_test_status !== null;
    }

    public function labRecordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lab_recorded_by');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function mediaLogs(): HasMany
    {
        return $this->hasMany(MediaLog::class);
    }
}
