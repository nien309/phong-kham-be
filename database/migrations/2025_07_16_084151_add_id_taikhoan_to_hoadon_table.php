<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdTaikhoanToHoadonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('hoadon', function (Blueprint $table) {
        $table->unsignedBigInteger('id_taikhoan')->nullable()->after('id_thongtinkhambenh');
        $table->foreign('id_taikhoan')->references('id_taikhoan')->on('taikhoan')->onDelete('set null');
    });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hoadon', function (Blueprint $table) {
            //
        });
    }
}
