<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHoadonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hoadon', function (Blueprint $table) {
        $table->id('id_hoadon');
        $table->unsignedBigInteger('id_thongtinkhambenh');
        $table->dateTime('ngaytao')->default(now());
        $table->enum('trangthai', ['cho_thanh_toan', 'da_thanh_toan', 'huy'])->default('cho_thanh_toan');
        $table->string('hinhthucthanhtoan')->default('tien_mat');
        $table->timestamps();
        $table->softDeletes();

        $table->foreign('id_thongtinkhambenh')->references('id_thongtinkhambenh')->on('thongtinkhambenh')->onDelete('cascade');
    });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hoadon');
    }
}
