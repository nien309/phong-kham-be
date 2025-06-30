<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKhoasTable extends Migration
{
    public function up()
    {
        Schema::create('khoas', function (Blueprint $table) {
            $table->id('id_khoa');
            $table->string('tenkhoa');
            $table->enum('trangthai', ['hoatdong', 'tamngung'])->default('hoatdong');
            $table->timestamps();
            $table->softDeletes(); // ở trong Schema::create()

        });
    }

    public function down()
    {
        Schema::dropIfExists('khoas');
    }
}
