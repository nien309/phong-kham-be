<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Benhan extends Model
{
    protected $table = 'benhan';
    protected $primaryKey = 'id_benhan';

    protected $fillable = [
        'id_hosobenhan',
        'chandoan',
        'mota',
        'ngaybatdau',
        'id_khoa',
        'id_nhanvien',
    ];

    public function hosobenhan()
    {
        return $this->belongsTo(HosoBenhAn::class, 'id_hosobenhan');
    }

    public function thongtinkhambenh()
    {
        return $this->hasMany(ThongTinKhamBenh::class, 'id_benhan');
    }
    public function khoa()
    {
        return $this->belongsTo(Khoa::class, 'id_khoa', 'id_khoa');
    }
    public function nhanvien()
    {
        return $this->belongsTo(NhanVien::class, 'id_nhanvien', 'id_nhanvien');
    }

}

