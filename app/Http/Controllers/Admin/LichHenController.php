<?php

namespace App\Http\Controllers\Admin;

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
    $request->validate([
        'ngayhen' => 'required|date',
        'id_cakham' => 'required|exists:cakham,id_cakham',
    ]);

    $ngayhen = $request->input('ngayhen');
    $id_cakham = $request->input('id_cakham');

    // Lấy danh sách bác sĩ trong khoa
    $dsBacSi = NhanVien::where('id_khoa', $id_khoa)->get();

    $dsBacSiRanh = [];

    foreach ($dsBacSi as $bacsi) {
        // Lấy lịch làm việc của bác sĩ
        $lich = LichLamViec::where('id_nhanvien', $bacsi->id_nhanvien)->get();

        foreach ($lich as $lichlam) {
            $lichGiaiMa = json_decode($lichlam->thoigianlamviec, true);
            foreach ($lichGiaiMa as $ngayCa) {
                if ($ngayCa['ngay'] === $ngayhen && in_array($id_cakham, $ngayCa['ca'])) {
                    $dsBacSiRanh[] = $bacsi;
                    break 2; // tìm thấy bác sĩ hợp lệ rồi, qua bác sĩ khác
                }
            }
        }
    }

    return response()->json($dsBacSiRanh);
}

}