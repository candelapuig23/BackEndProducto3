<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransferZonaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_zona', function (Blueprint $table) {
            $table->unsignedBigInteger('id_zona', true); // Clave primaria como unsignedBigInteger
            $table->string('descripcion', 100); // Descripción de la zona
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
        Schema::dropIfExists('transfer_zona');
    }
}
