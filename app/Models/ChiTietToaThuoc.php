<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietToaThuoc extends Model
{
    use HasFactory;

    protected $table = 'chi_tiet_toa_thuoc';
    protected $primaryKey = 'id_chitiettoathuoc';

    protected $fillable = [
        'id_toathuoc',
        'ten',
        'so_luong',
        'don_vi_tinh',
        'cach_dung',
    ];

    public function toaThuoc()
    {
        return $this->belongsTo(ToaThuoc::class, 'id_toathuoc', 'id_toathuoc');
    }
}
