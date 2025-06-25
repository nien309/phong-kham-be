<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtToTaikhoanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('taikhoan', function (Blueprint $table) {
        $table->softDeletes();
    });
}

public function down()
{
    Schema::table('taikhoan', function (Blueprint $table) {
        $table->dropSoftDeletes();
    });
}

}
