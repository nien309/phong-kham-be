<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftdeletesToCakhamAndLichhenTables extends Migration
{
    public function up()
    {
        Schema::table('cakham', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('lichhen', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::table('cakham', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('lichhen', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
