<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = 'logs';
    protected $primaryKey = 'id_log';
    protected $fillable = [
        'tenhanhdong',
        'thoigianthuchien',
        'tenbangthuchien',
        'id_taikhoan'
    ];

    public $timestamps = true;

    public function taikhoan()
    {
        return $this->belongsTo(TaiKhoan::class, 'id_taikhoan', 'id_taikhoan');
    }
}

