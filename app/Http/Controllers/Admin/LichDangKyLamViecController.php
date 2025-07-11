<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LichDangKyLamViec;
use Illuminate\Http\Request;

class LichDangKyLamViecController extends Controller
{
    public function index()
{
    $dsLich = LichDangKyLamViec::with([
        'nhanvien.taikhoan:id_taikhoan,id_nguoidung,hoten',
        'nhanvien.khoa:id_khoa,tenkhoa'
    ])->get();

    $dsLich = $dsLich->map(function ($lich) {
        $lich->thoigiandangky = json_decode($lich->thoigiandangky, true);
        return $lich;
    });

    return response()->json([
        'message' => 'Danh sách lịch đăng ký',
        'data' => $dsLich
    ]);
}

    public function store(Request $request)
{
    // Lấy người dùng đăng nhập từ token
    $user = auth()->user();

    // Kiểm tra tài khoản phải là nhân viên
    if ($user->loai_taikhoan !== 'nhanvien') {
        return response()->json(['error' => 'Chỉ tài khoản nhân viên mới được đăng ký lịch làm việc.'], 403);
    }

    // Lấy thông tin nhân viên từ quan hệ
    $nhanvien = $user->nguoidung; // nên được định nghĩa từ quan hệ belongsTo trong TaiKhoan.php

    if (!$nhanvien) {
        return response()->json(['error' => 'Không tìm thấy thông tin nhân viên.'], 404);
    }

    // Validate dữ liệu đầu vào cơ bản
    $validated = $request->validate([
        'thangnam' => 'required|string',
        'thoigiandangky' => 'required|json',
        'ghichu' => 'nullable|string',
    ]);

    // Parse JSON và validate chi tiết
    $thoigiandangky = json_decode($validated['thoigiandangky'], true);

    if (!is_array($thoigiandangky)) {
        return response()->json(['error' => 'Dữ liệu thoigiandangky phải là mảng JSON hợp lệ.'], 422);
    }

    // Lấy danh sách ID các ca khám hợp lệ từ bảng cakham
    $validCaIds = \App\Models\CaKham::pluck('id_cakham')->toArray();

    // Duyệt từng ngày để kiểm tra ca khám
    foreach ($thoigiandangky as $ngay) {
        if (!isset($ngay['ca']) || !is_array($ngay['ca'])) {
            return response()->json(['error' => 'Mỗi ngày trong thoigiandangky phải chứa danh sách các ca.'], 422);
        }

        foreach ($ngay['ca'] as $ca) {
            if (!in_array($ca, $validCaIds)) {
                return response()->json(['error' => "Ca khám không hợp lệ: $ca"], 422);
            }
        }
    }

    // Tạo bản ghi lịch đăng ký làm việc
    $lich = \App\Models\LichDangKyLamViec::create([
        'id_nhanvien' => $nhanvien->id_nhanvien,
        'thangnam' => $validated['thangnam'],
        'thoigiandangky' => $validated['thoigiandangky'],
        'trangthai' => 'chờ duyệt',
        'ghichu' => $validated['ghichu'] ?? null,
    ]);

    return response()->json([
        'message' => 'Đăng ký lịch làm việc thành công.',
        'data' => $lich
    ], 201);
}

  public function show($id)
{
    $lich = LichDangKyLamViec::with([
        'nhanvien.taikhoan:id_taikhoan,id_nguoidung,hoten', // lấy hoten từ tai_khoan
        'nhanvien.khoa:id_khoa,tenkhoa',
    ])->findOrFail($id);

    $lich->thoigiandangky = json_decode($lich->thoigiandangky, true);

    return response()->json(
        $lich
    );
}



    public function update(Request $request, $id)
{
    $lich = LichDangKyLamViec::findOrFail($id);

    $validated = $request->validate([
        'thangnam' => 'sometimes|string',
        'thoigiandangky' => 'sometimes|json',
        'trangthai' => 'sometimes|in:chờ duyệt,đã duyệt,từ chối',
        'ghichu' => 'nullable|string',
    ]);

    // Giải mã JSON nếu có
    if (isset($validated['thoigiandangky']) && is_string($validated['thoigiandangky'])) {
        $validated['thoigiandangky'] = json_decode($validated['thoigiandangky'], true);
    }

    // Kiểm tra và xử lý theo trạng thái
    if (
        isset($validated['trangthai']) &&
        $validated['trangthai'] === 'đã duyệt' &&
        $lich->trangthai !== 'đã duyệt'
    ) {
        // Tạo lịch làm việc mới
        \App\Models\LichLamViec::create([
            'id_nhanvien' => $lich->id_nhanvien,
            'thoigianlamviec' => $lich->thoigiandangky,
            'ngaytao' => now(),
            'trangthai' => 'đang làm',
            'is_dinhky' => false,
            'lydothaydoi' => null,
        ]);
    }

    if (
        isset($validated['trangthai']) &&
        $validated['trangthai'] === 'từ chối' &&
        $lich->trangthai !== 'từ chối'
    ) {
        // Ghi lại lý do từ chối nếu có
        $validated['ghichu'] = $validated['ghichu'] ?? 'Yêu cầu đã bị từ chối';
    }

    // Cập nhật bản ghi gốc
    $lich->update([
        'thangnam' => $validated['thangnam'] ?? $lich->thangnam,
        'thoigiandangky' => isset($validated['thoigiandangky']) ? json_encode($validated['thoigiandangky']) : $lich->thoigiandangky,
        'trangthai' => $validated['trangthai'] ?? $lich->trangthai,
        'ghichu' => $validated['ghichu'] ?? $lich->ghichu,
    ]);

    return response()->json([
        'message' => 'Cập nhật thành công.',
        'data' => $lich
    ]);
}


    public function destroy($id)
    {
        $lich = LichDangKyLamViec::findOrFail($id);
        $lich->delete();

        return response()->json(['message' => 'Đã xoá mềm lịch đăng ký']);
    }
    public function destroyAll()
{
    LichDangKyLamViec::query()->delete(); // Xoá mềm tất cả

    return response()->json(['message' => 'Đã xoá hết lịch, sẵn sàng nhập lại mới!']);
}

}
