<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHosobenhanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hosobenhan', function (Blueprint $table) {
    $table->id('id_hosobenhan');
    $table->unsignedBigInteger('id_khachhang');
    $table->enum('trangthai', ['dang_dieu_tri', 'hoan_thanh', 'huy'])->default('dang_dieu_tri');
    $table->timestamps();
    $table->softDeletes();

    $table->foreign('id_khachhang')->references('id_khachhang')->on('khach_hangs')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hosobenhan');
    }
}
