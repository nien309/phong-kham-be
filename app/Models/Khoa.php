<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Khoa extends Model
{
    
    use SoftDeletes;
    protected $table = 'khoas';
    protected $primaryKey = 'id_khoa';
    protected $fillable = ['tenkhoa', 'trangthai'];

    public function nhanViens()
    {
        return $this->hasMany(NhanVien::class, 'id_khoa', 'id_khoa');
    }

    public function dichVus()
    {
        return $this->hasMany(DichVu::class, 'id_khoa', 'id_khoa');
    }
    
}
