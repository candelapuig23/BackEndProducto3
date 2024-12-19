<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferPrecios extends Model
{
    use HasFactory;

    // Nombre de la tabla en la base de datos
    protected $table = 'transfer_precios';

    // Permitir asignación masiva de estos campos
    protected $fillable = ['id_hotel', 'id_vehiculo', 'precio'];

    // Si la tabla usa timestamps (created_at y updated_at)
    public $timestamps = true;
}
