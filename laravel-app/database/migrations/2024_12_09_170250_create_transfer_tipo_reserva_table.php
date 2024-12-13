<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransferTipoReservaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_tipo_reserva', function (Blueprint $table) {
            $table->increments('id_tipo_reserva'); // Clave primaria
            $table->string('Descripcion', 100); // Campo de descripción, longitud máxima 100 caracteres
            $table->timestamps(); // Incluye created_at y updated_at (opcional)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transfer_tipo_reserva');
    }
}
