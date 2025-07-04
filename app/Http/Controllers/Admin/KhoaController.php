<?php

namespace App\Http\Controllers\Admin;
use App\Models\Khoa;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\LogService;
class KhoaController extends Controller
{
    public function index()
    {
        return Khoa::with('nhanviens', 'dichvus')
            ->orderBy('id_khoa', 'asc')
            ->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tenkhoa' => 'required|string',
            'trangthai' => 'required|in:hoatdong,tamngung',
        ]);

        LogService::log('Tạo khoa mới: ' . $request->tenkhoa, 'khoas');
        return Khoa::create($validated);
    }

    public function show($id)
    {
        return Khoa::with('nhanviens', 'dichvus')->findOrFail($id);
    }

    public function update(Request $request, $id)
{
    $khoa = Khoa::findOrFail($id);

    $validated = $request->validate([
        'tenkhoa' => 'required|string|unique:khoas,tenkhoa,' . $id . ',id_khoa',
        'trangthai' => 'required|in:hoatdong,tamngung',
    ]);

    $khoa->update($validated);

     LogService::log('Cập nhật khoa: ' . $khoa->tenkhoa, 'khoas');
    return response()->json([
        'message' => 'Cập nhật khoa thành công',
        'data' => $khoa
    ]);
}


   public function destroy($id)
{
    $khoa = Khoa::with(['nhanviens', 'dichvus'])->findOrFail($id);

    if ($khoa->nhanviens->count() > 0 || $khoa->dichvus->count() > 0) {
        return response()->json([
            'message' => 'Không thể xoá khoa này vì vẫn còn nhân viên hoặc dịch vụ liên quan.'
        ], 400);
    }

    LogService::log('Xoá khoa: ' . $khoa->tenkhoa, 'khoas');
    $khoa->delete(); // Xoá mềm
    return response()->json(['message' => 'Đã xoá khoa thành công.']);
}

}
