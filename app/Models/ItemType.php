<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemType extends Model
{
    protected $fillable = ['name', 'code'];

    public function lostItems()
    {
        return $this->hasMany(LostItem::class);
    }
}
