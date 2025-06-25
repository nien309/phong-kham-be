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
    'gioitinh', 'ngaysinh', 'trangthai', 'loai_taikhoan', 'id_nguoidung'
];

    protected $hidden = ['matkhau'];

    public function nguoidung()
    {
        return match ($this->loai_taikhoan) {
            'khachhang' => $this->belongsTo(KhachHang::class, 'id_nguoidung'),
            'nhanvien' => $this->belongsTo(NhanVien::class, 'id_nguoidung'),
            default => null,
        };
    }
}

