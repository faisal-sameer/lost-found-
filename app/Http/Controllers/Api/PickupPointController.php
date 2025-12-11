<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PickupPoint;
use Illuminate\Http\Request;

class PickupPointController extends Controller
{
    public function index()
    {
        return PickupPoint::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'city'    => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        $point = PickupPoint::create($data);

        return response()->json($point, 201);
    }
}
