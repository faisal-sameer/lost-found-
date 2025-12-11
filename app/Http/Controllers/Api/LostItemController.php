<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LostItem;
use Illuminate\Http\Request;

class LostItemController extends Controller
{
    public function index()
    {
        return LostItem::with('pickupPoint')->latest()->get();
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'pickup_point_id' => 'required|exists:pickup_points,id',
            'barcode'         => 'nullable|string|max:100',
            'item_type_id'    => 'nullable|exists:item_types,id',

            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string',
            'owner_id_number' => 'nullable|string|max:50',
            'owner_phone'     => 'nullable|string|max:50',
        ]);

        $item = LostItem::create($data);

        return response()->json($item->load('pickupPoint'), 201);
    }


    public function search(Request $request)
    {
        $request->validate([
            'owner_id_number' => 'nullable|string',
            'owner_phone'     => 'nullable|string',
            'barcode'         => 'nullable|string',
        ]);

        $query = LostItem::with('pickupPoint')
            ->where('status', 'received');

        if ($request->filled('barcode')) {
            $query->where('barcode', $request->barcode);
        }

        if ($request->filled('owner_id_number')) {
            $query->where('owner_id_number', $request->owner_id_number);
        }

        if ($request->filled('owner_phone')) {
            $query->where('owner_phone', $request->owner_phone);
        }

        return $query->latest()->get();
    }


    public function markDelivered($id)
    {
        $item = LostItem::findOrFail($id);
        $item->status = 'delivered';
        $item->save();

        return $item->load('pickupPoint');
    }
}
