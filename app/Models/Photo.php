<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    use HasFactory;

    protected $table = 'photo';

    const UPDATED_AT = null;

    protected $fillable = [
        'element_id',
        'chemin',
        'legende',
        'latitude',
        'longitude',
        'ordre',
    ];

    public function element(): BelongsTo
    {
        return $this->belongsTo(Element::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->chemin);
    }
}