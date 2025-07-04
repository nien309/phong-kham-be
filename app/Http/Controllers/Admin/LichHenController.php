<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LichHen;
use App\Models\TaiKhoan; // ✅ Đúng vị trí
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\LogService;

class LichHenController extends Controller
{
    public function index()
    {
        return LichHen::with([
            'khachhang.taikhoan:id_taikhoan,id_nguoidung,hoten',
            'nhanvien.taikhoan:id_taikhoan,id_nguoidung,hoten',
            'cakham:id_cakham,khunggio'
        ])
        ->orderBy('ngayhen', 'desc')
        ->get();
    }


    public function show($id)
    {
        return LichHen::with([
            'khachhang.taikhoan:id_taikhoan,id_nguoidung,hoten',
            'nhanvien.taikhoan:id_taikhoan,id_nguoidung,hoten',
            'cakham:id_cakham,khunggio'
        ])->findOrFail($id);
    }

    
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


    
    public function taoLich(Request $request)
    {
            $validated = $request->validate([
        'id_khachhang' => 'required|exists:khach_hangs,id_khachhang',
        'id_nhanvien' => 'required|exists:nhan_viens,id_nhanvien',
        'id_khoa' => 'required|exists:khoas,id_khoa',
        'id_cakham' => 'required|exists:cakham,id_cakham',
        'ngayhen' => 'required|date',
        'ghichu' => 'nullable|string',
    ]);


        $validated['trangthai'] = 'đã xác nhận';

        return LichHen::create($validated);
    }

   
    public function huyLich($id)
    {
        $lichhen = LichHen::findOrFail($id);
        $lichhen->update(['trangthai' => 'đã huỷ']);

        return response()->json(['message' => 'Đã huỷ lịch hẹn']);
    }

  
    public function capNhatTrangThai(Request $request, $id)
    {
        $lichhen = LichHen::findOrFail($id);

        $validated = $request->validate([
            'trangthai' => 'required|in:chờ xác nhận,đã xác nhận,chuyển đến bác sĩ,chuyển đến lễ tân,hoàn thành,đã huỷ',
        ]);

        $lichhen->update(['trangthai' => $validated['trangthai']]);

        return $lichhen;
    }
}