<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTrangthaiEnumInThongtinkhambenh extends Migration
{
    public function up()
    {
        Schema::table('thongtinkhambenh', function (Blueprint $table) {
            // Xoá cột cũ
            $table->dropColumn('trangthai');
        });

        Schema::table('thongtinkhambenh', function (Blueprint $table) {
            // Thêm lại cột dạng ENUM
            $table->enum('trangthai', ['dang_kham', 'da_hoan_thanh'])->default('dang_kham');
        });
    }

    public function down()
    {
        Schema::table('thongtinkhambenh', function (Blueprint $table) {
            $table->dropColumn('trangthai');
        });

        Schema::table('thongtinkhambenh', function (Blueprint $table) {
            $table->string('trangthai')->default('dang_kham');
        });
    }
}
