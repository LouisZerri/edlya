<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = [
        'element_id',
        'chemin',
        'legende',
        'latitude',
        'longitude',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($photo) {
            if ($photo->chemin) {
                Storage::disk('public')->delete($photo->chemin);
            }
        });
    }

    public function element(): BelongsTo
    {
        return $this->belongsTo(Element::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->chemin);
    }
}