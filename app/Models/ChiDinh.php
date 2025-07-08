<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChiDinh extends Model
{
    use SoftDeletes;

    protected $table = 'chidinh';
    protected $primaryKey = 'id_chidinh';

    protected $fillable = [
        'id_thongtinkhambenh',
        'id_dichvu',
        'soluong',
        'dongia',
        'ketqua',
        'ngaychidinh',
        'hinhanh',
    ];

    public function thongtinkhambenh()
    {
        return $this->belongsTo(ThongTinKhamBenh::class, 'id_thongtinkhambenh');
    }

    public function dichvu()
    {
        return $this->belongsTo(DichVu::class, 'id_dichvu');
    }
}
