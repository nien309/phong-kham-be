<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller; 
use App\Services\LogService;
use App\Models\CaKham;
use Illuminate\Http\Request;

class CaKhamController extends Controller
{
    public function index()
    {
        return CaKham::orderBy('khunggio')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'khunggio' => 'required|in:sáng,chiều',
            'trangthai' => 'required|in:đang hoạt động,đã tắt',
        ]);

        LogService::log('Tạo ca khám khung giờ: ' . $validated['khunggio'], 'cakham');

        return CaKham::create($validated);
    }

    public function update(Request $request, $id)
{
    $cakham = CaKham::findOrFail($id);

    $validated = $request->validate([
        'khunggio' => 'in:sáng,chiều',
        'trangthai' => 'in:đang hoạt động,đã tắt',
    ]);

    if (empty($validated)) {
        return response()->json(['message' => 'Không có dữ liệu để cập nhật'], 422);
    }

    $cakham->update($validated);

    LogService::log('Cập nhật ca khám ID: ' . $cakham->id_cakham, 'cakham');

    return $cakham;
}

}