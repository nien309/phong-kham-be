<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\LichHen;
use App\Models\TaiKhoan; // ✅ Đúng vị trí
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\LogService;
use App\Models\NhanVien;
use App\Models\LichLamViec;
use App\Models\CaKham;
use App\Models\Khoa;
class LichHenController extends Controller
{
    public function index()
    {
        return LichHen::with([
            'khachhang.taikhoan:id_taikhoan,id_nguoidung,hoten',
            'nhanvien.taikhoan:id_taikhoan,id_nguoidung,hoten',
            'cakham:id_cakham,khunggio'
        ])
        ->orderBy('ngayhen', 'desc')
        ->get();
    }


    public function lichHenCuaToi()
{
    /** @var TaiKhoan $user */
    $user = auth()->user();

    // Kiểm tra loại tài khoản
    if ($user->loai_taikhoan !== 'khachhang') {
        return response()->json(['error' => 'Tài khoản không phải khách hàng'], 403);
    }

    // Lấy khách hàng
    $khachhang = $user->nguoidung;

    if (!$khachhang) {
        return response()->json(['error' => 'Chưa liên kết khách hàng'], 400);
    }

    // Trả về lịch hẹn của chính khách hàng này
    $lichHen = LichHen::with([
        'khachhang.taikhoan:id_taikhoan,id_nguoidung,hoten',
        'nhanvien.taikhoan:id_taikhoan,id_nguoidung,hoten',
        'cakham:id_cakham,khunggio'
    ])
    ->where('id_khachhang', $khachhang->id_khachhang)
    ->orderBy('ngayhen', 'desc')
    ->get();

    return response()->json($lichHen);
}

    public function show($id)
    {
        return LichHen::with([
            'khachhang.taikhoan:id_taikhoan,id_nguoidung,hoten',
            'nhanvien.taikhoan:id_taikhoan,id_nguoidung,hoten',
            'cakham:id_cakham,khunggio'
        ])->findOrFail($id);
    }

    
    public function datLich(Request $request)
    {
        /** @var TaiKhoan $user */
        $user = auth()->user(); // đăng nhập trả về từ bảng `taikhoan`

        // Lấy id_khachhang từ quan hệ
        $khachhang = $user->nguoidung;
        if (!$khachhang || !$user->loai_taikhoan === 'khachhang') {
            return response()->json(['error' => 'Tài khoản không phải khách hàng hoặc chưa liên kết khách hàng.'], 400);
        }

        $validated = $request->validate([
            'id_nhanvien' => 'required|exists:nhan_viens,id_nhanvien',
            'id_khoa' => 'required|exists:khoas,id_khoa',
            'id_cakham' => 'required|exists:cakham,id_cakham',
            'ngayhen' => 'required|date',
            'ghichu' => 'nullable|string',
        ]);

        $validated['id_khachhang'] = $khachhang->id_khachhang;
        $validated['trangthai'] = 'chờ xác nhận';

        // Kiểm tra bác sĩ đó có lịch làm việc đúng ngày & ca không?
        $lichBacSi = LichLamViec::where('id_nhanvien', $validated['id_nhanvien'])->get();

        $hopLe = false;

        foreach ($lichBacSi as $lich) {
            $json = json_decode($lich->thoigianlamviec, true);
            foreach ($json as $ngayCa) {
                if ($ngayCa['ngay'] === $validated['ngayhen'] && in_array($validated['id_cakham'], $ngayCa['ca'])) {
                    $hopLe = true;
                    break 2;
                }
            }
        }

        if (!$hopLe) {
            return response()->json(['error' => 'Bác sĩ không có lịch làm việc phù hợp.'], 422);
        }

                return LichHen::create($validated);
            }


    
    public function taoLich(Request $request)
    {
            $validated = $request->validate([
        'id_khachhang' => 'required|exists:khach_hangs,id_khachhang',
        'id_nhanvien' => 'required|exists:nhan_viens,id_nhanvien',
        'id_khoa' => 'required|exists:khoas,id_khoa',
        'id_cakham' => 'required|exists:cakham,id_cakham',
        'ngayhen' => 'required|date',
        'ghichu' => 'nullable|string',
    ]);


        $validated['trangthai'] = 'đã xác nhận';

        return LichHen::create($validated);
    }

   
    public function huyLich($id)
    {
        $lichhen = LichHen::findOrFail($id);
        $lichhen->update(['trangthai' => 'đã huỷ']);

        return response()->json(['message' => 'Đã huỷ lịch hẹn']);
    }

  
    public function capNhatTrangThai(Request $request, $id)
    {
        $lichhen = LichHen::findOrFail($id);

        $validated = $request->validate([
            'trangthai' => 'required|in:chờ xác nhận,đã xác nhận,chuyển đến bác sĩ,chuyển đến lễ tân,hoàn thành,đã huỷ',
        ]);

        $lichhen->update(['trangthai' => $validated['trangthai']]);

        return $lichhen;
    }
    

public function layBacSiTheoLichRanh(Request $request, $id_khoa)
{
    $ngayhen = $request->input('ngayhen');
    $id_cakham = $request->input('id_cakham');

    // Validate input
    if (!$ngayhen || !$id_cakham) {
        return response()->json(['error' => 'Thiếu ngày hẹn hoặc ca khám'], 400);
    }

    // Lấy tất cả bác sĩ thuộc khoa + nạp sẵn tài khoản
    $dsBacSi = NhanVien::where('id_khoa', $id_khoa)
        ->with('taikhoan')
        ->get();

    $dsBacSiRanh = [];

    foreach ($dsBacSi as $bacsi) {
        // Lấy lịch làm việc đang làm
        $lichLamViec = LichLamViec::where('id_nhanvien', $bacsi->id_nhanvien)
            ->where('trangthai', 'đang làm')
            ->get();

        foreach ($lichLamViec as $lich) {
            $thoigianLamViec = json_decode($lich->thoigianlamviec, true);

            if (!is_array($thoigianLamViec)) {
                continue;
            }

            foreach ($thoigianLamViec as $ngayCa) {
                if (
                    isset($ngayCa['ngay']) && 
                    $ngayCa['ngay'] === $ngayhen && 
                    isset($ngayCa['ca']) && 
                    is_array($ngayCa['ca']) && 
                    in_array($id_cakham, $ngayCa['ca'])
                ) {
                    // Thêm thông tin có hoten
                    $dsBacSiRanh[] = [
                        'id_nhanvien' => $bacsi->id_nhanvien,
                        'taikhoan' => ['hoten'=>optional($bacsi->taikhoan)->hoten],
                        'chucvu' => $bacsi->chucvu,
                        'id_khoa' => $bacsi->id_khoa,
                    ];
                    break 2;
                }
            }
        }
    }

    // Log kết quả
    Log::info('Available doctors found', ['count' => count($dsBacSiRanh)]);

    return response()->json(array_values($dsBacSiRanh));
}

}

