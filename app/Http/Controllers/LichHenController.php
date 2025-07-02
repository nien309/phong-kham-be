<?php

namespace App\Http\Controllers;

use App\Models\LichHen;
use Illuminate\Http\Request;

class LichHenController extends Controller
{
    
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

}