<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResepDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resep_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_resep')->unsigned();
            $table->string('berat')->unique();
            $table->decimal('ratio',4,2);
            $table->integer('id_produk')->nullable()->unsigned();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('resep_detail');
    }
}
