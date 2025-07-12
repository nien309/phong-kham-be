<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTrangthaiToChidinhTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::table('chidinh', function (Blueprint $table) {
            $table->enum('trangthai', ['chờ thực hiện', 'đang thực hiện', 'hoàn thành'])
                ->default('chờ thực hiện');
            $table->dateTime('ngaythuchien')->nullable();
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chidinh', function (Blueprint $table) {
    $table->dropColumn('trangthai');
    $table->dropColumn('ngaythuchien');
});

    }
}
