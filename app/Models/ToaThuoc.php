<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ToaThuoc extends Model
{
    use SoftDeletes;

    protected $table = 'toathuoc';
    protected $primaryKey = 'id_toathuoc';

    protected $fillable = [
        'id_thongtinkhambenh',
        'chandoan',
        'ngayketoa',
        'trangthai',
    ];

    public function thongtinkhambenh()
    {
        return $this->belongsTo(ThongTinKhamBenh::class, 'id_thongtinkhambenh');
    }
}
