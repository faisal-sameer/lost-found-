<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClaimRequest;
use App\Models\LostItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClaimRequestController extends Controller
{
    // الحاج يقدم طلب استلام لغرض معيّن
    public function store(Request $request, $lostItemId)
    {
        $item = LostItem::findOrFail($lostItemId);

        // نقدر نمنع الطلب لو الغرض مسلّم أصلاً
        if ($item->status === 'delivered') {
            return response()->json([
                'message' => 'هذا الغرض تم تسليمه بالفعل.'
            ], 422);
        }

        $data = $request->validate([
            'claimant_name'       => 'nullable|string|max:255',
            'claimant_id_number'  => 'nullable|string|max:50',
            'claimant_phone'      => 'nullable|string|max:50',
        ]);

        $claimCode = strtoupper(Str::random(8)); // مثال: 9FHG3KLD

        $claim = ClaimRequest::create([
            'lost_item_id'        => $item->id,
            'claim_code'          => $claimCode,
            'claimant_name'       => $data['claimant_name'] ?? null,
            'claimant_id_number'  => $data['claimant_id_number'] ?? null,
            'claimant_phone'      => $data['claimant_phone'] ?? null,
        ]);

        return response()->json($claim, 201);
    }

    // (اختياري) عرض آخر الطلبات للأدمن
    public function index()
    {
        return ClaimRequest::with('lostItem.pickupPoint')
            ->latest()
            ->take(20)
            ->get();
    }
}
