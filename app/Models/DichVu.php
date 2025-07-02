<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DichVu extends Model
{
    protected $table = 'dich_vus';
    protected $primaryKey = 'id_dichvu';

    protected $fillable = [
        'tendichvu',
        'dongia',
        'trangthai',
        'ngaytao',
        'ngaycapnhat',
        'id_khoa',
    ];

    public $timestamps = false; // Vì bạn đang dùng ngaytao & ngaycapnhat thủ công

    // Quan hệ: Mỗi dịch vụ thuộc về 1 khoa
    public function khoa()
    {
        return $this->belongsTo(Khoa::class, 'id_khoa', 'id_khoa');
    }
}
