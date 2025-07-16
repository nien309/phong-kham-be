<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDongiaToChidinhTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chidinh', function (Blueprint $table) {
            $table->decimal('dongia',10,2)->nullable()->after('soluong');
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
            $table->dropColumn('dongia');
        });
    }
}
