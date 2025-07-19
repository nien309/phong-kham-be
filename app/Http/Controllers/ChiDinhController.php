<?php

namespace App\Http\Controllers;

use App\Models\ChiDinh;
use App\Models\ThongTinKhamBenh;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\LogService;

class ChiDinhController extends Controller
{
    /**
     * 📌 Danh sách chỉ định
     */
    public function index()
    {
        $user = Auth::user()->load('nhanvien');

        // Nếu là Kỹ thuật viên → Xem toàn bộ không filter khoa
        if (($user->nhanvien->chucvu ?? null) === 'kythuatvien') {
            return ChiDinh::with(['thongtinkhambenh', 'dichvu'])->get();
        }

        // Nếu là Bác sĩ hoặc Điều dưỡng → Chỉ xem chỉ định thuộc khoa của mình
        if (in_array($user->nhanvien->chucvu ?? null, ['bacsi', 'dieuduong'])) {
            return ChiDinh::whereHas('thongtinkhambenh.benhan', function ($q) use ($user) {
                $q->where('id_khoa', $user->nhanvien->id_khoa);
            })->with(['thongtinkhambenh', 'dichvu'])->get();
        }

        // Nếu là Khách hàng → Không cho list all, chỉ cho xem qua API riêng
        return response()->json(['message' => 'Không có quyền'], 403);
    }

    public function chidinhCuaToi() {
    $user = Auth::user();
    if ($user->loai_taikhoan !== 'khachhang') {
        return response()->json(['message' => 'Không có quyền'], 403);
    }

    $khachhang = $user->nguoidung;
    if (!$khachhang) {
        return response()->json(['message' => 'Không tìm thấy thông tin KH'], 404);
    }

    $chidinh = ChiDinh::whereHas('thongtinkhambenh.benhan.hosobenhan', function ($q) use ($khachhang) {
        $q->where('id_khachhang', $khachhang->id_khachhang);
    })->with(['thongtinkhambenh', 'dichvu'])->get();

    return response()->json($chidinh);
}

    /**
     * 📌 Tạo chỉ định (Chỉ Bác sĩ)
     */
    public function store(Request $request)
    {
        $user = Auth::user()->load('nhanvien');

        if (($user->nhanvien->chucvu ?? null) !== 'bacsi') {
            return response()->json(['message' => 'Chỉ bác sĩ được phép chỉ định'], 403);
        }

        $validated = $request->validate([
            'id_thongtinkhambenh' => 'required|exists:thongtinkhambenh,id_thongtinkhambenh',
            'id_dichvu'           => 'required|exists:dich_vus,id_dichvu',
            'soluong'             => 'required|integer|min:1',
            'dongia'             => 'required|numeric|min:1',

        ]);

        // Kiểm tra ThongTinKhamBenh có thuộc khoa của bác sĩ không
        $ttkb = ThongTinKhamBenh::with('benhan')->findOrFail($validated['id_thongtinkhambenh']);

        if ($ttkb->benhan->id_khoa !== $user->nhanvien->id_khoa) {
            return response()->json(['message' => 'Không được chỉ định ngoài khoa của bạn'], 403);
        }

        $chidinh = ChiDinh::create([
            ...$validated,
            'trangthai'    => 'chờ thực hiện',
            'ngaychidinh'  => now(),
        ]);

        LogService::log('Tạo chỉ định ID: ' . $chidinh->id_chidinh, 'chidinh');

        return response()->json($chidinh, 201);
    }

    /**
     * 📌 Xem chi tiết chỉ định
     */
    public function show($id)
    {
        $chidinh = ChiDinh::with(['thongtinkhambenh.benhan.hosobenhan', 'dichvu'])->findOrFail($id);
        $user = Auth::user()->load('nhanvien');

        // Nếu là KTV → Xem tất cả
        if (($user->nhanvien->chucvu ?? null) === 'kythuatvien') {
            return response()->json($chidinh);
        }

        // Nếu là BS/ĐD → Chỉ xem thuộc khoa của mình
        if (in_array($user->nhanvien->chucvu ?? null, ['bacsi', 'dieuduong'])) {
            if ($chidinh->thongtinkhambenh->benhan->id_khoa !== $user->nhanvien->id_khoa) {
                return response()->json(['message' => 'Không được xem chỉ định khoa khác'], 403);
            }
            return response()->json($chidinh);
        }

        // Nếu là KH → Xem chỉ định của mình
        if ($user->loai_taikhoan === 'khachhang') {
            $khachhang = $user->nguoidung;
            if (!$khachhang) {
                return response()->json(['message' => 'Không tìm thấy thông tin khách hàng'], 404);
            }

            if (
                !$chidinh->thongtinkhambenh->benhan ||
                !$chidinh->thongtinkhambenh->benhan->hosobenhan ||
                $chidinh->thongtinkhambenh->benhan->hosobenhan->id_khachhang !== $khachhang->id_khachhang
            ) {
                return response()->json(['message' => 'Không được phép xem chỉ định này'], 403);
            }

            return response()->json($chidinh);
        }

        return response()->json(['message' => 'Không có quyền'], 403);
    }

    /**
     * 📌 Cập nhật chỉ định (KTV cập nhật kết quả)
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user()->load('nhanvien');

        if (($user->nhanvien->chucvu ?? null) !== 'kythuatvien') {
            return response()->json(['message' => 'Chỉ kỹ thuật viên được cập nhật kết quả'], 403);
        }

        $chidinh = ChiDinh::findOrFail($id);

        $validated = $request->validate([
            'ketqua'       => 'nullable|string',
            'hinhanh'      => 'nullable|string',
            'trangthai'    => 'required|string',
            'ngaythuchien' => 'nullable|date',
        ]);

        $chidinh->update([
            'ketqua'       => $validated['ketqua'] ?? $chidinh->ketqua,
            'hinhanh'      => $validated['hinhanh'] ?? $chidinh->hinhanh,
            'trangthai'    => $validated['trangthai'] ?? 'hoàn thành',
            'ngaythuchien' => $validated['ngaythuchien'] ?? now(),
        ]);

        LogService::log('KTV cập nhật chỉ định ID: ' . $chidinh->id_chidinh, 'chidinh');

        return response()->json($chidinh);
    }

    /**
     * ❌ Không hỗ trợ xoá
     */
    public function destroy($id)
    {
        return response()->json(['message' => 'Không hỗ trợ xoá chỉ định'], 405);
    }
}
