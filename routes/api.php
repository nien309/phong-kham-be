<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\AdminTaiKhoanController;
use App\Http\Controllers\KhoaController;
use App\Http\Controllers\DichVuController;
use App\Http\Controllers\Admin\CaKhamController;
use App\Http\Controllers\Admin\LichHenController;
use App\Http\Controllers\Admin\NhanVienController;
use App\Http\Controllers\Admin\KhachHangController;


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
    
    Route::get('/admin/taikhoan/{id}', [AdminTaiKhoanController::class, 'show']);
    Route::middleware(['auth:sanctum', 'check.admin'])->prefix('admin')->group(function () {
        Route::get('/taikhoan', [AdminTaiKhoanController::class, 'index']);
        Route::post('/taikhoan', [AdminTaiKhoanController::class, 'createFromAdmin']);
        Route::put('/taikhoan/{id}', [AdminTaiKhoanController::class, 'update']);
        Route::delete('/taikhoan/{id}', [AdminTaiKhoanController::class, 'destroy']);
        Route::apiResource('khoas', \App\Http\Controllers\Admin\KhoaController::class);
        Route::apiResource('dichvus', \App\Http\Controllers\Admin\DichVuController::class);
        Route::apiResource('cakham', CaKhamController::class)->only([
            'index', 'store', 'update', 'destroy', 'show'
        ]);
        Route::apiResource('lichhen', LichHenController::class)->only([
            'index', 'store', 'update', 'destroy', 'show'
        ]);
        Route::post('lichhen/tao-lich', [LichHenController::class, 'taoLich']);
        Route::put('lichhen/huy-lich/{id}', [LichHenController::class, 'huyLich']);
        Route::put('lichhen/capnhat-trangthai/{id}', [LichHenController::class, 'capNhatTrangThai']);
        Route::apiResource('nhanviens', NhanVienController::class)->only([
            'index', 'store', 'update', 'destroy', 'show'
        ]);
        Route::apiResource('khachhangs', KhachHangController::class)->only([
            'index', 'store', 'update', 'destroy', 'show'
        ]);
    });
    Route::middleware(['auth:sanctum', 'check.user'])->prefix('admin')->group(function () {
        // Route::get('/taikhoan/{id}', [AdminTaiKhoanController::class, 'show']);
    });
    Route::get('/khoas', [KhoaController::class, 'index']);
    Route::get('/khoas/{id}', [KhoaController::class, 'show']);
// Route::post('/lichhen/dat-lich', [LichHenController::class, 'datLich']);
// Route::middleware(['auth:sanctum', 'check.admin'])->prefix('admin')->group(function () {

//     Route::apiResource('cakham', CaKhamController::class)->only(['index', 'store', 'update', 'destroy', 'show']);
//     Route::apiResource('lichhen', LichHenController::class)->only(['index', 'store', 'update', 'destroy', 'show']);
//     Route::post('/lichhen/tao-lich', [LichHenController::class, 'taoLich']);
//     Route::put('/lichhen/huy-lich/{id}', [LichHenController::class, 'huyLich']);
//     Route::put('/lichhen/capnhat-trangthai/{id}', [LichHenController::class, 'capNhatTrangThai']);
// });

    Route::post('/lichhen/dat-lich', [LichHenController::class, 'datLich']);

    Route::get('/nhanviens/khoa/{id_khoa}', [NhanVienController::class, 'getByKhoa']);

     

});

