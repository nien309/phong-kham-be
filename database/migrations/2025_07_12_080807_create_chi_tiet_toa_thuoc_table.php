<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChiTietToaThuocTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chi_tiet_toa_thuoc', function (Blueprint $table) {
            $table->id('id_chitiettoathuoc');
            $table->unsignedBigInteger('id_toathuoc');
            $table->string('ten');
            $table->integer('so_luong'); 
            $table->string('don_vi_tinh');
            $table->text('cach_dung');
            $table->timestamps();

            $table->foreign('id_toathuoc')
                  ->references('id_toathuoc')
                  ->on('toathuoc')
                  ->onDelete('cascade'); // Xoá toa thuốc thì chi tiết cũng xoá theo
        });
    }

    public function down()
    {
        Schema::dropIfExists('chi_tiet_toa_thuoc');
    }
}
