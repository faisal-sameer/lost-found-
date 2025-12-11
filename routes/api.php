<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PickupPointController;
use App\Http\Controllers\Api\LostItemController;
use App\Http\Controllers\Api\ItemTypeController;
use App\Http\Controllers\Api\ClaimRequestController;

Route::get('/pickup-points', [PickupPointController::class, 'index']);
Route::post('/pickup-points', [PickupPointController::class, 'store']);

Route::get('/lost-items', [LostItemController::class, 'index']);
Route::post('/lost-items', [LostItemController::class, 'store']);

Route::get('/lost-items/search', [LostItemController::class, 'search']);
Route::post('/lost-items/{id}/delivered', [LostItemController::class, 'markDelivered']);
Route::get('/item-types', [ItemTypeController::class, 'index']);
Route::post('/lost-items/{lostItem}/claim', [ClaimRequestController::class, 'store']);
Route::get('/claim-requests', [ClaimRequestController::class, 'index']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
