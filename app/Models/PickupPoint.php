<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PickupPoint extends Model
{
    protected $fillable = ['name', 'city', 'address', 'map_url'];

    public function lostItems()
    {
        return $this->hasMany(LostItem::class);
    }
}
