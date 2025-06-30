<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDichVusTable extends Migration
{
    public function up()
    {
        Schema::create('dich_vus', function (Blueprint $table) {
            $table->id('id_dichvu');
            $table->float('dongia');
            $table->enum('trangthai', ['hoatdong', 'tamngung'])->default('hoatdong');

            $table->dateTime('ngaytao')->useCurrent();
            $table->dateTime('ngaycapnhat')->nullable()->useCurrentOnUpdate();

            $table->unsignedBigInteger('id_khoa')->nullable();
            $table->foreign('id_khoa')->references('id_khoa')->on('khoas')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('dich_vus');
    }
}
