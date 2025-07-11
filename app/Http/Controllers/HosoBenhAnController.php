<?php

namespace App\Http\Controllers;

use App\Models\HosoBenhAn;
use Illuminate\Http\Request;
use App\Services\LogService;
use Illuminate\Support\Facades\Auth;

class HosoBenhAnController extends Controller
{
    // 🧿 Xem tất cả hồ sơ (chỉ bác sĩ hoặc lễ tân)
    public function index()
    {
        $user = Auth::user();
        if (!in_array($user->chucvu, ['bacsi', 'letan'])) {
            return response()->json(['message' => 'Không có quyền truy cập'], 403);
        }

        return HosoBenhAn::with('khachhang')->get();
    }
    public function hosoBenhAnCuaToi()
{
    $user = Auth::user();
    if ($user->loai_taikhoan !== 'khachhang') {
        return response()->json(['message' => 'Không có quyền truy cập'], 403);
    }

    $khachhang = \App\Models\KhachHang::find($user->id_nguoidung);
    if (!$khachhang) {
        return response()->json(['message' => 'Không tìm thấy thông tin khách hàng'], 404);
    }

    $hoso = HosoBenhAn::with('benhans', 'khachhang')
                ->where('id_khachhang', $khachhang->id_khachhang)
                ->get();

    if ($hoso->isEmpty()) {
        return response()->json(['message' => 'Bạn chưa có hồ sơ bệnh án'], 404);
    }

    return response()->json($hoso);
}


    // 🧿 Tạo hồ sơ bệnh án (chỉ lễ tân)
    public function store(Request $request)
    {
        $user = Auth::user()->load('nhanvien');
        if ($user->nhanvien->chucvu !== 'letan') {
            return response()->json(['message' => 'Chỉ lễ tân được phép tạo hồ sơ'], 403);
        }

        $validated = $request->validate([
            'id_khachhang' => 'required',
        ]);

        $hoso = HosoBenhAn::create([
            'id_khachhang' => $validated['id_khachhang'],
        ]);

        // LogService::log('Tạo hồ sơ bệnh án ID: ' . $hoso->id_hosobenhan, 'hosobenhan');

        return response()->json($hoso, 201);
    }

    // 🧿 Xem 1 hồ sơ (chỉ bác sĩ/khách hàng xem hồ sơ của mình)
    public function show($id)
    {
        $user = Auth::user()->load('nhanvien');
        $hoso = HosoBenhAn::with('benhans')->findOrFail($id);

        // 👉 Nếu là khách hàng thì kiểm tra quyền sở hữu hồ sơ
        if ($user->loai_taikhoan === 'khachhang') {
        $khachhang = \App\Models\KhachHang::find($user->id_nguoidung);

        if (!$khachhang || $hoso->id_khachhang !== $khachhang->id_khachhang) {
            return response()->json(['message' => 'Bạn không được phép xem hồ sơ này'], 403);
        }

        return response()->json($hoso);
    }


        // 👉 Nếu là nhân viên (bacsi, dieuduong)
        $chucvu = $user->nhanvien->chucvu ?? null;

        if (!in_array($chucvu, ['bacsi', 'dieuduong'])) {
            return response()->json(['message' => 'Không có quyền truy cập'], 403);
        }

        return response()->json($hoso);
    }


// File: app/Http/Controllers/HosoBenhAnController.php

public function searchByPhone(Request $request)
{
    $user = Auth::user()->load('nhanvien');

    // 👉 Chỉ cho bác sĩ hoặc lễ tân tìm kiếm
    $chucvu = $user->nhanvien->chucvu ?? null;
    if (!in_array($chucvu, ['bacsi', 'letan'])) {
        return response()->json(['message' => 'Không có quyền tìm kiếm hồ sơ'], 403);
    }

    $request->validate([
        'sdt' => 'required|string',
    ]);

    // Vì sdt nằm ở bảng taikhoans nên phải whereHas
    $khachhang = \App\Models\KhachHang::whereHas('taikhoan', function($query) use ($request) {
        $query->where('sdt', $request->sdt);
    })->with('taikhoan')->first();

    if (!$khachhang) {
        return response()->json(['message' => 'Không tìm thấy khách hàng với số điện thoại này'], 404);
    }

    // Tìm tất cả hồ sơ bệnh án của khách hàng này
    $hoso = HosoBenhAn::with('benhans', 'khachhang')
                ->where('id_khachhang', $khachhang->id_khachhang)
                ->get();

    if ($hoso->isEmpty()) {
        return response()->json(['message' => 'Khách hàng này chưa có hồ sơ bệnh án'], 404);
    }

    return response()->json($hoso);
}

}

