<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Middleware\SetLocale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/test', function () {
    return response()->json(['message' => 'Test successful']);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware([SetLocale::class])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('/posts', PostController::class)->middleware('auth:sanctum');
    Route::get('/tags', [PostController::class, 'tags'])->middleware('auth:sanctum');
});
