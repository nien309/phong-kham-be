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
    return NhanVien::with('taikhoan')
        ->orderBy('id_nhanvien', 'asc')
        ->get();
}
  public function store(Request $request)
{
    // Lấy dữ liệu từ mảng lồng nhau `taikhoan`
    $taikhoanData = $request->input('taikhoan');

    // Gán mặc định nếu thiếu
    $taikhoanData['loai_taikhoan'] = $taikhoanData['loai_taikhoan'] ?? 'nhanvien';
    $taikhoanData['phan_quyen'] = $taikhoanData['phan_quyen'] ?? 'nhanvien';

    // Validate dữ liệu
    $validatedNhanVien = $request->validate([
        'chucvu' => 'required|string',
        'luong' => 'required|numeric',
        'id_khoa' => 'required|exists:khoas,id_khoa'
    ]);

    $validatedTaiKhoan = validator($taikhoanData, [
        'hoten' => 'required|string',
        'matkhau' => 'required|string|min:6|confirmed',
        'sdt' => 'required|unique:taikhoan,sdt',
        'email' => 'required|email|unique:taikhoan,email',
        'gioitinh' => 'nullable|string',
        'ngaysinh' => 'nullable|date',
        'diachi' => 'nullable|string',
        'loai_taikhoan' => 'required|in:khachhang,nhanvien,admin',
        'phan_quyen' => 'required|in:admin_hethong,admin_nhansu,khachhang,nhanvien',
    ])->validate();

    // Tạo nhân viên
    $nhanvien = \App\Models\NhanVien::create([
        'chucvu' => $validatedNhanVien['chucvu'],
        'luong' => $validatedNhanVien['luong'],
        'id_khoa' => $validatedNhanVien['id_khoa'],
    ]);

    // Tạo tài khoản gắn với nhân viên
    $taikhoan = \App\Models\TaiKhoan::create([
        'hoten' => $validatedTaiKhoan['hoten'],
        'matkhau' => bcrypt($validatedTaiKhoan['matkhau']),
        'sdt' => $validatedTaiKhoan['sdt'],
        'email' => $validatedTaiKhoan['email'],
        'gioitinh' => $taikhoanData['gioitinh'] ?? 'khac',
        'ngaysinh' => $taikhoanData['ngaysinh'] ?? now(),
        'diachi' => $taikhoanData['diachi'] ?? '',
        'loai_taikhoan' => $validatedTaiKhoan['loai_taikhoan'],
        'phan_quyen' => $validatedTaiKhoan['phan_quyen'],
        'id_nguoidung' => $nhanvien->id_nhanvien, // khóa ngoại
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

        // Xoá mềm tài khoản liên kết nếu có
        if ($nhanvien->taikhoan) {
            $nhanvien->taikhoan->delete();
        }

        $nhanvien->delete();

        return response()->json(['message' => 'Đã xoá nhân viên và tài khoản liên quan']);
    }
        public function getByKhoa($id_khoa)
{
    $bacsi = \App\Models\NhanVien::where('id_khoa', $id_khoa)
        ->where('chucvu', 'bacsi')
        ->with([
            'taikhoan:id_taikhoan,id_nguoidung,hoten',
            'khoa:id_khoa,tenkhoa'
        ])
        ->get(['id_nhanvien', 'id_khoa']);

    $bacsi = $bacsi->map(function ($item) {
        return [
            'id_nhanvien' => $item->id_nhanvien,
            'id_khoa' => $item->id_khoa,
            'tenkhoa' => $item->khoa->tenkhoa ?? null,
            'taikhoan' => $item->taikhoan
        ];
    });

    return response()->json($bacsi);
}





}
