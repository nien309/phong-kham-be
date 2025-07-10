<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdNhanvienToBenhanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('benhan', function (Blueprint $table) {
            $table->unsignedBigInteger('id_nhanvien')->nullable()->after('id_khoa');

            // Nếu có bảng `nhanviens` thì tạo ràng buộc FK luôn:
            $table->foreign('id_nhanvien')->references('id_nhanvien')->on('nhan_viens')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('benhan', function (Blueprint $table) {
            $table->dropForeign(['id_nhanvien']);
            $table->dropColumn('id_nhanvien');
        });
    }
}
