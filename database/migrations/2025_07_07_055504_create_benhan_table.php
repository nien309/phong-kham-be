<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBenhanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('benhan', function (Blueprint $table) {
        $table->id('id_benhan');
        $table->unsignedBigInteger('id_hosobenhan');
        $table->text('chandoan')->nullable();
        $table->text('mota')->nullable();
        $table->date('ngaybatdau')->nullable();
        $table->timestamps();
        $table->softDeletes();

        $table->foreign('id_hosobenhan')->references('id_hosobenhan')->on('hosobenhan')->onDelete('cascade');
    });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('benhan');
    }
}
