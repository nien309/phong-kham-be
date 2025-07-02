<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KhachHang;
use App\Models\TaiKhoan;
use Illuminate\Http\Request;
use App\Services\LogService;

class KhachHangController extends Controller
{
    public function index()
    {
        return KhachHang::with('taikhoan')->get(); // Trả kèm thông tin tài khoản
    }

    public function store(Request $request)
    {
        // Gán mặc định nếu không có
        $request->merge([
            'loai_taikhoan' => $request->input('loai_taikhoan', 'khachhang'),
            'phan_quyen' => $request->input('phan_quyen', 'khachhang'),
        ]);

        // Validate toàn bộ dữ liệu
        $validated = $request->validate([
            'hoten' => 'required|string',
            'matkhau' => 'required|string|min:6|confirmed',
            'sdt' => 'required|unique:taikhoan,sdt',
            'email' => 'required|email|unique:taikhoan,email',
            'gioitinh' => 'nullable|string',
            'ngaysinh' => 'nullable|date',
            'diachi' => 'nullable|string',
            'loai_taikhoan' => 'required|in:khachhang,nhanvien,admin',
            'phan_quyen' => 'required|in:admin_hethong,admin_nhansu,khachhang,nhanvien',
            'nghenghiep' => 'nullable|string'
        ]);

        // Tạo khách hàng trước
        $khachhang = KhachHang::create([
            'nghenghiep' => $validated['nghenghiep'] ?? '',
        ]);

        // Tạo tài khoản gắn với khách hàng
        $taikhoan = TaiKhoan::create([
            'hoten' => $validated['hoten'],
            'matkhau' => bcrypt($validated['matkhau']),
            'sdt' => $validated['sdt'],
            'email' => $validated['email'],
            'gioitinh' => $request->gioitinh ?? 'khac',
            'ngaysinh' => $request->ngaysinh ?? now(),
            'diachi' => $request->diachi ?? '',
            'loai_taikhoan' => $validated['loai_taikhoan'],
            'phan_quyen' => $validated['phan_quyen'],
            'id_nguoidung' => $khachhang->id_khachhang, // Khóa ngoại
        ]);

        LogService::log('Tạo khách hàng ID tài khoản: ' . $taikhoan->id_taikhoan, 'khachhangs');

        return response()->json([
            'message' => 'Tạo khách hàng thành công',
            'taikhoan' => $taikhoan,
            'khachhang' => $khachhang
        ], 201);
    }

    public function show($id)
    {
        return KhachHang::with('taikhoan')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $khachhang = KhachHang::findOrFail($id);
        $taikhoan = $khachhang->taikhoan;

        $validated = $request->validate([
            // Khách hàng
            'nghenghiep' => 'nullable|string',

            // Tài khoản
            'hoten' => 'nullable|string',
            'sdt' => 'nullable|string|unique:taikhoan,sdt,' . $taikhoan->id_taikhoan . ',id_taikhoan',
            'email' => 'nullable|email|unique:taikhoan,email,' . $taikhoan->id_taikhoan . ',id_taikhoan',
            'gioitinh' => 'nullable|string',
            'ngaysinh' => 'nullable|date',
            'diachi' => 'nullable|string',
            'phan_quyen' => 'nullable|in:admin_hethong,admin_nhansu,khachhang,nhanvien',
            'matkhau' => 'nullable|string|min:6|confirmed'
        ]);

        // Cập nhật bảng khách hàng
        $khachhang->update([
            'nghenghiep' => $validated['nghenghiep'] ?? $khachhang->nghenghiep,
        ]);

        // Cập nhật bảng tài khoản
        $taikhoan->update([
            'hoten' => $validated['hoten'] ?? $taikhoan->hoten,
            'sdt' => $validated['sdt'] ?? $taikhoan->sdt,
            'email' => $validated['email'] ?? $taikhoan->email,
            'gioitinh' => $validated['gioitinh'] ?? $taikhoan->gioitinh,
            'ngaysinh' => $validated['ngaysinh'] ?? $taikhoan->ngaysinh,
            'diachi' => $validated['diachi'] ?? $taikhoan->diachi,
            'phan_quyen' => $validated['phan_quyen'] ?? $taikhoan->phan_quyen,
            'matkhau' => isset($validated['matkhau']) 
                ? bcrypt($validated['matkhau']) 
                : $taikhoan->matkhau,
        ]);

        LogService::log('Cập nhật khách hàng ID: ' . $id, 'khachhangs');

        return response()->json([
            'message' => 'Cập nhật thành công',
            'khachhang' => $khachhang,
            'taikhoan' => $taikhoan,
        ]);
    }

    public function destroy($id)
    {
        $khachhang = KhachHang::findOrFail($id);
        $khachhang->delete();

        LogService::log('Xoá khách hàng ID: ' . $id, 'khachhangs');

        return response()->json(['message' => 'Đã xoá khách hàng']);
    }
}
