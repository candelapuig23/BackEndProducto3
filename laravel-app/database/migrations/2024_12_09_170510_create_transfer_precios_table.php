<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransferPreciosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('transfer_precios', function (Blueprint $table) {
    $table->increments('id_precios'); // Clave primaria
    $table->unsignedInteger('id_vehiculo'); // Relación con transfer_vehiculo
    $table->unsignedBigInteger('id_hotel'); // Relación con transfer_hotel
    $table->integer('Precio'); // Columna Precio
    $table->timestamps(); // created_at y updated_at

    // Relaciones
    $table->foreign('id_vehiculo')->references('id_vehiculo')->on('transfer_vehiculo')->onDelete('cascade');
    $table->foreign('id_hotel')->references('id_hotel')->on('transfer_hotel')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transfer_precios');
    }
}

