<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LichLamViec extends Model
{
    protected $table = 'lich_lam_viec';
    protected $primaryKey = 'id_lichlamviec';
    public $timestamps = false;

    protected $fillable = [
        'id_nhanvien',
        'trangthai',
        'ngaytao',
        'thoigianlamviec',
        'is_dinhky',
        'lydothaydoi'
    ];

    protected $casts = [
        'thoigianlamviec' => 'array',
        'ngaytao' => 'datetime',
        'is_dinhky' => 'boolean',
    ];

    public function nhanvien()
    {
        return $this->belongsTo(NhanVien::class, 'id_nhanvien');
    }
}
