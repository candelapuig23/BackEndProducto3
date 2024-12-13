<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransferViajerosAdminTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_viajeros_admin', function (Blueprint $table) {
            $table->increments('id_viajero_admin'); // Clave primaria
            $table->string('nombre', 100); // Nombre del viajero administrador
            $table->string('apellido1', 100); // Primer apellido
            $table->string('apellido2', 100); // Segundo apellido
            $table->string('direccion', 100); // Dirección
            $table->string('codigoPostal', 10); // Código postal
            $table->string('ciudad', 100); // Ciudad
            $table->string('pais', 100); // País
            $table->string('email', 100)->unique(); // Email único
            $table->string('password', 100); // Contraseña
            $table->timestamps(); // Incluye created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transfer_viajeros_admin');
    }
}
