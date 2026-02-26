<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'user';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'telephone',
        'entreprise',
        'roles',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'roles' => 'array',
        ];
    }

    public function logements()
    {
        return $this->hasMany(Logement::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
