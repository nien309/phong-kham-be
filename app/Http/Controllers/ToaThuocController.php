<?php

namespace App\Http\Controllers;

use App\Models\ToaThuoc;
use Illuminate\Http\Request;
use App\Services\LogService;
use Illuminate\Support\Facades\Auth;

class ToaThuocController extends Controller
{
    // 🩺 Tạo toa thuốc (BÁC SĨ)
   public function store(Request $request)
{
    $user = Auth::user()->load('nhanvien');

    if (($user->nhanvien->chucvu ?? null) !== 'bacsi') {
        return response()->json(['message' => 'Chỉ bác sĩ được phép tạo toa thuốc'], 403);
    }

    $validated = $request->validate([
        'id_thongtinkhambenh' => 'required|exists:thongtinkhambenh,id_thongtinkhambenh',
        'chandoan' => 'required|string',
        'ngayketoa' => 'nullable|date',
    ]);

    // 📌 Kiểm tra TT khám bệnh có thuộc khoa của bác sĩ không
    $ttkb = \App\Models\ThongTinKhamBenh::with('benhan')->findOrFail($validated['id_thongtinkhambenh']);

    if (!$ttkb->benhan) {
        return response()->json(['message' => 'Không tìm thấy bệnh án của thông tin khám bệnh'], 404);
    }

    if ($ttkb->benhan->id_khoa !== $user->nhanvien->id_khoa) {
        return response()->json(['message' => 'Thông tin khám bệnh không thuộc khoa của bạn'], 403);
    }

    $toa = ToaThuoc::create($validated);

    \App\Services\LogService::log('Tạo toa thuốc ID: ' . $toa->id_toathuoc, 'toathuoc');

    return response()->json($toa, 201);
}

    // 🩺 Cập nhật toa thuốc (BÁC SĨ)
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->chucvu !== 'bacsi') {
            return response()->json(['message' => 'Chỉ bác sĩ được cập nhật toa thuốc'], 403);
        }

        $toa = ToaThuoc::findOrFail($id);

        $validated = $request->validate([
            'chandoan' => 'nullable|string',
            'ngayketoa' => 'nullable|date',
            'trangthai' => 'nullable|string', // Chỉ nếu bạn có cột này
        ]);

        $toa->update($validated);

        LogService::log('Cập nhật toa thuốc ID: ' . $id, 'toathuoc');

        return response()->json($toa);
    }

   public function show($id)
{
    $user = Auth::user()->load('nhanvien');

    // Load toa thuốc kèm thông tin khám bệnh và chi tiết
    $toa = ToaThuoc::with(['chiTietToaThuoc', 'thongtinkhambenh.benhan'])
                ->findOrFail($id);

    // Chỉ bác sĩ hoặc điều dưỡng được phép xem
    if (!in_array($user->nhanvien->chucvu ?? null, ['bacsi', 'dieuduong'])) {
        return response()->json(['message' => 'Không có quyền xem'], 403);
    }

    // Kiểm tra khoa
    if ($toa->thongtinkhambenh->benhan->id_khoa !== $user->nhanvien->id_khoa) {
        return response()->json(['message' => 'Không được phép xem toa thuốc khoa khác'], 403);
    }

    return response()->json($toa);
}


    // 🧑‍⚕️📋 Danh sách toa thuốc (BÁC SĨ & ĐIỀU DƯỠNG)
    public function index()
    {
        $user = Auth::user();
        if (!in_array($user->chucvu, ['bacsi', 'dieuduong'])) {
            return response()->json(['message' => 'Không có quyền xem danh sách'], 403);
        }

        $toathuocs = ToaThuoc::with('chiTietToaThuoc')->get();

        return response()->json($toathuocs);
    }

    // 🧑‍⚕️📄 Xuất toa thuốc (BÁC SĨ & ĐIỀU DƯỠNG)
    public function xuatToa($id)
    {
        $user = Auth::user();
        if (!in_array($user->chucvu, ['bacsi', 'dieuduong'])) {
            return response()->json(['message' => 'Không có quyền xuất toa'], 403);
        }

        $toa = ToaThuoc::with('chiTietToaThuoc')->findOrFail($id);

        // TODO: Xử lý xuất PDF tại đây
        return response()->json([
            'message' => 'Chức năng xuất toa đang phát triển',
            'toathuoc' => $toa
        ]);
    }
}
