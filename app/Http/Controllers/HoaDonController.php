<?php

namespace App\Http\Controllers;

use App\Models\HoaDon;
use App\Models\ThongTinKhamBenh;
use App\Models\ChiDinh;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\LogService;

use PDF;

class HoaDonController extends Controller
{
    /**
     * TẠO HOÁ ĐƠN (Chỉ lễ tân/thu ngân)
     */
    public function store(Request $request)
{
    $user = Auth::user()->load('nhanvien');

    if (!$user->nhanvien || !in_array($user->nhanvien->chucvu, ['letan', 'thungan'])) {
        return response()->json(['message' => 'Bạn không có quyền tạo hoá đơn!'], 403);
    }

    $request->validate([
        'id_thongtinkhambenh' => 'required|exists:thongtinkhambenh,id_thongtinkhambenh',
        'hinhthucthanhtoan' => 'in:tien_mat,chuyen_khoan,vi_dien_tu',
    ]);

    if (HoaDon::where('id_thongtinkhambenh', $request->id_thongtinkhambenh)->exists()) {
        return response()->json(['message' => 'Hoá đơn đã tồn tại!'], 409);
    }

    $chidinh = ChiDinh::where('id_thongtinkhambenh', $request->id_thongtinkhambenh)->get();
    if ($chidinh->isEmpty()) {
        return response()->json(['message' => 'Không có chỉ định nào để tính tiền!'], 400);
    }

    $tongtien = $chidinh->sum(fn($item) => (float) $item->soluong * (float) $item->dongia);

    $hoadon = HoaDon::create([
        'id_thongtinkhambenh' => $request->id_thongtinkhambenh,
        'id_taikhoan' => $user->id_taikhoan,
        'tongtien' => $tongtien,
        'ngaytao' => now(),
        'hinhthucthanhtoan' => $request->hinhthucthanhtoan ?? 'tien_mat',
        'trangthai' => 'cho_thanh_toan',
    ]);

    LogService::log('Tạo hoá đơn ID: ' . $hoadon->id_hoadon, 'hoadon');

    return response()->json([
        'message' => 'Tạo hoá đơn thành công.',
        'hoadon' => $hoadon
    ], 201);
}


    
     
    public function index(Request $request)
    {
        $query = HoaDon::with('thongtinkhambenh.benhan.hosobenhan.khachhang.taikhoan');

        if ($request->has('sdt')) {
            $query->whereHas('thongtinkhambenh.hosobenhan.khachhang.taikhoan', function ($q) use ($request) {
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
    public function cancel (Request $request,$id){
        $user = Auth::user();
        if(!$user->nhanvien || !in_array($user->nhanvien->chucvu, ['thungan'])){
            return response()->json(['message' => 'Bạn không có quyền huỷ hoá đơn'],403);
        }
        $request->validate([
            'lydo'=>'required|string|max:255',
        ]);
        $hoadon= HoaDon::findOrFail($id);
        if ($hoadon->trangthai === 'da_thanh_toan'){
            return response()->json(['message'=>'Không thể huỷ hoá đơn đã thanh toán.'],400);
        }
        $hoadon->trangthai='huy';
        $hoadon->lydo_huy=$request->lydo;
        $hoadon->save();
            LogService::log('Huỷ hoá đơn ID: ' . $hoadon->id_hoadon, 'hoadon');
        return response()->json(['message'=>'Đã huỷ hoá đơn thành công']);
    }
    public function update(Request $request, $id){
        $user = Auth::user();
        if(!$user->nhanvien || !in_array($user->nhanvien->chucvu,['thungan'])){
            return response()->json(['message'=> 'Bạn không có quyền cập nhật trạng thái hoá đơn'],403);
        }
        $request ->validate([
            'trangthai' => 'required|in:cho_thanh_toan,da_thanh_toan',

        ]);
        $hoadon= HoaDon::findOrFail($id);
        if($hoadon->trangthai==='huy'){
            return response()->json(['message'=>'Không thể cập nhật hoá đơn đã huỷ!'],400);
        }
        $hoadon->trangthai =$request->trangthai;
        $hoadon->save();
            LogService::log('Cập nhật trạng thái hoá đơn ID: ' . $hoadon->id_hoadon, 'hoadon');
        return response()->json([
            'message' => "Cập nhật trạng thái hoá đơn thành công.",
            'hoadon' =>$hoadon,
        ]);
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
