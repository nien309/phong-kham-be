<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsTable extends Migration
{
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
        $table->id('id_log');
        $table->string('tenhanhdong');
        $table->timestamp('thoigianthuchien')->default(DB::raw('CURRENT_TIMESTAMP'));
        $table->string('tenbangthuchien');

        // Liên kết đến taikhoan.id_taikhoan
        $table->unsignedBigInteger('id_taikhoan');
        $table->foreign('id_taikhoan')->references('id_taikhoan')->on('taikhoan')->onDelete('cascade');

        $table->timestamps();
});

    }

    public function down()
    {
        Schema::dropIfExists('logs');
    }
}
