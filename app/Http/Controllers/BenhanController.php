<?php


namespace App\Http\Controllers;

use App\Models\Benhan;
use App\Models\KhachHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\LogService;

class BenhanController extends Controller
{
    /**
     * 📌 Danh sách bệnh án
     */
    public function index()
    {
        $user = Auth::user()->load('nhanvien');

        if ($user->loai_taikhoan === 'khachhang') {
            $khachhang = KhachHang::where('id_taikhoan', $user->id_taikhoan)->first();

            if (!$khachhang) {
                return response()->json(['message' => 'Không tìm thấy thông tin khách hàng'], 404);
            }

            return Benhan::whereHas('hosobenhan', function ($q) use ($khachhang) {
                $q->where('id_khachhang', $khachhang->id_khachhang);
            })->with(['hosobenhan', 'khoa'])->get();
        }

        if (!in_array($user->nhanvien->chucvu ?? null, ['bacsi', 'dieuduong'])) {
            return response()->json(['message' => 'Không có quyền truy cập'], 403);
        }

        // Bác sĩ chỉ xem bệnh án thuộc khoa của mình
        return Benhan::where('id_khoa', $user->nhanvien->id_khoa)
                     ->with(['hosobenhan', 'khoa'])
                     ->get();
    }

    /**
     * 📌 Tạo bệnh án (bác sĩ)
     */
   public function store(Request $request)
{
    $user = Auth::user()->load('nhanvien');

    if ($user->nhanvien->chucvu !== 'bacsi') {
        return response()->json(['message' => 'Chỉ bác sĩ được phép tạo'], 403);
    }

    if (!$user->nhanvien->id_khoa) {
        return response()->json(['message' => 'Bác sĩ chưa được phân công khoa'], 422);
    }

    $validated = $request->validate([
        'id_hosobenhan' => 'required|exists:hosobenhan,id_hosobenhan',
        'chandoan'      => 'required|string',
        'mota'          => 'required|string',
        'ngaybatdau'    => 'nullable|date',
    ]);

    $benhan = Benhan::create([
        ...$validated,
        'id_khoa'      => $user->nhanvien->id_khoa,
        'id_nhanvien'  => $user->nhanvien->id_nhanvien,
    ]);

    $benhan->load(['khoa', 'nhanvien', 'hosobenhan']);

    LogService::log('Tạo bệnh án ID: ' . $benhan->id_benhan, 'benhan');

    return response()->json($benhan, 201);
}

    /**
     * 📌 Xem chi tiết bệnh án
     */
    public function show($id)
{
    $benhan = Benhan::with(['hosobenhan', 'thongtinkhambenh', 'khoa', 'nhanvien'])
        ->findOrFail($id);

    $user = Auth::user();

    if ($user->loai_taikhoan === 'khachhang') {
        // Lấy khách hàng qua quan hệ
        $khachhang = $user->nguoidung;

        if (!$khachhang) {
            return response()->json(['message' => 'Không tìm thấy thông tin khách hàng'], 404);
        }

        if (!$benhan->hosobenhan || $benhan->hosobenhan->id_khachhang !== $khachhang->id_khachhang) {
            return response()->json(['message' => 'Bạn không được phép xem bệnh án này'], 403);
        }

    } elseif (!in_array($user->nhanvien->chucvu ?? null, ['bacsi', 'dieuduong'])) {
        return response()->json(['message' => 'Không có quyền truy cập'], 403);
    }

    return response()->json($benhan);
}

    /**
     * 📌 Cập nhật bệnh án (bác sĩ hoặc điều dưỡng)
     */
   public function update(Request $request, $id)
{
    $user = Auth::user()->load('nhanvien');

    if (!in_array($user->nhanvien->chucvu ?? null, ['bacsi', 'dieuduong'])) {
        return response()->json(['message' => 'Không có quyền cập nhật'], 403);
    }

    $benhan = Benhan::with(['khoa', 'nhanvien'])->findOrFail($id);

    if ($benhan->id_khoa !== $user->nhanvien->id_khoa) {
        return response()->json(['message' => 'Bạn không được phép chỉnh bệnh án khoa khác'], 403);
    }

    $request->validate([
        'chandoan'   => 'required|string',
        'mota'       => 'required|string',
        'ngaybatdau' => 'nullable|date',
    ]);

    $benhan->update($request->only(['chandoan', 'mota', 'ngaybatdau']));

    LogService::log('Cập nhật bệnh án ID: ' . $id, 'benhan');

    return response()->json($benhan);
}


    /**
     * ❌ Không hỗ trợ xoá
     */
    public function destroy($id)
    {
        return response()->json(['message' => 'Không hỗ trợ xoá bệnh án'], 405);
    }
}
