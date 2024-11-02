<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SubnetController;
use App\Models\Subnet;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Support\Facades\Route;

Route::get('/auth/user', function (#[CurrentUser] User $user) {
    return $user;
})->middleware('auth:sanctum');

Route::get('/auth/{provider}/redirect', [AuthController::class, 'redirectToProvider']);
Route::get('/auth/{provider}/callback', [AuthController::class, 'handleProviderCallback']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/role', [AuthController::class, 'role']);

    Route::get('/subnets', [SubnetController::class, 'index'])->can('viewAny', Subnet::class);    
    Route::get('/subnets/{subnet}', [SubnetController::class, 'show'])->can('view', 'subnet');
    Route::post('/subnets/{subnet}/join', [SubnetController::class, 'join']);
});
