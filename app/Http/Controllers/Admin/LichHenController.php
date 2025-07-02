<?php

namespace App\Http\Controllers\Admin;

use App\Models\LichHen;
use Illuminate\Http\Request;
use App\Services\LogService;
class LichHenController extends Controller
{
    public function index()
    {
        return LichHen::with([
            'khachhang:id_khachhang,hoten',
            'nhanvien:id_nhanvien,hoten',
            'cakham:id_cakham,khunggio'
        ])
        ->orderBy('ngayhen', 'desc')
        ->get();
    }

    public function show($id)
    {
        return LichHen::with([
            'khachhang:id_khachhang,hoten',
            'nhanvien:id_nhanvien,hoten',
            'cakham:id_cakham,khunggio'
        ])->findOrFail($id);
    }
    public function datLich(Request $request)
    {
        $validated = $request->validate([
            'id_khachhang' => 'required|exists:khach_hangs,id_khachhang',
            'id_nhanvien' => 'required|exists:nhanviens,id_nhanvien',
            'id_cakham' => 'required|exists:cakham,id_cakham',
            'ngayhen' => 'required|date',
            'ghichu' => 'nullable|string',
        ]);

        $validated['trangthai'] = 'chờ xác nhận';

        return LichHen::create($validated);
    }

    
    public function taoLich(Request $request)
    {
        $validated = $request->validate([
            'id_khachhang' => 'required|exists:khach_hangs,id_khachhang',
            'id_nhanvien' => 'required|exists:nhanviens,id_nhanvien',
            'id_cakham' => 'required|exists:cakham,id_cakham',
            'ngayhen' => 'required|date',
            'ghichu' => 'nullable|string',
        ]);

        $validated['trangthai'] = 'đã xác nhận';

        return LichHen::create($validated);
    }

   
    public function huyLich($id)
    {
        $lichhen = LichHen::findOrFail($id);
        $lichhen->update(['trangthai' => 'đã huỷ']);

        return response()->json(['message' => 'Đã huỷ lịch hẹn']);
    }

  
    public function capNhatTrangThai(Request $request, $id)
    {
        $lichhen = LichHen::findOrFail($id);

        $validated = $request->validate([
            'trangthai' => 'required|in:chờ xác nhận,đã xác nhận,chuyển đến bác sĩ,chuyển đến lễ tân,hoàn thành,đã huỷ',
        ]);

        $lichhen->update(['trangthai' => $validated['trangthai']]);

        return $lichhen;
    }
}