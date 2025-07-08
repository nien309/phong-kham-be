<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HosoBenhAn extends Model
{
    use SoftDeletes;

    protected $table = 'hosobenhan';
    protected $primaryKey = 'id_hosobenhan';

    protected $fillable = [
        'id_khachhang',
        'trangthai',
    ];

    public function khachhang()
    {
        return $this->belongsTo(KhachHang::class, 'id_khachhang');
    }

    public function benhans()
    {
        return $this->hasMany(Benhan::class, 'id_hosobenhan');
    }
}
