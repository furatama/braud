<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order', function (Blueprint $table) {
            $table->increments('id');
            $table->string('no')->unique();
            $table->integer('id_customer')->unsigned();
            $table->enum('metode',['cash','credit']);
            $table->date('tanggal');
            $table->string('faktur')->nullable();
            $table->decimal('tunai',13,2);
            $table->text('keterangan')->nullable();
            $table->integer('id_user')->unsigned();

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
        Schema::dropIfExists('order');
    }
}
