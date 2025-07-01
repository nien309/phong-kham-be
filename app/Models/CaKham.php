<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaKham extends Model
{
    use SoftDeletes;

    protected $table = 'cakham';
    protected $primaryKey = 'id_cakham';

    protected $fillable = [
        'khunggio',
        'trangthai',
    ];

    protected $dates = ['deleted_at'];

    public function lichhens()
    {
        return $this->hasMany(LichHen::class, 'id_cakham', 'id_cakham');
    }
}
