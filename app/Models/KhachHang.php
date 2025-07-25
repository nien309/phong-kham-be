<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KhachHang extends Model
{
    use SoftDeletes;
    protected $table = 'khach_hangs';
    protected $primaryKey = 'id_khachhang';
    protected $fillable = ['nghenghiep'];
    public $timestamps = true; // nếu có created_at, updated_at
    public function taikhoan()
    {
        return $this->hasOne(TaiKhoan::class, 'id_nguoidung', 'id_khachhang')
                    ->where('loai_taikhoan', 'khachhang');
    }
    public function hosobenhan()
{
    return $this->hasOne(HoSoBenhAn::class, 'id_khachhang', 'id_khachhang');
}


}
