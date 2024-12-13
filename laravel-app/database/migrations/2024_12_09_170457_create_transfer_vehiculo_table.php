<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransferVehiculoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_vehiculo', function (Blueprint $table) {
            $table->increments('id_vehiculo'); // Clave primaria
            $table->string('Descripcion', 100); // Descripción del vehículo, longitud máxima 100 caracteres
            $table->string('email_conductor', 100); // Email del conductor
            $table->string('password', 100); // Contraseña asociada
            $table->timestamps(); // Incluye columnas created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transfer_vehiculo');
    }
}
