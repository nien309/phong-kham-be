<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ThongTinKhamBenh extends Model
{
    use SoftDeletes;

    protected $table = 'thongtinkhambenh';
    protected $primaryKey = 'id_thongtinkhambenh';

    protected $fillable = [
        'id_benhan',
        'trieuchung',
        'ngaykham',
        'chandoan',
        'trangthai',
    ];

    public function benhan()
    {
        return $this->belongsTo(Benhan::class, 'id_benhan');
    }

    public function chidinh()
    {
        return $this->hasMany(ChiDinh::class, 'id_thongtinkhambenh');
    }

    public function toathuoc()
    {
        return $this->hasOne(ToaThuoc::class, 'id_thongtinkhambenh');
    }

    public function hoadon()
    {
        return $this->hasOne(HoaDon::class, 'id_thongtinkhambenh');
    }
}
