<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferHotel extends Model
{
    use HasFactory;

    protected $table = 'transfer_hotel'; // Nombre de la tabla
    protected $primaryKey = 'id_hotel'; // Clave primaria
    public $timestamps = true; // Habilitar created_at y updated_at

    protected $fillable = [
        'id_zona',
        'comision',
        'usuario',
        'password',
    ];

    // Relación con TransferZona
    public function zona()
    {
        return $this->belongsTo(TransferZona::class, 'id_zona', 'id_zona');
    }

    // Relación con TransferReserva
    public function reservas()
    {
        return $this->hasMany(TransferReserva::class, 'id_hotel', 'id_hotel');
    }
}
