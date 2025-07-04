<?php

namespace App\Http\Controllers;

use App\Models\LichHen;
use Illuminate\Http\Request;

class LichHenController extends Controller
{
    
    public function datLich(Request $request)
{
    /** @var TaiKhoan $user */
    $user = auth()->user(); // đăng nhập trả về từ bảng `taikhoan`

    // Lấy id_khachhang từ quan hệ
    $khachhang = $user->nguoidung;
    if (!$khachhang || !$user->loai_taikhoan === 'khachhang') {
        return response()->json(['error' => 'Tài khoản không phải khách hàng hoặc chưa liên kết khách hàng.'], 400);
    }

    $validated = $request->validate([
        'id_nhanvien' => 'required|exists:nhan_viens,id_nhanvien',
        'id_khoa' => 'required|exists:khoas,id_khoa',
        'id_cakham' => 'required|exists:cakham,id_cakham',
        'ngayhen' => 'required|date',
        'ghichu' => 'nullable|string',
    ]);

    $validated['id_khachhang'] = $khachhang->id_khachhang;
    $validated['trangthai'] = 'chờ xác nhận';

    return LichHen::create($validated);
}


    

}