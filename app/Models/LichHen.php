<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LichHen extends Model
{
    use SoftDeletes;

    protected $table = 'lichhen';
    protected $primaryKey = 'id_lichhen';

    protected $fillable = [
        'id_khachhang',
        'id_nhanvien',
        'id_cakham',
        'id_khoa',
        'ngayhen',
        'ghichu',
        'trangthai',
    ];
    const TRANG_THAI = [
        'chờ xác nhận',
        'đã xác nhận',
        'chuyển đến bác sĩ',
        'chuyển đến lễ tân',
        'hoàn thành',
        'đã huỷ',
    ];

    protected $dates = ['deleted_at'];

    public function khachhang()
    {
        return $this->belongsTo(KhachHang::class, 'id_khachhang', 'id_khachhang');
    }

    public function nhanvien()
    {
        return $this->belongsTo(NhanVien::class, 'id_nhanvien', 'id_nhanvien');
    }

    public function cakham()
    {
        return $this->belongsTo(CaKham::class, 'id_cakham', 'id_cakham');
    }
    public function khoa()
    {
        return $this->belongsTo(Khoa::class, 'id_khoa', 'id_khoa');
    }

}
