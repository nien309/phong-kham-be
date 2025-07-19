<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaiKhoan;
use App\Models\KhachHang;
use App\Models\NhanVien;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
class AuthController extends Controller
{
    public function register(Request $request)
{
    if (\App\Models\TaiKhoan::where('email', $request->email)->exists()) {
        return response()->json(['message' => 'Email đã tồn tại'], 409);
    }

    if (\App\Models\TaiKhoan::where('sdt', $request->sdt)->exists()) {
        return response()->json(['message' => 'Số điện thoại đã tồn tại'], 409);
    }

    $validated = $request->validate([
        'hoten' => 'required|string',
        'matkhau' => 'required|string|min:6|confirmed',
        'sdt' => 'required',
        'email' => 'required|email',
    ]);

    // Mặc định là khách hàng
    $nguoidung = \App\Models\KhachHang::create([
        'nghenghiep' => $request->nghenghiep ?? '',
    ]);
    // if(Carbon::parse($request->ngaysinh)->diffInYears(now())<10){
    //      return response()->json(['message' => 'Khách hàng nhỏ hơn 10 tuổi không nhận'], 409);
    // }
    $taikhoan = \App\Models\TaiKhoan::create([
        'hoten' => $validated['hoten'],
        'matkhau' => bcrypt($validated['matkhau']),
        'sdt' => $validated['sdt'],
        'email' => $validated['email'],
        'gioitinh' => $request->gioitinh ?? 'khac',
        'ngaysinh' => $request->ngaysinh ?? now(),
        'diachi' => $request->diachi ?? '',
        'loai_taikhoan' => 'khachhang', // không để user chọn
        'phan_quyen' => 'khachhang',
        'id_nguoidung' => $nguoidung->getKey(),
    ]);

    return response()->json([
        'message' => 'Đăng ký thành công',
        'taikhoan' => $taikhoan
    ]);
}

public function login(Request $request)
{
    $request->validate([
        'email' => 'required|string',
        'matkhau' => 'required|string'
    ]);

    $taikhoan = TaiKhoan::where('email', $request->email)->first();

    if (!$taikhoan || !\Illuminate\Support\Facades\Hash::check($request->matkhau, $taikhoan->matkhau)) {
        return response()->json(['message' => 'Sai thông tin đăng nhập'], 401);
    }

    $token = $taikhoan->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'Đăng nhập thành công',
        'token' => $token,
        'taikhoan' => $taikhoan
    ]);
}
public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();

    return response()->json(['message' => 'Đã đăng xuất']);
}
public function update(Request $request)
{
    $taikhoan = auth()->user();

    $taikhoan->update($request->only([
        'hoten', 'email', 'sdt', 'diachi', 'gioitinh', 'ngaysinh'
    ]));

    // Cập nhật bảng phụ
    if ($taikhoan->loai_taikhoan === 'khachhang') {
        $taikhoan->nguoidung()->update([
            'nghenghiep' => $request->nghenghiep,
        ]);
    } elseif ($taikhoan->loai_taikhoan === 'nhanvien') {
        $taikhoan->nguoidung()->update([
            'chucvu' => $request->chucvu,
            'luong' => $request->luong,
        ]);
    }

    return response()->json(['message' => 'Cập nhật thành công']);
}
public function getUserInfo(Request $request)
{
    try {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'User not authenticated'
            ], 401);
        }

        $data = [
            'id_taikhoan' => $user->id_taikhoan,
            'hoten' => $user->hoten,
            'ngaysinh' => $user->ngaysinh,
            'gioitinh' => $user->gioitinh,
            'sdt' => $user->sdt,
            'email' => $user->email,
            'loai_taikhoan' =>$user->loai_taikhoan,
            'phan_quyen'=>$user->phan_quyen,
            'trangthai'=>$user->trangthai,
        ];

        // Nếu có quan hệ nhân viên thì lấy cột chucvu trực tiếp
        if ($user->nhanvien) {
            $data['nhanvien'] = [
                'id_nhanvien' => $user->nhanvien->id_nhanvien,
                'chucvu' => $user->nhanvien->chucvu, // đây là cột, không phải quan hệ
            ];
        }

        return response()->json($data, 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Something went wrong',
            'error' => $e->getMessage()
        ], 500);
    }
}

}
