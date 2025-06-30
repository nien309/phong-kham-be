<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdKhoaToNhanViensTable extends Migration
{
    public function up()
    {
        Schema::table('nhan_viens', function (Blueprint $table) {
            $table->unsignedBigInteger('id_khoa')->nullable()->after('luong');

            $table->foreign('id_khoa')
                  ->references('id_khoa')
                  ->on('khoas')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('nhan_viens', function (Blueprint $table) {
            $table->dropForeign(['id_khoa']);
            $table->dropColumn('id_khoa');
        });
    }
}

