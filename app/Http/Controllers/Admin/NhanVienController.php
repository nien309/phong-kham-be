<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NhanVien;
use Illuminate\Http\Request;
use App\Services\LogService;

class NhanVienController extends Controller
{
    public function index()
    {
        return NhanVien::with('taikhoan')->get(); // Trả kèm thông tin tài khoản nếu có
    }

    public function store(Request $request)
{
    // Gán mặc định nếu không có
    $request->merge([
        'loai_taikhoan' => $request->input('loai_taikhoan', 'nhanvien'),
        'phan_quyen' => $request->input('phan_quyen', 'nhanvien'),
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
        'chucvu' => 'required|string',
        'luong' => 'required|numeric',
        'id_khoa' => 'required|exists:khoas,id_khoa'
    ]);

    // Tạo nhân viên trước
    $nhanvien = \App\Models\NhanVien::create([
        'chucvu' => $validated['chucvu'],
        'luong' => $validated['luong'],
        'id_khoa' => $validated['id_khoa'],
    ]);

    // Tạo tài khoản gắn với nhân viên
    $taikhoan = \App\Models\TaiKhoan::create([
        'hoten' => $validated['hoten'],
        'matkhau' => bcrypt($validated['matkhau']),
        'sdt' => $validated['sdt'],
        'email' => $validated['email'],
        'gioitinh' => $request->gioitinh ?? 'khac',
        'ngaysinh' => $request->ngaysinh ?? now(),
        'diachi' => $request->diachi ?? '',
        'loai_taikhoan' => $validated['loai_taikhoan'],
        'phan_quyen' => $validated['phan_quyen'],
        'id_nguoidung' => $nhanvien->id_nhanvien, // Khóa ngoại
    ]);

    LogService::log('Tạo nhân viên ID tài khoản: ' . $taikhoan->id_taikhoan, 'nhanviens');

    return response()->json([
        'message' => 'Tạo nhân viên thành công',
        'taikhoan' => $taikhoan,
        'nhanvien' => $nhanvien
    ], 201);
}


    public function show($id)
    {
        return NhanVien::with('taikhoan')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $nhanvien = NhanVien::findOrFail($id);

        $validated = $request->validate([
            'chucvu' => 'nullable|string',
            'luong' => 'nullable|numeric',
            'id_khoa' => 'nullable|exists:khoas,id_khoa'
        ]);

        $nhanvien->update($validated);

        LogService::log('Cập nhật nhân viên ID: ' . $id, 'nhanviens');

        return response()->json($nhanvien);
    }

    public function destroy($id)
    {
        $nhanvien = NhanVien::findOrFail($id);
        $nhanvien->delete();

        LogService::log('Xoá nhân viên ID: ' . $id, 'nhanviens');

        return response()->json(['message' => 'Đã xoá nhân viên']);
    }
}
