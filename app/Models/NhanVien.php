<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NhanVien extends Model
{
    protected $table = 'nhan_viens'; // tên bảng nếu không giống tên model
    protected $primaryKey = 'id_nhanvien'; // Sửa lỗi thiếu cột id
    public $timestamps = true; // hoặc false nếu không có timestamps

    protected $fillable = ['chucvu', 'luong']; // thêm nếu cần fillable
    public function khoa()
{
    return $this->belongsTo(Khoa::class, 'id_khoa', 'id_khoa');
}

}
