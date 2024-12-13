<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferVehiculo extends Model
{
    use HasFactory;

    protected $table = 'transfer_vehiculo'; // Nombre de la tabla
    protected $primaryKey = 'id_vehiculo'; // Clave primaria
    public $timestamps = true; // Habilitar created_at y updated_at

    protected $fillable = [
        'Descripcion',
        'email_conductor',
        'password',
    ];

    // Relación con TransferReserva
    public function reservas()
    {
        return $this->hasMany(TransferReserva::class, 'id_vehiculo', 'id_vehiculo');
    }

    public static function getVehiculos()
{
    return \App\Models\TransferVehiculo::select('id_vehiculo', 'descripcion')->get();
}

}
