<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdKhoaToBenhanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('benhan', function (Blueprint $table) {
            $table->unsignedBigInteger('id_khoa')->after('id_hosobenhan')->nullable();

            // Nếu có bảng `khoas` thì thêm foreign key:
            $table->foreign('id_khoa')->references('id_khoa')->on('khoas')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('benhan', function (Blueprint $table) {
            $table->dropForeign(['id_khoa']);
            $table->dropColumn('id_khoa');
        });
    }
}
