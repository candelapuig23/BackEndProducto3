<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Cambiar la extensión a Authenticatable

class TransferViajero extends Authenticatable
{
    use HasFactory;

    protected $table = 'transfer_viajeros'; // Nombre de la tabla
    protected $primaryKey = 'id_viajero'; // Clave primaria
    public $timestamps = true; // Habilita las columnas created_at y updated_at

    protected $fillable = [
        'nombre',
        'apellido1',
        'apellido2',
        'direccion',
        'codigoPostal',
        'ciudad',
        'pais',
        'email',
        'password',
    ];

    // Método adaptado para registrar un viajero
    public static function registrarViajero($data)
    {
        $data['password'] = bcrypt($data['password']); // Cifrar contraseña
        return self::create($data);
    }

    // Método para obtener usuario por email
    public static function getUserByEmail($email)
    {
        return self::where('email', $email)->first();
    }

    public function reservas()
{
    return $this->hasMany(TransferReserva::class, 'email_cliente', 'email');
}

}
