<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLichDangKyLamViecTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lich_dang_ky_lam_viec', function (Blueprint $table) {
        $table->bigIncrements('id_dangky');
        $table->unsignedBigInteger('id_nhanvien');
        $table->string('thangnam');
        $table->json('thoigiandangky');
        $table->enum('trangthai', ['chờ duyệt', 'đã duyệt'])->default('chờ duyệt');
        $table->text('ghichu')->nullable();
        $table->softDeletes();
        $table->timestamps();

        $table->foreign('id_nhanvien')->references('id_nhanvien')->on('nhan_viens')->onDelete('cascade');
    });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lich_dang_ky_lam_viec');
    }
}
