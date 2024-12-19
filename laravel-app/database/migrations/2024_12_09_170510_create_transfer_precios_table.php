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
        $table->id('id_precios'); // Clave primaria autoincremental
        $table->unsignedBigInteger('id_vehiculo'); // Relación con vehículos
        $table->unsignedBigInteger('id_hotel');    // Relación con hoteles
        $table->integer('precio')->default(50);    // Precio con valor por defecto

        $table->foreign('id_vehiculo')->references('id_vehiculo')->on('transfer_vehiculo')->onDelete('cascade');
        $table->foreign('id_hotel')->references('id_hotel')->on('tranfer_hotel')->onDelete('cascade');

        $table->timestamps(); // Campos created_at y updated_at
    });
}

public function down()
{
    Schema::dropIfExists('transfer_precios');
}

}