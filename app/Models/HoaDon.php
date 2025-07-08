<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HoaDon extends Model
{
    use SoftDeletes;

    protected $table = 'hoadon';
    protected $primaryKey = 'id_hoadon';

    protected $fillable = [
        'id_thongtinkhambenh',
        'ngaytao',
        'trangthai',
        'hinhthucthanhtoan',
    ];

    public function thongtinkhambenh()
    {
        return $this->belongsTo(ThongTinKhamBenh::class, 'id_thongtinkhambenh');
    }
}