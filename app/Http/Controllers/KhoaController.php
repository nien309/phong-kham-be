<?php

namespace App\Http\Controllers;

use App\Models\Khoa;
use Illuminate\Http\Request;

class KhoaController extends Controller
{
    public function index()
    {
        return Khoa::with('nhanviens', 'dichvus')->get();
    }

   public function store(Request $request)
{
    $validated = $request->validate([
        'tenkhoa' => [
            'required',
            'string',
            Rule::unique('khoas', 'tenkhoa')->where(function ($query) {
                return $query->whereNull('deleted_at');
            }),
        ],
        'trangthai' => 'required|in:hoatdong,tamngung',
    ], [
        'tenkhoa.unique' => 'Tên khoa đã tồn tại.',
    ]);

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

    $khoa->delete(); // Xoá mềm
    return response()->json(['message' => 'Đã xoá khoa thành công (xoá mềm).']);
}

}
