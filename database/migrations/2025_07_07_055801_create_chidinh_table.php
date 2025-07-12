<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChidinhTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chidinh', function (Blueprint $table) {
        $table->id('id_chidinh');
        $table->unsignedBigInteger('id_thongtinkhambenh');
        $table->unsignedBigInteger('id_dichvu');
        $table->integer('soluong')->default(1);
        
        $table->text('ketqua')->nullable();
        $table->text('hinhanh')->nullable();
        $table->dateTime('ngaychidinh')->nullable();
        $table->timestamps();
        $table->softDeletes();

        $table->foreign('id_thongtinkhambenh')->references('id_thongtinkhambenh')->on('thongtinkhambenh')->onDelete('cascade');
        $table->foreign('id_dichvu')->references('id_dichvu')->on('dich_vus')->onDelete('cascade');
    });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chidinh');
    }
}
