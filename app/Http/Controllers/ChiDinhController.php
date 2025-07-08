<?php

namespace App\Http\Controllers;

use App\Models\ChiDinh;
use Illuminate\Http\Request;
use App\Services\LogService;
use Illuminate\Support\Facades\Auth;

class ChiDinhController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->chucvu !== 'bacsi') return response()->json(['message' => 'Chỉ bác sĩ được tạo chỉ định'], 403);

        $validated = $request->validate([
            'id_thongtinkhambenh' => 'required|exists:thongtinkhambenh,id_thongtinkhambenh',
            'id_dichvu' => 'required|exists:dichvus,id_dichvu',
            'soluong' => 'required|integer|min:1',
            'dongia' => 'required|numeric',
        ]);

        $chidinh = ChiDinh::create($validated);
        LogService::log('Tạo chỉ định ID: ' . $chidinh->id_chidinh, 'chidinh');
        return response()->json($chidinh, 201);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->chucvu !== 'kythuatvien') return response()->json(['message' => 'Chỉ kỹ thuật viên được cập nhật'], 403);

        $chidinh = ChiDinh::findOrFail($id);
        $chidinh->update($request->only(['ketqua', 'hinhanh']));
        LogService::log('Cập nhật chỉ định ID: ' . $id, 'chidinh');
        return response()->json($chidinh);
    }

    public function show($id)
    {
        $chidinh = ChiDinh::with('dichvu')->findOrFail($id);
        return response()->json($chidinh);
    }
}
