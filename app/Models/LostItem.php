<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LostItem extends Model
{
    protected $fillable = [
        'pickup_point_id',
        'barcode',
        'item_type_id',
        'title',
        'description',
        'owner_id_number',
        'owner_phone',
        'status',
    ];

    public function pickupPoint()
    {
        return $this->belongsTo(PickupPoint::class);
    }
    public function claimRequests()
    {
        return $this->hasMany(ClaimRequest::class);
    }
}
