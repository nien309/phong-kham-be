<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLichhenTable extends Migration
{
    public function up()
    {
        Schema::create('lichhen', function (Blueprint $table) {
            $table->id('id_lichhen');

            // Khóa ngoại
            $table->unsignedBigInteger('id_khachhang');
            $table->unsignedBigInteger('id_nhanvien');
            $table->unsignedBigInteger('id_cakham');
            

            // Nội dung lịch hẹn
            $table->date('ngayhen');
            $table->text('ghichu')->nullable();

            $table->enum('trangthai', [
                'chờ xác nhận',
                'đã xác nhận',
                'chuyển đến bác sĩ',
                'chuyển đến lễ tân',
                'hoàn thành',
                'đã huỷ'
            ])->default('chờ xác nhận');

            $table->timestamps();

            // Ràng buộc khóa ngoại
            $table->foreign('id_khachhang')->references('id_khachhang')->on('khach_hangs')->onDelete('cascade');
            $table->foreign('id_nhanvien')->references('id_nhanvien')->on('nhan_viens')->onDelete('cascade');
            $table->foreign('id_cakham')->references('id_cakham')->on('cakham')->onDelete('cascade');
             $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lichhen');
    }
}
