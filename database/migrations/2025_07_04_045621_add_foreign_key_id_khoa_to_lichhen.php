<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyIdKhoaToLichhen extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lichhen', function (Blueprint $table) {
            // THÊM cột nếu chưa có
            if (!Schema::hasColumn('lichhen', 'id_khoa')) {
             $table->unsignedBigInteger('id_khoa')->nullable()->after('id_nhanvien');
  
            }

            // TẠO foreign key
            $table->foreign('id_khoa')->references('id_khoa')->on('khoas')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('lichhen', function (Blueprint $table) {
            $table->dropForeign(['id_khoa']);
            $table->dropColumn('id_khoa');
        });
    }
}
