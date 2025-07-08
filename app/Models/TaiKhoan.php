<?php


namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaiKhoan extends Authenticatable
{
    use HasApiTokens;
     use SoftDeletes;
    protected $table = 'taikhoan';
    protected $primaryKey = 'id_taikhoan';

    protected $fillable = [
    'hoten', 'matkhau', 'sdt', 'email', 'diachi',
    'gioitinh', 'ngaysinh', 'trangthai', 'loai_taikhoan', 'phan_quyen', 'id_nguoidung'
];

    protected $hidden = ['matkhau'];
public function nhanvien()
{
    return $this->belongsTo(NhanVien::class, 'id_nguoidung');
}

  public function nguoidung()
{
    if ($this->loai_taikhoan === 'khachhang') {
        return $this->belongsTo(KhachHang::class, 'id_nguoidung');
    }

    if ($this->loai_taikhoan === 'nhanvien') {
        return $this->belongsTo(NhanVien::class, 'id_nguoidung');
    }

    // ✅ Quan trọng: luôn trả về quan hệ trống để tránh lỗi
    return $this->belongsTo(NhanVien::class, 'id_nguoidung')->whereNull('id_nhanvien'); // luôn trả về query rỗng
}

}

