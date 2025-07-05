<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLichLamViecTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lich_lam_viec', function (Blueprint $table) {
        $table->id('id_lichlamviec');
        $table->unsignedBigInteger('id_nhanvien');
        $table->enum('trangthai', ['đang làm', 'nghỉ', 'thay đổi'])->default('đang làm');
        $table->timestamp('ngaytao')->useCurrent();
        $table->json('thoigianlamviec'); // Ví dụ: [{"ngay": "2025-07-10", "ca": [1,2]}]
        $table->boolean('is_dinhky')->default(false);
        $table->text('lydothaydoi')->nullable();

        // Quan hệ
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
        Schema::dropIfExists('lich_lam_viec');
    }
}
