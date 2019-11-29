<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBahanResepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bahan_resep', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_resep')->unsigned();
            $table->integer('id_bahan')->unsigned();
            $table->decimal('batch',10,5);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bahan_resep');
    }
}
