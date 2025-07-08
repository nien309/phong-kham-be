<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThongtinkhambenhTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('thongtinkhambenh', function (Blueprint $table) {
        $table->id('id_thongtinkhambenh');
        $table->unsignedBigInteger('id_benhan');
        $table->text('trieuchung')->nullable();
        $table->dateTime('ngaykham');
        $table->text('chandoan')->nullable();
        $table->string('trangthai')->default('dang_kham');
        $table->timestamps();
        $table->softDeletes();

        $table->foreign('id_benhan')->references('id_benhan')->on('benhan')->onDelete('cascade');
    });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('thongtinkhambenh');
    }
}
