<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KhachHang extends Model
{
    protected $table = 'khach_hangs';
    protected $primaryKey = 'id_khachhang';
    protected $fillable = ['nghenghiep'];
    public $timestamps = true; // nếu có created_at, updated_at
}
