<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\AdminTaiKhoanController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json($request->user());
});


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/update', [AuthController::class, 'update']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('admin')->group(function () {
        Route::get('/taikhoan', [AdminTaiKhoanController::class, 'index']);
        Route::get('/taikhoan/{id}', [AdminTaiKhoanController::class, 'show']);
        Route::post('/taikhoan', [AdminTaiKhoanController::class, 'createFromAdmin']);
        Route::put('/taikhoan/{id}', [AdminTaiKhoanController::class, 'update']);
        Route::delete('/taikhoan/{id}', [AdminTaiKhoanController::class, 'destroy']);
    });
});

