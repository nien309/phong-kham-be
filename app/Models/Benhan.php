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
    ];

    public function hosobenhan()
    {
        return $this->belongsTo(HosoBenhAn::class, 'id_hosobenhan');
    }

    public function thongtinkhambenh()
    {
        return $this->hasMany(ThongTinKhamBenh::class, 'id_benhan');
    }
}

