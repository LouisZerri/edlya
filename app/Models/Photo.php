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
        if (str_starts_with($this->chemin, '/uploads/')) {
            return $this->chemin;
        }

        return Storage::url($this->chemin);
    }
}