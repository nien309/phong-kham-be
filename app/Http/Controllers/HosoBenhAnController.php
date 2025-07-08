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

    // 🧿 Tạo hồ sơ bệnh án (chỉ lễ tân)
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->chucvu !== 'letan') {
            return response()->json(['message' => 'Chỉ lễ tân được phép tạo hồ sơ'], 403);
        }

        $validated = $request->validate([
            'id_khachhang' => 'required|exists:khachhangs,id_khachhang',
        ]);

        $hoso = HosoBenhAn::create([
            'id_khachhang' => $validated['id_khachhang'],
        ]);

        LogService::log('Tạo hồ sơ bệnh án ID: ' . $hoso->id_hosobenhan, 'hosobenhan');

        return response()->json($hoso, 201);
    }

    // 🧿 Xem 1 hồ sơ (chỉ bác sĩ/khách hàng xem hồ sơ của mình)
    public function show($id)
    {
        $hoso = HosoBenhAn::with('benhans')->findOrFail($id);
        $user = Auth::user();

        if ($user->loai_taikhoan === 'khachhang') {
        $khachhang = \App\Models\KhachHang::where('id_taikhoan', $user->id_taikhoan)->first();
        if (!$khachhang || $hoso->id_khachhang !== $khachhang->id_khachhang) {
            return response()->json(['message' => 'Bạn không được phép xem hồ sơ này'], 403);
        }
        } elseif (!in_array($user->chucvu ?? null, ['bacsi', 'dieuduong'])) {
            return response()->json(['message' => 'Không có quyền truy cập'], 403);
        }

        return response()->json($hoso);
    }

    // 🧿 Cập nhật trạng thái (chỉ bác sĩ)
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->chucvu !== 'bacsi') {
            return response()->json(['message' => 'Chỉ bác sĩ được phép cập nhật'], 403);
        }

        $hoso = HosoBenhAn::findOrFail($id);
        $request->validate([
            'trangthai' => 'in:dang_dieu_tri,hoan_thanh,huy',
        ]);

        $hoso->update([
            'trangthai' => $request->trangthai,
        ]);

        LogService::log('Cập nhật hồ sơ bệnh án ID: ' . $id, 'hosobenhan');

        return response()->json($hoso);
    }

    // 🧿 Xoá mềm (chỉ bác sĩ)
    // public function destroy($id)
    // {
    //     $user = Auth::user();
    //     if ($user->chucvu !== 'bacsi') {
    //         return response()->json(['message' => 'Chỉ bác sĩ được xoá hồ sơ'], 403);
    //     }

    //     $hoso = HosoBenhAn::findOrFail($id);
    //     $hoso->delete();

    //     LogService::log('Xoá hồ sơ bệnh án ID: ' . $id, 'hosobenhan');

    //     return response()->json(['message' => 'Đã xoá hồ sơ bệnh án']);
    // }
}

