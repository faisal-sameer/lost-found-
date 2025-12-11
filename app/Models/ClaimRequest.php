<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClaimRequest extends Model
{
    protected $fillable = [
        'lost_item_id',
        'claim_code',
        'claimant_name',
        'claimant_id_number',
        'claimant_phone',
        'status',
    ];

    public function lostItem()
    {
        return $this->belongsTo(LostItem::class);
    }
}
