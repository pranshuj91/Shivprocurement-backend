<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcurementSetting extends Model
{
    protected $fillable = [
        'moisture_threshold',
        'fm_threshold',
        'dm_threshold',
    ];

    protected function casts(): array
    {
        return [
            'moisture_threshold' => 'float',
            'fm_threshold' => 'float',
            'dm_threshold' => 'float',
        ];
    }

    public static function current(): self
    {
        return static::firstOrCreate([], [
            'moisture_threshold' => 10.0,
            'fm_threshold' => 2.0,
            'dm_threshold' => 2.0,
        ]);
    }

    public function entryIsOutOfSpec(object $entry): bool
    {
        return (float) $entry->moisture > $this->moisture_threshold
            || (float) $entry->fm > $this->fm_threshold
            || (float) $entry->dm > $this->dm_threshold;
    }

    public function applyOutOfSpecScope($query): void
    {
        $query->where(function ($q) {
            $q->where('moisture', '>', $this->moisture_threshold)
                ->orWhere('fm', '>', $this->fm_threshold)
                ->orWhere('dm', '>', $this->dm_threshold);
        });
    }

    public function toThresholdArray(): array
    {
        return [
            'moisture' => $this->moisture_threshold,
            'fm' => $this->fm_threshold,
            'dm' => $this->dm_threshold,
        ];
    }
}
