<?php



use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCakhamTable extends Migration
{
    public function up()
    {
        Schema::create('cakham', function (Blueprint $table) {
            $table->id('id_cakham');
            $table->enum('khunggio', ['sáng', 'chiều']);
            $table->enum('trangthai', ['đang hoạt động', 'đã tắt'])->default('đang hoạt động');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cakham');
    }
}
