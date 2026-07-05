<?php

namespace App\Models;

use App\Models\UserAddress;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'store_id',
        'cod_rejection_count',
        'cod_blocked_until',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'cod_blocked_until' => 'datetime',
        ];
    }

    /**
     * Relasi ke profil pengguna.
     */
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Relasi ke alamat pengguna.
     */
    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    /**
     * Relasi ke toko tempat user (kasir) bekerja.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}