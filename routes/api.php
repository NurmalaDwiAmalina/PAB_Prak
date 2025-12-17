<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Partners\TourPartnerController;
use Laravel\Passport\Http\Middleware\EnsureClientIsResourceOwner;
use App\Http\Controllers\Api\TourController;
use App\Http\Controllers\Api\TokenController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::prefix('partners')->group(function () {
    Route::middleware([EnsureClientIsResourceOwner::class])
        ->get('/tours', [TourPartnerController::class, 'index']);
    Route::middleware([EnsureClientIsResourceOwner::class])
        ->post('/tours', [TourPartnerController::class, 'store']);
    Route::post('/tours/book', [TourPartnerController::class, 'book']);
    Route::middleware('auth:api')->post(
        '/oauth/logout',
        [TokenController::class, 'revoke']
    );
    Route::middleware(['auth:api'])->get(
        '/tours',
        [TourController::class, 'index']
    );
    Route::middleware(['auth:api'])->get(
        '/tours/{id}',
        [TourController::class, 'show']
    );
    Route::middleware(['auth:api'])->post(
        '/tours/{id}',
        [TourController::class, 'attend']
    );
});

// M2M Product Routes
Route::middleware(\Laravel\Passport\Http\Middleware\CheckToken::class)->group(function () {
    Route::apiResource('products', \App\Http\Controllers\Api\ProductController::class);
});
