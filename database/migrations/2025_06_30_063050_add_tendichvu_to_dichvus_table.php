<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTendichvuToDichvusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
{
    Schema::table('dich_vus', function (Blueprint $table) {
        $table->string('tendichvu')->after('id_dichvu');
    });
}

public function down()
{
    Schema::table('dich_vus', function (Blueprint $table) {
        $table->dropColumn('tendichvu');
    });
}


}
