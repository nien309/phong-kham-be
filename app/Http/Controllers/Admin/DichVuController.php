<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DichVu;
use Illuminate\Http\Request;
use App\Services\LogService;

class DichVuController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10); // Số bản ghi mỗi trang, mặc định 10
        return DichVu::with('khoa')->paginate($perPage);
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'tendichvu' => 'required|string|max:255',
        'dongia' => 'required|numeric|min:0',
        'trangthai' => 'required|in:hoatdong,tamngung',
        'id_khoa' => 'nullable|exists:khoas,id_khoa',
    ]);

    $data = $validated;
    $data['ngaytao'] = now();

    $dichvu = DichVu::create($data);

    LogService::log('Tạo dịch vụ mới: ' . $dichvu->tendichvu, 'dichvus');

    return response()->json([
        'message' => 'Tạo dịch vụ thành công',
        'data' => $dichvu
    ]);
}

    public function show($id)
    {
        return DichVu::with('khoa')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $dichvu = DichVu::findOrFail($id);

        $validated = $request->validate([
            'tendichvu' => 'nullable|string|max:255',
            'dongia' => 'nullable|numeric|min:0',
            'trangthai' => 'nullable|in:hoatdong,tamngung',
            'id_khoa' => 'nullable|exists:khoas,id_khoa',
        ]);

        $dichvu->update([
            ...$validated,
            'ngaycapnhat' => now(),
        ]);

        LogService::log('Cập nhật dịch vụ ID: ' . $dichvu->id_dichvu, 'dichvus');

        return response()->json([
            'message' => 'Cập nhật dịch vụ thành công',
            'data' => $dichvu
        ]);
    }

    public function destroy($id)
    {
        $dichvu = DichVu::findOrFail($id);
        $dichvu->delete();

        LogService::log('Xoá dịch vụ ID: ' . $dichvu->tendichvu, 'dichvus');

        return response()->json(['message' => 'Đã xoá dịch vụ']);
    }
   
}
