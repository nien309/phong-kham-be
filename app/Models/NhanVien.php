<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NhanVien extends Model
{
    use SoftDeletes;

    protected $table = 'nhan_viens';
    protected $primaryKey = 'id_nhanvien';
    public $timestamps = true;

    protected $fillable = [
        'chucvu',
        'luong',
        'id_khoa',
        'id_taikhoan', // nếu có liên kết tài khoản
    ];

    // Quan hệ với bảng Khoa
    public function khoa()
    {
        return $this->belongsTo(Khoa::class, 'id_khoa', 'id_khoa');
    }

    // Quan hệ với bảng Tài Khoản (nếu có)
    public function taikhoan()
    {
        return $this->hasOne(TaiKhoan::class, 'id_nguoidung', 'id_nhanvien')
                    ->where('loai_taikhoan', 'nhanvien');
    }

    // Quan hệ với các lịch hẹn (nếu có)
    public function lichHen()
    {
        return $this->hasMany(LichHen::class, 'id_nhanvien', 'id_nhanvien');
    }
    public function getByKhoa($id_khoa)
    {
        $nhanviens = NhanVien::where('id_khoa', $id_khoa)->with('taikhoan', 'khoa')->get();

        return response()->json($nhanviens);
    }

}
