<?php

namespace App\Http\Controllers;
use App\Services\LogService;

use Illuminate\Http\Request;

class AdminTaiKhoanController extends Controller
{
    public function createFromAdmin(Request $request)
{
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
    ]);

    if ($validated['loai_taikhoan'] === 'khachhang') {
        $nguoidung = \App\Models\KhachHang::create([
            'nghenghiep' => $request->nghenghiep ?? '',
        ]);
    } else {
        $nguoidung = \App\Models\NhanVien::create([
            'chucvu' => $request->chucvu ?? 'letan',
            'luong' => $request->luong ?? 0,
        ]);
    }

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
        'id_nguoidung' => $nguoidung->getKey(),
    ]);
      LogService::log('Tạo tài khoản mới: ' . $taikhoan->hoten, 'taikhoan');
    return response()->json([
        'message' => 'Tạo tài khoản thành công',
        'taikhoan' => $taikhoan,
    ]);
}
    public function index()
    {
        $taikhoans = \App\Models\TaiKhoan::all();

        $taikhoans = $taikhoans->map(function ($tk) {
            $tk->nguoidung = $tk->nguoidung(); // Gọi thủ công
            return $tk;
        });

        return response()->json($taikhoans);
    }

    public function show($id)
    {
        $taikhoan = \App\Models\TaiKhoan::find($id);

        if (!$taikhoan) {
            return response()->json(['message' => 'Không tìm thấy tài khoản'], 404);
        }

        $taikhoan->nguoidung = $taikhoan->nguoidung(); // Gọi quan hệ thủ công

        return response()->json($taikhoan);
    }

    public function update(Request $request, $id)
    {
        $taikhoan = \App\Models\TaiKhoan::find($id);
        if (!$taikhoan) {
            return response()->json(['message' => 'Không tìm thấy tài khoản'], 404);
        }

        $taikhoan->update($request->only([
            'hoten', 'email', 'sdt', 'diachi', 'gioitinh', 'ngaysinh', 'trangthai', 'phan_quyen'
        ]));

        // Cập nhật bảng phụ
        // if ($taikhoan->loai_taikhoan === 'khachhang') {
        //     \App\Models\KhachHang::where('id_khachhang', $taikhoan->id_nguoidung)->update([
        //         'nghenghiep' => $request->nghenghiep,
        //     ]);
        // } elseif ($taikhoan->loai_taikhoan === 'nhanvien') {
        //     \App\Models\NhanVien::where('id_nhanvien', $taikhoan->id_nguoidung)->update([
        //         'chucvu' => $request->chucvu,
        //         'luong' => $request->luong,
        //     ]);
        // }

        return response()->json(['message' => 'Cập nhật thành công']);
    }

public function destroy($id)
{
    $taikhoan = \App\Models\TaiKhoan::find($id);
    if (!$taikhoan) {
        return response()->json(['message' => 'Không tìm thấy tài khoản'], 404);
    }

    // Xoá bảng phụ nếu muốn
    if ($taikhoan->loai_taikhoan === 'khachhang') {
        \App\Models\KhachHang::where('id_khachhang', $taikhoan->id_nguoidung)->delete();
    } elseif ($taikhoan->loai_taikhoan === 'nhanvien') {
        \App\Models\NhanVien::where('id_nhanvien', $taikhoan->id_nguoidung)->delete();
    }

    $taikhoan->delete();

    return response()->json(['message' => 'Đã xoá tài khoản']);
}


}
