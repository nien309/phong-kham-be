<?php

namespace App\Http\Controllers;

use App\Models\ChiTietToaThuoc;
use App\Models\ToaThuoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChiTietToaThuocController extends Controller
{
    // 📋 Danh sách chi tiết toa thuốc cho 1 toa (lọc theo id_toathuoc)
    public function index(Request $request)
    {
        $user = Auth::user()->load('nhanvien');

        if (!in_array($user->nhanvien->chucvu ?? null, ['bacsi', 'dieuduong'])) {
            return response()->json(['message' => 'Không có quyền'], 403);
        }

        $request->validate([
            'id_toathuoc' => 'required|exists:toathuoc,id_toathuoc',
        ]);

        $toa = ToaThuoc::with('thongtinkhambenh.benhan')->findOrFail($request->id_toathuoc);

        if ($toa->thongtinkhambenh->benhan->id_khoa !== $user->nhanvien->id_khoa) {
            return response()->json(['message' => 'Không được phép xem chi tiết toa thuốc khoa khác'], 403);
        }

        $list = ChiTietToaThuoc::where('id_toathuoc', $toa->id_toathuoc)->get();

        return response()->json($list);
    }

    // ➕ Thêm chi tiết toa thuốc (BÁC SĨ)
    public function store(Request $request)
    {
        $user = Auth::user()->load('nhanvien');

        if ($user->nhanvien->chucvu !== 'bacsi') {
            return response()->json(['message' => 'Chỉ bác sĩ được thêm chi tiết toa thuốc'], 403);
        }

        $validated = $request->validate([
            'id_toathuoc' => 'required|exists:toathuoc,id_toathuoc',
            'ten' => 'required|string',
            'so_luong' => 'required|integer|min:1',
            'don_vi_tinh' => 'required|string',
            'cach_dung' => 'required|string',
        ]);

        $toa = ToaThuoc::with('thongtinkhambenh.benhan')->findOrFail($validated['id_toathuoc']);

        if ($toa->thongtinkhambenh->benhan->id_khoa !== $user->nhanvien->id_khoa) {
            return response()->json(['message' => 'Không được phép thêm chi tiết toa thuốc khoa khác'], 403);
        }

        $ct = ChiTietToaThuoc::create($validated);

        return response()->json($ct, 201);
    }

    // 📄 Xem 1 chi tiết toa thuốc
    public function show(ChiTietToaThuoc $chiTietToaThuoc)
    {
        $user = Auth::user()->load('nhanvien');

        if (!in_array($user->nhanvien->chucvu ?? null, ['bacsi', 'dieuduong'])) {
            return response()->json(['message' => 'Không có quyền'], 403);
        }

        $toa = ToaThuoc::with('thongtinkhambenh.benhan')->findOrFail($chiTietToaThuoc->id_toathuoc);

        if ($toa->thongtinkhambenh->benhan->id_khoa !== $user->nhanvien->id_khoa) {
            return response()->json(['message' => 'Không được phép xem chi tiết toa thuốc khoa khác'], 403);
        }

        return response()->json($chiTietToaThuoc);
    }

    // ✏️ Sửa chi tiết toa thuốc (BÁC SĨ)
  public function update(Request $request, $id) // Bỏ model binding
{
    // 1. Tìm thủ công với eager loading
    $chiTiet = ChiTietToaThuoc::with([
            'toaThuoc:id_toathuoc,id_thongtinkhambenh', 
            'toaThuoc.thongtinkhambenh:id_thongtinkhambenh,id_benhan',
            'toaThuoc.thongtinkhambenh.benhan:id_benhan,id_khoa'
        ])
        ->findOrFail($id);

    // 2. Debug quan hệ
    if (!$chiTiet->toaThuoc) {
        logger()->error('Lỗi quan hệ toa thuốc', [
            'expected_toa_id' => $chiTiet->id_toathuoc,
            'actual_toa' => ToaThuoc::find($chiTiet->id_toathuoc)?->toArray()
        ]);
        
        abort(500, 'Dữ liệu quan hệ không khớp');
    }

    // 3. Kiểm tra quyền
    $user = Auth::user()->load('nhanvien:id,chucvu,id_khoa');
    
    if ($user->nhanvien->chucvu !== 'bacsi') {
        return response()->json(['message' => 'Chỉ bác sĩ được cập nhật'], 403);
    }

    // 4. Kiểm tra khoa
    if ($chiTiet->toaThuoc->thongtinkhambenh->benhan->id_khoa !== $user->nhanvien->id_khoa) {
        return response()->json(['message' => 'Không được phép cập nhật chi tiết toa thuốc khoa khác'], 403);
    }

    // 5. Validate và update
    $validated = $request->validate([
        'ten' => 'nullable|string|max:255',
        'so_luong' => 'nullable|integer|min:1|max:100',
        'don_vi_tinh' => 'nullable|string|max:50',
        'cach_dung' => 'nullable|string|max:500'
    ]);

    $chiTiet->update($validated);

    return response()->json([
        'success' => true,
        'data' => $chiTiet->fresh(['toaThuoc.thongtinkhambenh.benhan'])
    ]);
}

    // ❌ Xoá chi tiết toa thuốc (BÁC SĨ)
    public function destroy(ChiTietToaThuoc $chiTietToaThuoc)
    {
        $user = Auth::user()->load('nhanvien');

        if ($user->nhanvien->chucvu !== 'bacsi') {
            return response()->json(['message' => 'Chỉ bác sĩ được xoá chi tiết toa thuốc'], 403);
        }

        $toa = ToaThuoc::with('thongtinkhambenh.benhan')->findOrFail($chiTietToaThuoc->id_toathuoc);

        if ($toa->thongtinkhambenh->benhan->id_khoa !== $user->nhanvien->id_khoa) {
            return response()->json(['message' => 'Không được phép xoá chi tiết toa thuốc khoa khác'], 403);
        }

        $chiTietToaThuoc->delete();

        return response()->json(['message' => 'Đã xoá chi tiết toa thuốc']);
    }
}
