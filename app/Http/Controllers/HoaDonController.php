<?php

namespace App\Http\Controllers;

use App\Models\HoaDon;
use Illuminate\Http\Request;
use App\Services\LogService;
use Illuminate\Support\Facades\Auth;

class HoaDonController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_thongtinkhambenh' => 'required|exists:thongtinkhambenh,id_thongtinkhambenh',
            'ngaytao' => 'required|date',
            'trangthai' => 'in:cho_thanh_toan,da_thanh_toan,huy',
            'hinhthucthanhtoan' => 'required|string'
        ]);

        $hd = HoaDon::create($validated);
        LogService::log('Tạo hoá đơn ID: ' . $hd->id_hoadon, 'hoadon');
        return response()->json($hd, 201);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->chucvu !== 'thungan') return response()->json(['message' => 'Chỉ thu ngân được cập nhật hoá đơn'], 403);

        $hd = HoaDon::findOrFail($id);
        $hd->update($request->only(['trangthai', 'hinhthucthanhtoan']));
        LogService::log('Cập nhật hoá đơn ID: ' . $id, 'hoadon');
        return response()->json($hd);
    }

    public function show($id)
    {
        return response()->json(HoaDon::findOrFail($id));
    }
}