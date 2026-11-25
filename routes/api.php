<?php

use App\Http\Controllers\Api\MediaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('media', MediaController::class)
    ->parameters(['media' => 'media']);

Route::get('/users/{user}/media', [MediaController::class, 'userMedia']);
Route::post('/users/{user}/media/{media}', [MediaController::class, 'attachMedia']);
Route::delete('/users/{user}/media/{media}', [MediaController::class, 'detachMedia']);
