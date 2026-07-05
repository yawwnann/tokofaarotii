<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingZone extends Model
{
    protected $fillable = ['store_id', 'zone_level', 'rate'];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public static function levels(): array
    {
        return [
            'same_district'    => 'Dalam Kecamatan',
            'same_regency'     => 'Sekabupaten',
            'same_province'    => 'Seprovinsi',
            'same_island'      => 'Sepulau',
            'different_island' => 'Luar Pulau',
        ];
    }
}
