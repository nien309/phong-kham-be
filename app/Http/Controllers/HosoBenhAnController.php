<?php

namespace App\Http\Controllers;

use App\Models\HosoBenhAn;
use App\Models\Taikhoan;
use App\Models\KhachHang;


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

    $hoso = HosoBenhAn::with('benhans.khoa', 'khachhang', 'benhans.nhanvien.taikhoan')
                ->where('id_khachhang', $khachhang->id_khachhang)
                ->first();

    if (!$hoso) {
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
            'sdt' => 'required',
        ]);

        // Tìm tài khoản theo SĐT
        $taikhoan = \App\Models\TaiKhoan::where('sdt', $validated['sdt'])->first();

        if (!$taikhoan) {
            return response()->json([
                'message' => 'Không tìm thấy tài khoản. Vui lòng tạo tài khoản trước.'
            ], 404);
        }

        // Lấy khách hàng
        $khachhang = \App\Models\KhachHang::where('id_khachhang', $taikhoan->id_nguoidung)->first();

        if (!$khachhang) {
            return response()->json([
                'message' => 'Không tìm thấy thông tin khách hàng. Vui lòng kiểm tra lại.'
            ], 404);
        }

        // Kiểm tra hồ sơ đã tồn tại chưa
        $hosobenhan = \App\Models\HosoBenhAn::where('id_khachhang', $khachhang->id_khachhang)->first();

        if ($hosobenhan) {
            return response()->json([
                'message' => 'Khách hàng đã có hồ sơ bệnh án.',
                'data' => $hosobenhan
            ], 409);
        }

        // Tạo mới hồ sơ
        $hosobenhan = \App\Models\HosoBenhAn::create([
            'id_khachhang' => $khachhang->id_khachhang,
        ]);

        return response()->json([
            'message' => 'Tạo hồ sơ bệnh án thành công',
            'data' => $hosobenhan
        ], 201);
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

    $chucvu = $user->nhanvien->chucvu ?? null;
    if (!in_array($chucvu, ['bacsi', 'letan'])) {
        return response()->json(['message' => 'Không có quyền tìm kiếm hồ sơ'], 403);
    }

    $request->validate([
        'sdt' => 'required|string',
    ]);

    $khachhang = \App\Models\KhachHang::whereHas('taikhoan', function($query) use ($request) {
        $query->where('sdt', $request->sdt);
    })->first();

    if (!$khachhang) {
        return response()->json(['message' => 'Không tìm thấy khách hàng với số điện thoại này'], 404);
    }

    // Chú ý: with('khachhang.taikhoan')
    $hoso = HosoBenhAn::with(['benhans', 'khachhang.taikhoan',    'benhans.khoa',
    'benhans.nhanvien.taikhoan'
])
                ->where('id_khachhang', $khachhang->id_khachhang)
                ->get();

    if ($hoso->isEmpty()) {
        return response()->json(['message' => 'Khách hàng này chưa có hồ sơ bệnh án'], 404);
    }

    return response()->json($hoso[0]);
}


}

