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
    'id_taikhoan',
    'tongtien',
    'ngaytao',
    'hinhthucthanhtoan',
    'trangthai',
    'lydo_huy',
    ];


    public function thongtinkhambenh()
    {
        return $this->belongsTo(ThongTinKhamBenh::class, 'id_thongtinkhambenh');
    }
    
    public function taikhoan()
    {
        return $this->belongsTo(TaiKhoan::class, 'id_taikhoan');
    }

}