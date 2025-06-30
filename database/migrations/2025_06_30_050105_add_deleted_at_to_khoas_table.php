<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtToKhoasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('khoas', function (Blueprint $table) {
        $table->softDeletes(); // thêm cột deleted_at
    });
}

public function down()
{
    Schema::table('khoas', function (Blueprint $table) {
        $table->dropSoftDeletes();
    });
}

}
