<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SubnetController;
use App\Http\Controllers\UserTimeController;
use App\Http\Middleware\CheckPublicApiKey;
use App\Models\Subnet;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Support\Facades\Route;

Route::get('/auth/user', function (#[CurrentUser] User $user) {
    return $user;
})->middleware('auth:sanctum');

Route::any('/auth/{provider}/redirect', [AuthController::class, 'redirectToProvider']);
Route::any('/auth/{provider}/callback', [AuthController::class, 'handleProviderCallback']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

Route::middleware(CheckPublicApiKey::class)->group(function () {
    Route::post('/subnets/{subnet}/authenticate', [SubnetController::class, 'authenticate']);
    Route::post('/grade/subnet', [GradeController::class, 'store']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/role', [AuthController::class, 'role']);

    Route::post('/user/time/sync', [UserTimeController::class, 'syncTime']);

    Route::get('/subnets', [SubnetController::class, 'index'])->can('viewAny', Subnet::class);
    Route::get('/subnets/{subnet}', [SubnetController::class, 'show'])->can('view', 'subnet');
    Route::post('/subnets/{subnet}/join', [SubnetController::class, 'join']);

    Route::get('/notifications', [NotificationController::class, 'index']);

    Route::get('/assignments', [AssignmentController::class, 'index']);
});
