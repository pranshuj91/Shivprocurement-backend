<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'unloading_entry_id',
        'type',
        'file_path',
        'caption',
    ];

    public function unloadingEntry(): BelongsTo
    {
        return $this->belongsTo(UnloadingEntry::class);
    }
}
