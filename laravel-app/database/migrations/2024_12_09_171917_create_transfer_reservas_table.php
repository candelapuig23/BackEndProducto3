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
        Schema::create('transfer_reservas', function (Blueprint $table) {
            $table->id('id_reserva'); // Clave primaria
            $table->string('localizador', 100); // Código único de reserva
            $table->unsignedBigInteger('id_hotel')->nullable()->comment('Es el hotel que realiza la reserva'); // Relación con transfer_hotel
            $table->unsignedInteger('id_tipo_reserva'); // Relación con transfer_tipo_reserva
            $table->string('email_cliente', 100); // Email del cliente que realiza la reserva
            $table->datetime('fecha_reserva'); // Fecha y hora en que se realiza la reserva
            $table->datetime('fecha_modificacion')->nullable(); // Fecha y hora de la última modificación
            $table->unsignedBigInteger('id_destino'); // Relación con la tabla de destinos
            $table->date('fecha_entrada')->nullable(); // Fecha de entrada
            $table->time('hora_entrada')->nullable(); // Hora de entrada
            $table->string('numero_vuelo_entrada', 50)->nullable(); // Número de vuelo de entrada
            $table->string('origen_vuelo_entrada', 50)->nullable(); // Origen del vuelo de entrada
            $table->time('hora_vuelo_salida')->nullable(); // Hora del vuelo de salida
            $table->date('fecha_vuelo_salida')->nullable(); // Fecha del vuelo de salida
            $table->integer('num_viajeros'); // Número de viajeros
            $table->unsignedInteger('id_vehiculo'); // Relación con la tabla de vehículos

            // Llaves foráneas
            $table->foreign('id_hotel')->references('id_hotel')->on('transfer_hotel')->onDelete('cascade');
            $table->foreign('id_tipo_reserva')->references('id_tipo_reserva')->on('transfer_tipo_reserva')->onDelete('cascade');
            $table->foreign('id_destino')->references('id_zona')->on('transfer_zona')->onDelete('cascade');
            $table->foreign('id_vehiculo')->references('id_vehiculo')->on('transfer_vehiculo')->onDelete('cascade');

            $table->timestamps(); // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transfer_reservas');
    }
};
