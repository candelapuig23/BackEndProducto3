<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferTipoReserva extends Model
{
    use HasFactory;

    protected $table = 'transfer_tipo_reserva'; // Nombre de la tabla
    protected $primaryKey = 'id_tipo_reserva'; // Clave primaria
    public $timestamps = true; // Habilitar created_at y updated_at

    protected $fillable = [
        'Descripcion',
    ];

    // Relación con TransferReserva
    public function reservas()
    {
        return $this->hasMany(TransferReserva::class, 'id_tipo_reserva', 'id_tipo_reserva');
    }
}
