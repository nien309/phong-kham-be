<?php

namespace App\Http\Controllers;

use App\Models\ThongTinKhamBenh;
use Illuminate\Http\Request;
use App\Services\LogService;
use Illuminate\Support\Facades\Auth;

class ThongTinKhamBenhController extends Controller
{
    /**
     * 📌 Lấy danh sách thông tin khám bệnh
     */
    public function index()
    {
        $user = Auth::user()->load('nhanvien');

        if (!in_array($user->nhanvien->chucvu ?? null, ['bacsi', 'dieuduong'])) {
            return response()->json(['message' => 'Không có quyền truy cập'], 403);
        }

        // Chỉ lấy những TT khám bệnh thuộc khoa của nhân viên
        $ttkb = ThongTinKhamBenh::whereHas('benhan', function ($q) use ($user) {
            $q->where('id_khoa', $user->nhanvien->id_khoa);
        })->with(['benhan', 'chidinh', 'toathuoc', 'hoadon'])->get();

        return response()->json($ttkb);
    }

    /**
     * 📌 Tạo mới thông tin khám bệnh (Chỉ bác sĩ)
     */
    public function store(Request $request)
    {
        $user = Auth::user()->load('nhanvien');

        if (($user->nhanvien->chucvu ?? null) !== 'bacsi') {
            return response()->json(['message' => 'Chỉ bác sĩ được phép tạo'], 403);
        }

        $validated = $request->validate([
            'id_benhan'  => 'required|exists:benhan,id_benhan',
            'trieuchung' => 'required|string',
            'ngaykham'   => 'required|date',
            'chandoan'   => 'required|string',
            'trangthai'  => 'required|string',
        ]);

        // Kiểm tra bệnh án có thuộc khoa của bác sĩ không
        $benhan = \App\Models\Benhan::findOrFail($validated['id_benhan']);
        if ($benhan->id_khoa !== $user->nhanvien->id_khoa) {
            return response()->json(['message' => 'Bệnh án không thuộc khoa của bạn'], 403);
        }

        $ttkb = ThongTinKhamBenh::create($validated);

        LogService::log('Tạo thông tin khám bệnh ID: ' . $ttkb->id_thongtinkhambenh, 'thongtinkhambenh');

        return response()->json($ttkb, 201);
    }

    /**
     * 📌 Xem chi tiết thông tin khám bệnh
     */
    public function show($id)
    {
        $ttkb = ThongTinKhamBenh::with([ 'chidinh.dichvu', 'benhan.hosobenhan.khachhang.taikhoan', 'toathuoc.chitiettoathuoc', 'hoadon'])->findOrFail($id);
        $user = Auth::user()->load('nhanvien');

        if ($user->loai_taikhoan === 'khachhang') {
            $khachhang = $user->nguoidung;

            if (!$khachhang) {
                return response()->json(['message' => 'Không tìm thấy thông tin khách hàng'], 404);
            }

            if (!$ttkb->benhan || !$ttkb->benhan->hosobenhan || 
                $ttkb->benhan->hosobenhan->id_khachhang !== $khachhang->id_khachhang) {
                return response()->json(['message' => 'Không được phép xem TT khám bệnh này'], 403);
            }
        } elseif (in_array($user->nhanvien->chucvu ?? null, ['bacsi', 'dieuduong'])) {
            if ($ttkb->benhan->id_khoa !== $user->nhanvien->id_khoa) {
                return response()->json(['message' => 'Không được phép xem TT khám bệnh khoa khác'], 403);
            }
        } else {
            return response()->json(['message' => 'Không có quyền truy cập'], 403);
        }

        return response()->json($ttkb);
    }

    /**
     * 📌 Cập nhật thông tin khám bệnh (bác sĩ & điều dưỡng)
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user()->load('nhanvien');

        if (!in_array($user->nhanvien->chucvu ?? null, ['bacsi', 'dieuduong'])) {
            return response()->json(['message' => 'Không có quyền cập nhật'], 403);
        }

        $ttkb = ThongTinKhamBenh::with('benhan')->findOrFail($id);

        if ($ttkb->benhan->id_khoa !== $user->nhanvien->id_khoa) {
            return response()->json(['message' => 'Không được chỉnh TT khám bệnh khoa khác'], 403);
        }

        $request->validate([
           
            'trangthai'  => 'required|string',
        ]);

        $ttkb->update($request->only(['trieuchung', 'chandoan', 'trangthai']));

        LogService::log('Cập nhật thông tin khám bệnh ID: ' . $id, 'thongtinkhambenh');

        return response()->json($ttkb);
    }

    /**
     * ❌ Không hỗ trợ xoá
     */
    public function destroy($id)
    {
        return response()->json(['message' => 'Không hỗ trợ xoá thông tin khám bệnh'], 405);
    }
    public function thongTinKhamBenhCuaToi()
{
    $user = Auth::user();

    if ($user->loai_taikhoan !== 'khachhang') {
        return response()->json(['message' => 'Không có quyền'], 403);
    }

    $khachhang = $user->nguoidung;
    if (!$khachhang) {
        return response()->json(['message' => 'Không tìm thấy thông tin khách hàng'], 404);
    }

    $ttkb = ThongTinKhamBenh::whereHas('benhan.hosobenhan', function ($q) use ($khachhang) {
        $q->where('id_khachhang', $khachhang->id_khachhang);
    })->with(['benhan'])->get();

    return response()->json($ttkb);
}
/**
 * 📌 Lấy danh sách Thông Tin Khám Bệnh theo ID Bệnh Án
 */
public function getByBenhan($id_benhan)
{
    $user = Auth::user()->load('nhanvien');

    // Chỉ bác sĩ, điều dưỡng được phép xem
    if (!in_array($user->nhanvien->chucvu ?? null, ['bacsi', 'dieuduong'])) {
        return response()->json(['message' => 'Không có quyền truy cập'], 403);
    }

    // Tìm bệnh án và kiểm tra khoa
    $benhan = \App\Models\Benhan::findOrFail($id_benhan);
    if ($benhan->id_khoa !== $user->nhanvien->id_khoa) {
        return response()->json(['message' => 'Không được phép xem bệnh án khoa khác'], 403);
    }

    $ttkb = ThongTinKhamBenh::where('id_benhan', $id_benhan)
        ->with([ 'chidinh', 'toathuoc', 'hoadon'])
        ->get();

    return response()->json($ttkb);
}

public function chiTiet($id)
    {
        $ttkb = ThongTinKhamBenh::with(['chidinh.dichvu', 'benhan.nhanvien.taikhoan'])->findOrFail($id);

        $chidinh = $ttkb->chidinh->map(function ($cd) {
            return [
                'dichvu' => $cd->dichvu ?? null,
                'soluong' => $cd->soluong,
                'dongia' => $cd->dongia,
            ];
        });

        return response()->json([
            'id_thongtinkhambenh' => $ttkb->id_thongtinkhambenh,
            'ngaykham' => $ttkb->ngaykham,
            'bacsi' => $ttkb->benhan->nhanvien->taikhoan->hoten ?? null,
            'chidinh' => $chidinh,
        ]);
    }
}
