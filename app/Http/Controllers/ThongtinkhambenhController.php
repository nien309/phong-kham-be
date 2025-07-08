<?php

namespace App\Http\Controllers;

use App\Models\ThongTinKhamBenh;
use Illuminate\Http\Request;
use App\Services\LogService;
use Illuminate\Support\Facades\Auth;

class ThongTinKhamBenhController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!in_array($user->chucvu, ['bacsi', 'dieuduong'])) {
            return response()->json(['message' => 'Không có quyền truy cập'], 403);
        }

        return ThongTinKhamBenh::with(['benhan', 'chidinh', 'toathuoc', 'hoadon'])->get();
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->chucvu !== 'bacsi') {
            return response()->json(['message' => 'Chỉ bác sĩ được phép tạo'], 403);
        }

        $validated = $request->validate([
            'id_benhan' => 'required|exists:benhan,id_benhan',
            'trieuchung' => 'nullable|string',
            'ngaykham' => 'required|date',
            'chandoan' => 'nullable|string',
            'trangthai' => 'nullable|string',
        ]);

        $ttkb = ThongTinKhamBenh::create($validated);

        LogService::log('Tạo thông tin khám bệnh ID: ' . $ttkb->id_thongtinkhambenh, 'thongtinkhambenh');

        return response()->json($ttkb, 201);
    }

    public function show($id)
    {
        $ttkb = ThongTinKhamBenh::with(['chidinh', 'toathuoc', 'hoadon'])->findOrFail($id);
        $user = Auth::user();

        if (!in_array($user->chucvu, ['bacsi', 'dieuduong', 'khachhang'])) {
            return response()->json(['message' => 'Không có quyền truy cập'], 403);
        }

        return response()->json($ttkb);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->chucvu !== 'bacsi') {
            return response()->json(['message' => 'Chỉ bác sĩ được cập nhật'], 403);
        }

        $ttkb = ThongTinKhamBenh::findOrFail($id);

        $request->validate([
            'trieuchung' => 'nullable|string',
            'ngaykham' => 'nullable|date',
            'chandoan' => 'nullable|string',
            'trangthai' => 'nullable|string',
        ]);

        $ttkb->update($request->only(['trieuchung', 'ngaykham', 'chandoan', 'trangthai']));

        LogService::log('Cập nhật thông tin khám bệnh ID: ' . $id, 'thongtinkhambenh');

        return response()->json($ttkb);
    }

    // public function destroy($id)
    // {
    //     $user = Auth::user();
    //     if ($user->chucvu !== 'bacsi') {
    //         return response()->json(['message' => 'Chỉ bác sĩ được phép xoá'], 403);
    //     }

    //     $ttkb = ThongTinKhamBenh::findOrFail($id);
    //     $ttkb->delete();

    //     LogService::log('Xoá thông tin khám bệnh ID: ' . $id, 'thongtinkhambenh');

    //     return response()->json(['message' => 'Đã xoá thông tin khám bệnh']);
    // }
}
