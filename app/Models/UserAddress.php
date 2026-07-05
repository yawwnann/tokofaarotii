<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAddress extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'label',
        'receiver_name',
        'phone',
        'province',
        'province_id',
        'city',
        'city_id',
        'district',
        'district_id',
        'village',
        'village_id',
        'postal_code',
        'address',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Alamat dimiliki oleh satu user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke data provinsi.
     */
    public function provinceData(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }

    /**
     * Relasi ke data kota/kabupaten.
     */
    public function cityData(): BelongsTo
    {
        return $this->belongsTo(Regency::class, 'city_id', 'id');
    }

    /**
     * Relasi ke data kecamatan.
     */
    public function districtData(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    /**
     * Relasi ke data desa/kelurahan.
     */
    public function villageData(): BelongsTo
    {
        return $this->belongsTo(Village::class, 'village_id', 'id');
    }
}
