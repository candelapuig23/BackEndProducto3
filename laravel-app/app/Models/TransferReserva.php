<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferReserva extends Model
{
    use HasFactory;

    protected $table = 'transfer_reservas'; // Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_reserva'; // Clave primaria
    public $timestamps = true; // Habilita las columnas created_at y updated_at

    protected $fillable = [
        'localizador',
        'id_hotel',
        'id_tipo_reserva',
        'email_cliente',
        'fecha_reserva',
        'fecha_modificacion',
        'id_destino',
        'fecha_entrada',
        'hora_entrada',
        'numero_vuelo_entrada',
        'origen_vuelo_entrada',
        'hora_vuelo_salida',
        'fecha_vuelo_salida',
        'num_viajeros',
        'id_vehiculo',
    ];

  // Relación con el modelo TransferVehiculo
public function vehiculo()
{
    return $this->belongsTo(TransferVehiculo::class, 'id_vehiculo', 'id_vehiculo');
}

// Relación con el modelo TransferHotel
public function hotel()
{
    return $this->belongsTo(TransferHotel::class, 'id_hotel', 'id_hotel');
}

// Relación con el modelo TransferTipoReserva
public function tipoReserva()
{
    return $this->belongsTo(TransferTipoReserva::class, 'id_tipo_reserva', 'id_tipo_reserva');
}



    // Relación con el modelo TransferViajero (usuario)
    public function usuario()
    {
        return $this->belongsTo(TransferViajero::class, 'email_cliente', 'email');
    }

    // Relación con el modelo TransferZona (destino)
    public function destino()
    {
        return $this->belongsTo(TransferZona::class, 'id_destino', 'id_zona');
    }

    // Métodos adicionales
    public static function getAllReservations()
    {
        return self::with(['hotel', 'tipoReserva', 'usuario', 'destino'])->get();
    }

    public static function getReservationDetailsById($id)
    {
        return self::with(['hotel', 'tipoReserva', 'usuario', 'destino'])->where('id_reserva', $id)->first();
    }

    public static function getZonas()
    {
        return \App\Models\TransferZona::select('id_zona', 'descripcion')->get();
    }

    public static function getVehiculos()
    {
        return \App\Models\TransferVehiculo::select('id_vehiculo', 'descripcion')->get();
    }

    public static function getTiposTrayecto()
    {
        return \App\Models\TransferTipoReserva::select('id_tipo_reserva', 'descripcion')->get();
    }

    public static function getHoteles()
    {
        return \App\Models\TransferHotel::select('id_hotel', 'usuario')->get();
    }

    public static function crearReserva($data)
    {
        return self::create($data);
    }

    public static function getReservasPorUsuario($email)
    {
        return self::where('email_cliente', $email)->with(['hotel', 'tipoReserva', 'destino'])->get();
    }
}
