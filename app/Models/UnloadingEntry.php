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
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function mediaLogs(): HasMany
    {
        return $this->hasMany(MediaLog::class);
    }
}
