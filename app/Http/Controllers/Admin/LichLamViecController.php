<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\LichLamViec;
use Illuminate\Http\Request;

class LichLamViecController extends Controller
{
    public function index()
{
    $user = auth()->user();
    $nguoidung = $user->nguoidung;

    // Nếu là admin → xem toàn bộ lịch
    if ($user->loai_taikhoan === 'admin') {
        return LichLamViec::with('nhanvien')->get();
    }

    // Nếu là nhân viên → chỉ xem lịch của chính mình
    if ($user->loai_taikhoan === 'nhanvien' && $nguoidung) {
        return LichLamViec::with('nhanvien')
            ->where('id_nhanvien', $nguoidung->id_nhanvien)
            ->get();
    }

    return response()->json(['error' => 'Không có quyền truy cập'], 403);
}

    public function tim(Request $request)
    {
        $query = LichLamViec::query();

        if ($request->has('id_nhanvien')) {
            $query->where('id_nhanvien', $request->input('id_nhanvien'));
        }

        if ($request->has('ngay')) {
            $query->whereDate('ngaytao', $request->input('ngay'));
        }

        if ($request->has('thangnam')) {
            $thangnam = $request->input('thangnam'); // dạng: "07-2025"
            [$thang, $nam] = explode('-', $thangnam);
            $query->whereMonth('ngaytao', $thang)->whereYear('ngaytao', $nam);
        }

        return $query->with('nhanvien')->get();
    }
}
