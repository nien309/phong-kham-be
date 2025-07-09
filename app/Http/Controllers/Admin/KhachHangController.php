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
       return KhachHang::with('taikhoan')
        ->orderBy('id_khachhang', 'asc')
        ->get();
        
    }

    public function store(Request $request)
{
    $taikhoanData = $request->input('taikhoan');

    // Gán mặc định nếu thiếu
    $taikhoanData['loai_taikhoan'] = $taikhoanData['loai_taikhoan'] ?? 'khachhang';
    $taikhoanData['phan_quyen'] = $taikhoanData['phan_quyen'] ?? 'khachhang';

    // ✅ Validate thông tin khách hàng
    $validatedKhachHang = $request->validate([
        'nghenghiep' => 'nullable|string',
    ]);

    // ✅ Validate thông tin tài khoản
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

    // ✅ Tạo khách hàng trước
    $khachhang = KhachHang::create([
        'nghenghiep' => $validatedKhachHang['nghenghiep'] ?? '',
    ]);

    // ✅ Tạo tài khoản liên kết với khách hàng
    $taikhoan = TaiKhoan::create([
        'hoten' => $validatedTaiKhoan['hoten'],
        'matkhau' => bcrypt($validatedTaiKhoan['matkhau']),
        'sdt' => $validatedTaiKhoan['sdt'],
        'email' => $validatedTaiKhoan['email'],
        'gioitinh' => $taikhoanData['gioitinh'] ?? 'khac',
        'ngaysinh' => $taikhoanData['ngaysinh'] ?? now(),
        'diachi' => $taikhoanData['diachi'] ?? '',
        'loai_taikhoan' => $validatedTaiKhoan['loai_taikhoan'],
        'phan_quyen' => $validatedTaiKhoan['phan_quyen'],
        'id_nguoidung' => $khachhang->id_khachhang, // liên kết
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
        $khachhang = KhachHang::with('taikhoan')->findOrFail($id);

        if ($khachhang->taikhoan) {
            $khachhang->taikhoan->delete(); // Xoá mềm tài khoản khách hàng
        }

        $khachhang->delete(); // Xoá mềm khách hàng

        LogService::log('Xoá khách hàng ID: ' . $id, 'khachhangs');

        return response()->json(['message' => 'Đã xoá khách hàng và tài khoản liên quan']);
    }

    public function search(Request $request)
{
    $request->validate([
        'sdt'   => 'nullable|string',
        'hoten' => 'nullable|string',
    ]);

    if (!$request->sdt && !$request->hoten) {
        return response()->json(['message' => 'Phải nhập ít nhất số điện thoại hoặc họ tên'], 422);
    }

    $khachhangs = \App\Models\KhachHang::whereHas('taikhoan', function($q) use ($request) {
        if ($request->sdt) {
            $q->where('sdt', $request->sdt);
        }
        if ($request->hoten) {
            $q->where('hoten', 'ilike', '%' . $request->hoten . '%');
        }
    })->with('taikhoan')->get();

    if ($khachhangs->isEmpty()) {
        return response()->json(['message' => 'Không tìm thấy khách hàng'], 404);
    }

    return response()->json($khachhangs);
}


}
