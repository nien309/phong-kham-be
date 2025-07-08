<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTrangthaiConstraintInLichDangKyLamViec extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Xoá constraint cũ nếu có
        DB::statement("ALTER TABLE lich_dang_ky_lam_viec DROP CONSTRAINT IF EXISTS lich_dang_ky_lam_viec_trangthai_check");

        // Thêm constraint mới có thêm 'từ chối'
        DB::statement("ALTER TABLE lich_dang_ky_lam_viec ADD CONSTRAINT lich_dang_ky_lam_viec_trangthai_check CHECK (trangthai IN ('chờ duyệt', 'đã duyệt', 'từ chối'))");
    }

    public function down()
    {
        // Trở về constraint cũ (không có 'từ chối')
        DB::statement("ALTER TABLE lich_dang_ky_lam_viec DROP CONSTRAINT IF EXISTS lich_dang_ky_lam_viec_trangthai_check");

        DB::statement("ALTER TABLE lich_dang_ky_lam_viec ADD CONSTRAINT lich_dang_ky_lam_viec_trangthai_check CHECK (trangthai IN ('chờ duyệt', 'đã duyệt'))");
    }
}
