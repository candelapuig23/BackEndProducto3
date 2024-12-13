<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferZona extends Model
{
    use HasFactory;

    protected $table = 'transfer_zona'; // Nombre de la tabla
    protected $primaryKey = 'id_zona'; // Clave primaria
    public $timestamps = true; // Habilitar created_at y updated_at

    protected $fillable = [
        'descripcion',
    ];

    // Relación con TransferHotel
    public function hoteles()
    {
        return $this->hasMany(TransferHotel::class, 'id_zona', 'id_zona');
    }
}
