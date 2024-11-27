<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ProgramController;
use App\Http\Controllers\Api\DashboardController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::get('me', [AuthController::class, 'me'])->middleware('auth:api');

    Route::middleware('auth:api')->group(function () {
        Route::apiResource('reports', ReportController::class);
    });

    Route::middleware('admin')->group(function () {
        Route::get('admin/reports', [ReportController::class, 'verifyIndex']);
        Route::put('admin/reports/{id}/approve', [ReportController::class, 'approveReport']);
        Route::put('admin/reports/{id}/reject', [ReportController::class, 'rejectReport']);
        Route::get('admin/dashboard', [DashboardController::class, 'index']);
    });

    Route::apiResource('programs', ProgramController::class);
});
