<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\AdminTaiKhoanController;
use App\Http\Controllers\HosoBenhAnController;
use App\Http\Controllers\BenhAnController;
use App\Http\Controllers\ThongtinkhambenhController;
use App\Http\Controllers\ChiDinhController;
use App\Http\Controllers\ToaThuocController;
use App\Http\Controllers\HoaDonController;


use App\Http\Controllers\Admin\{
    CaKhamController, LichHenController, NhanVienController, KhachHangController,LichDangKyLamViecController,LichLamViecController, KhoaController as AdminKhoaController, DichVuController as AdminDichVuController
};
use App\Http\Controllers\KhoaController;

// ✨ PUBLIC ROUTES (không cần đăng nhập)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

Route::get('/khoas', [KhoaController::class, 'index']);
Route::get('/khoas/{id}', [KhoaController::class, 'show']);
Route::get('/cakham', [CaKhamController::class, 'index']);
Route::get('/nhanviens/khoas/{id_khoa}', [NhanVienController::class, 'getByKhoa']);
Route::get('khoa/{id_khoa}/bac-si-lich-ranh', [LichHenController::class, 'layBacSiTheoLichRanh']);


// ✨ AUTHENTICATED ROUTES
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/taikhoan/me', [AuthController::class, 'getUserInfo']);
    Route::get('/admin/taikhoan/{id}', [AdminTaiKhoanController::class, 'show']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/update', [AuthController::class, 'update']);
    Route::get('/user', fn(Request $request) => $request->user());
    Route::apiResource('lich-dang-ky', LichDangKyLamViecController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
    Route::get('/hoso-benh-an-cua-toi', [HosoBenhAnController::class, 'hosoBenhAnCuaToi']);
    Route::get('/benh-an-cua-toi', [BenhanController::class, 'benhanCuaToi']);
    Route::get('/thong-tin-kham-benh/cua-toi', [ThongTinKhamBenhController::class, 'thongTinKhamBenhCuaToi']);
    Route::get('/chidinh/cua-toi', [ChiDinhController::class, 'chidinhCuaToi']);

    Route::apiResource('hosobenhan', HosoBenhAnController::class);
    Route::apiResource('benhan', BenhanController::class)->except(['destroy']);
    Route::apiResource('thongtinkhambenh', ThongTinKhamBenhController::class);
    Route::apiResource('chidinh', ChiDinhController::class)->only(['index', 'store', 'show', 'update']);

    // Route::post('chidinh', [ChiDinhController::class, 'store']);
    // Route::put('chidinh/{id}', [ChiDinhController::class, 'update']);
    // Route::get('chidinh/{id}', [ChiDinhController::class, 'show']);
    Route::get('/lich-hen-cua-toi', [LichHenController::class, 'lichHenCuaToi']);

    Route::post('toathuoc', [ToaThuocController::class, 'store']);
    Route::put('toathuoc/{id}', [ToaThuocController::class, 'update']);
    Route::get('toathuoc/{id}', [ToaThuocController::class, 'show']);

    Route::post('hoadon', [HoaDonController::class, 'store']);
    Route::put('hoadon/{id}', [HoaDonController::class, 'update']);
    Route::get('hoadon/{id}', [HoaDonController::class, 'show']);
    Route::post('/hosobenhan/search-by-phone', [HosoBenhAnController::class, 'searchByPhone']);



    // 🟩 KHÁCH HÀNG ĐẶT LỊCH
   Route::post('/lichhen', [LichHenController::class, 'datLich']);


    // 🟥 ADMIN DASHBOARD
    Route::middleware('check.admin')->prefix('admin')->group(function () {
        Route::get('/taikhoan', [AdminTaiKhoanController::class, 'index']);
        Route::post('/taikhoan', [AdminTaiKhoanController::class, 'createFromAdmin']);
        Route::put('/taikhoan/{id}', [AdminTaiKhoanController::class, 'update']);
        Route::delete('/taikhoan/{id}', [AdminTaiKhoanController::class, 'destroy']);
        Route::delete('/lich-dang-ky-lam-viec', [LichDangKyLamViecController::class, 'destroyAll']);

        Route::apiResource('khoas', AdminKhoaController::class);
        Route::apiResource('dichvus', AdminDichVuController::class);
        Route::apiResource('cakham', CaKhamController::class)->only(['index', 'store', 'update', 'destroy', 'show']);
        Route::apiResource('lichhen', LichHenController::class)->only(['index', 'update', 'destroy', 'show']);
        Route::post('lichhen/tao-lich', [LichHenController::class, 'taoLich']);
        Route::patch('lichhen/huy-lich/{id}', [LichHenController::class, 'huyLich']);
        Route::patch('lichhen/capnhat-trangthai/{id}', [LichHenController::class, 'capNhatTrangThai']);
        Route::apiResource('nhanviens', NhanVienController::class)->only(['index', 'store', 'update', 'destroy', 'show']);
        Route::apiResource('khachhangs', KhachHangController::class)->only(['index', 'store', 'update', 'destroy', 'show']);
        Route::get('/lich-lam-viec', [LichLamViecController::class, 'index']);
       
        Route::post('/nhanviens/search', [NhanVienController::class, 'search']);
     
        Route::post('/khachhangs/search', [KhachHangController::class, 'search']);

        Route::get('/lich-lam-viec/tim', [LichLamViecController::class, 'tim']);

    });

    // Dành cho khách (nếu sau này có)
    Route::middleware('check.user')->prefix('user')->group(function () {
        // Ví dụ: Route::get('/lichhen/cuatoi', ...)
    });
});































// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\AuthController;
// use App\Http\Controllers\PasswordResetController;
// use App\Http\Controllers\AdminTaiKhoanController;
// use App\Http\Controllers\KhoaController;
// use App\Http\Controllers\DichVuController;
// use App\Http\Controllers\Admin\CaKhamController;
// use App\Http\Controllers\Admin\LichHenController;
// use App\Http\Controllers\Admin\NhanVienController;
// use App\Http\Controllers\Admin\KhachHangController;


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return response()->json($request->user());
// });


// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login', [AuthController::class, 'login']);
// Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
// Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

// Route::middleware('auth:sanctum')->group(function () {
//     Route::post('/logout', [AuthController::class, 'logout']);
//     Route::put('/update', [AuthController::class, 'update']);
    
//     Route::get('/user', function (Request $request) {
//         return $request->user();
//     });
    
//     Route::get('/admin/taikhoan/{id}', [AdminTaiKhoanController::class, 'show']);
//     Route::middleware(['auth:sanctum', 'check.admin'])->prefix('admin')->group(function () {
//         Route::get('/taikhoan', [AdminTaiKhoanController::class, 'index']);
//         Route::post('/taikhoan', [AdminTaiKhoanController::class, 'createFromAdmin']);
//         Route::put('/taikhoan/{id}', [AdminTaiKhoanController::class, 'update']);
//         Route::delete('/taikhoan/{id}', [AdminTaiKhoanController::class, 'destroy']);
//         Route::apiResource('khoas', \App\Http\Controllers\Admin\KhoaController::class);
//         Route::apiResource('dichvus', \App\Http\Controllers\Admin\DichVuController::class);
//         Route::apiResource('cakham', CaKhamController::class)->only([
//             'index', 'store', 'update', 'destroy', 'show'
//         ]);
//         Route::apiResource('lichhen', LichHenController::class)->only([
//             'index', 'update', 'destroy', 'show'
//         ]);
//         Route::post('lichhen/tao-lich', [LichHenController::class, 'taoLich']);
//         Route::patch('lichhen/huy-lich/{id}', [LichHenController::class, 'huyLich']);
//         Route::patch('lichhen/capnhat-trangthai/{id}', [LichHenController::class, 'capNhatTrangThai']);
//         Route::apiResource('nhanviens', NhanVienController::class)->only([
//             'index', 'store', 'update', 'destroy', 'show'
//         ]);
//         Route::apiResource('khachhangs', KhachHangController::class)->only([
//             'index', 'store', 'update', 'destroy', 'show'
//         ]);
//     });
//     Route::middleware(['auth:sanctum', 'check.user'])->prefix('admin')->group(function () {
    
//     });
//     Route::middleware('auth:sanctum')->group(function () {
//         Route::post('/lichhen', [LichHenController::class, 'store']);
//     });

//     Route::get('/khoas', [KhoaController::class, 'index']);
//     Route::get('/khoas/{id}', [KhoaController::class, 'show']);
//     Route::get('/cakham', [CaKhamController::class, 'index']);
//     Route::get('/nhanviens/khoa/{id_khoa}', [NhanVienController::class, 'getByKhoa']);

     

// });

