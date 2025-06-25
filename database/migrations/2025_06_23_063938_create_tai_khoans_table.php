<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaiKhoansTable extends Migration
{
    public $withinTransaction = false;
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taikhoan', function (Blueprint $table) {
        $table->id('id_taikhoan');
        $table->string('hoten');
        $table->string('matkhau');
        $table->string('gioitinh');
        $table->date('ngaysinh');
        $table->string('diachi');
        $table->string('sdt');
        $table->string('email')->unique();
        $table->enum('trangthai', ['active', 'inactive', 'suspended'])->default('active');
        $table->enum('phan_quyen', ['admin_hethong', 'admin_nhansu', 'khachhang', 'nhanvien'])->default('khachhang');

        $table->enum('loai_taikhoan', ['khachhang', 'nhanvien', 'admin']); // Phân loại
        $table->unsignedBigInteger('id_nguoidung')->nullable();   // Khóa ngoại mềm (chứa id_khachhang hoặc id_nhanvien)
        
        $table->timestamps();
        $table->softDeletes(); // thêm dòng này để hỗ trợ xóa mềm

});

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('taikhoan');
    }
}
