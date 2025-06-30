<?php

namespace App\Http\Controllers;

use App\Models\DichVu;
use Illuminate\Http\Request;

class DichVuController extends Controller
{
    public function index()
    {
        return DichVu::with('khoa')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'dongia' => 'required|numeric|min:0',
            'trangthai' => 'required|in:hoatdong,tamngung',
            'id_khoa' => 'nullable|exists:khoas,id_khoa',
        ]);

        return DichVu::create([
            ...$validated,
            'ngaytao' => now(),
        ]);
    }

    public function show($id)
    {
        return DichVu::with('khoa')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $dichvu = DichVu::findOrFail($id);

        $dichvu->update([
            'dongia' => $request->dongia ?? $dichvu->dongia,
            'trangthai' => $request->trangthai ?? $dichvu->trangthai,
            'id_khoa' => $request->id_khoa ?? $dichvu->id_khoa,
            'ngaycapnhat' => now(),
        ]);

        return $dichvu;
    }

    public function destroy($id)
    {
        DichVu::findOrFail($id)->delete();
        return response()->json(['message' => 'Đã xoá dịch vụ']);
    }
}
