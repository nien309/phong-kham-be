<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimestampsToNhanViensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
{
    Schema::table('nhan_viens', function (Blueprint $table) {
        $table->timestamps(); // thêm created_at và updated_at
    });
}

public function down()
{
    Schema::table('nhan_viens', function (Blueprint $table) {
        $table->dropTimestamps();
    });
}

}
