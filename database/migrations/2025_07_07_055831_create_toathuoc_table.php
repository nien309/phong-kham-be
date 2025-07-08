<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateToathuocTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('toathuoc', function (Blueprint $table) {
        $table->id('id_toathuoc');
        $table->unsignedBigInteger('id_thongtinkhambenh');
        $table->text('chandoan')->nullable();
        $table->date('ngayketoa')->nullable();
        $table->string('trangthai')->default('moi');
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
        Schema::dropIfExists('toathuoc');
    }
}
