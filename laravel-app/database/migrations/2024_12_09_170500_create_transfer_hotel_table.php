<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_hotel', function (Blueprint $table) {
            $table->id('id_hotel')->autoIncrement(); // ID del hotel como clave primaria
            $table->unsignedBigInteger('id_zona'); // ID de la zona (relación foránea)
            $table->integer('comision')->nullable(); // Comisión
            $table->string('usuario', 100); // Usuario
            $table->string('password', 100); // Contraseña
            $table->timestamps(); // Agrega columnas created_at y updated_at automáticamente

            // Llave foránea
            $table->foreign('id_zona')->references('id_zona')->on('transfer_zona')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transfer_hotel');
    }
};
