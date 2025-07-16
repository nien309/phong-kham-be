<?php

namespace App\Http\Controllers;

use App\Models\HoaDon;
use App\Models\ThongTinKhamBenh;
use App\Models\ChiDinh;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;

class HoaDonController extends Controller
{
    /**
     * TẠO HOÁ ĐƠN (Chỉ lễ tân/thu ngân)
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // 👉 RÀNG BUỘC: Chỉ nhân viên có chức vụ lễ tân/thu ngân
        if (!$user->nhanvien || !in_array($user->nhanvien->chucvu, ['letan', 'thungan'])) {
            return response()->json(['message' => 'Bạn không có quyền tạo hoá đơn!'], 403);
        }

        $request->validate([
            'id_thongtinkhambenh' => 'required|exists:thongtinkhambenh,id_thongtinkhambenh',
            'hinhthucthanhtoan' => 'in:tien_mat,chuyen_khoan,vi_dien_tu',
        ]);

        // Lấy chỉ định
        $chidinh = ChiDinh::where('id_thongtinkhambenh', $request->id_thongtinkhambenh)->get();

        if ($chidinh->isEmpty()) {
            return response()->json(['message' => 'Không có chỉ định nào để tính tiền!'], 400);
        }

        // Tính tổng tiền
        $tongtien = $chidinh->sum(fn($item) => $item->soluong * $item->dongia);

        // Tạo hoá đơn
        $hoadon = HoaDon::create([
            'id_thongtinkhambenh' => $request->id_thongtinkhambenh,
            'id_taikhoan' => $user->id_taikhoan,
            'tongtien' => $tongtien,
            'ngaytao' => now(),
            'hinhthucthanhtoan' => $request->hinhthucthanhtoan ?? 'tien_mat',
            'trangthai' => 'cho_thanh_toan',
        ]);

        return response()->json([
            'message' => 'Tạo hoá đơn thành công.',
            'hoadon' => $hoadon
        ], 201);
    }

    /**
     * TÌM KIẾM DANH SÁCH HOÁ ĐƠN (Lễ tân, KH, quản lý)
     * Cho khách xem thì không cần chặn, còn muốn chặn thì có thể check riêng.
     */
    public function index(Request $request)
    {
        $query = HoaDon::with('thongtinkhambenh');

        if ($request->has('sdt')) {
            $query->whereHas('thongtinkhambenh.hosobenhan.khachhang', function ($q) use ($request) {
                $q->where('sdt', $request->sdt);
            });
        }

        if ($request->has('trangthai')) {
            $query->where('trangthai', $request->trangthai);
        }

        if ($request->has('from_date') && $request->has('to_date')) {
            $query->whereBetween('ngaytao', [$request->from_date, $request->to_date]);
        }

        return response()->json($query->paginate(10));
    }

    /**
     * XEM CHI TIẾT HOÁ ĐƠN (ai cũng xem được)
     */
    public function show($id)
    {
        $hoadon = HoaDon::with(['thongtinkhambenh.chidinh.dichvu'])->findOrFail($id);
        return response()->json($hoadon);
    }

    /**
     * HUỶ HOÁ ĐƠN (chỉ lễ tân/thu ngân)
     */
    public function destroy(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->nhanvien || !in_array($user->nhanvien->chucvu, ['letan', 'thungan'])) {
            return response()->json(['message' => 'Bạn không có quyền huỷ hoá đơn!'], 403);
        }

        $request->validate([
            'lydo' => 'required|string|max:255',
        ]);

        $hoadon = HoaDon::findOrFail($id);

        if ($hoadon->trangthai === 'da_thanh_toan') {
            return response()->json(['message' => 'Không thể huỷ hoá đơn đã thanh toán.'], 400);
        }

        $hoadon->trangthai = 'huy';
        $hoadon->lydo_huy = $request->lydo;
        $hoadon->save();

        return response()->json(['message' => 'Đã huỷ hoá đơn thành công.']);
    }

    /**
     * XUẤT PDF (không ràng buộc, tuỳ quyền)
     */
    public function exportPdf($id)
    {
        $hoadon = HoaDon::with(['thongtinkhambenh.chidinh.dichvu'])->findOrFail($id);

        $pdf = PDF::loadView('pdf.hoadon', ['hoadon' => $hoadon]);

        return $pdf->download('hoadon_' . $id . '.pdf');
    }
}
