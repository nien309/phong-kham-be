<?php
namespace App\Http\Controllers;

use App\Models\Benhan;
use Illuminate\Http\Request;
use App\Services\LogService;
use Illuminate\Support\Facades\Auth;

class BenhanController extends Controller
{
    // Xem danh sách bệnh án (phân quyền)
   use App\Models\KhachHang;
use App\Models\Benhan;

public function index()
{
    $user = Auth::user();

    // Nếu là khách hàng
    if ($user->loai_taikhoan === 'khachhang') {
        $khachhang = KhachHang::where('id_taikhoan', $user->id_taikhoan)->first();

        if (!$khachhang) {
            return response()->json(['message' => 'Không tìm thấy thông tin khách hàng'], 404);
        }

        // Trả về tất cả bệnh án thuộc hồ sơ bệnh án của khách hàng này
        return Benhan::whereHas('hosobenhan', function ($query) use ($khachhang) {
            $query->where('id_khachhang', $khachhang->id_khachhang);
        })->with('hosobenhan')->get();
    }

    // Nếu là nhân viên
    if (!in_array($user->chucvu ?? null, ['bacsi', 'dieuduong'])) {
        return response()->json(['message' => 'Không có quyền truy cập'], 403);
    }

    // Bác sĩ/điều dưỡng thì xem được tất cả bệnh án
    return Benhan::with('hosobenhan')->get();
}

    // Tạo bệnh án (bác sĩ)
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->chucvu !== 'bacsi') {
            return response()->json(['message' => 'Chỉ bác sĩ được phép tạo'], 403);
        }

        $validated = $request->validate([
            'id_hosobenhan' => 'required|exists:hosobenhan,id_hosobenhan',
            'chandoan' => 'nullable|string',
            'mota' => 'nullable|string',
            'ngaybatdau' => 'nullable|date',
        ]);

        $benhan = Benhan::create($validated);

        LogService::log('Tạo bệnh án ID: ' . $benhan->id_benhan, 'benhan');

        return response()->json($benhan, 201);
    }

    // Xem chi tiết bệnh án
    public function show($id)
    {
        $benhan = Benhan::with('thongtinkhambenh')->findOrFail($id);
        $user = Auth::user();

        if ($user->loai_taikhoan === 'khachhang') {
            $khachhang = KhachHang::where('id_taikhoan', $user->id_taikhoan)->first();

            if (!$khachhang || $benhan->hosobenhan->id_khachhang !== $khachhang->id_khachhang) {
                return response()->json(['message' => 'Bạn không được phép xem bệnh án này'], 403);
            }
        } elseif (!in_array($user->chucvu ?? null, ['bacsi', 'dieuduong'])) {
            return response()->json(['message' => 'Không có quyền truy cập'], 403);
        }

        return response()->json($benhan);
    }
    // Cập nhật (bác sĩ hoặc điều dưỡng)
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!in_array($user->chucvu, ['bacsi', 'dieuduong'])) {
            return response()->json(['message' => 'Không có quyền cập nhật'], 403);
        }

        $benhan = Benhan::findOrFail($id);

        $request->validate([
            'chandoan' => 'nullable|string',
            'mota' => 'nullable|string',
            'ngaybatdau' => 'nullable|date',
        ]);

        $benhan->update($request->only(['chandoan', 'mota', 'ngaybatdau']));

        LogService::log('Cập nhật bệnh án ID: ' . $id, 'benhan');

        return response()->json($benhan);
    }

    // ❌ Không có xoá
    public function destroy($id)
    {
        return response()->json(['message' => 'Không hỗ trợ xoá bệnh án'], 405);
    }
}
