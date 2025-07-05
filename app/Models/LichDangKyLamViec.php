<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LichDangKyLamViec extends Model
{
    use SoftDeletes;

    protected $table = 'lich_dang_ky_lam_viec';
    protected $primaryKey = 'id_dangky';

    protected $fillable = [
        'id_nhanvien', 'thangnam', 'thoigiandangky', 'trangthai', 'ghichu'
    ];

    protected $casts = [
        'thoigiandangky' => 'array'
    ];

    public function nhanvien()
    {
        return $this->belongsTo(NhanVien::class, 'id_nhanvien');
    }
}
