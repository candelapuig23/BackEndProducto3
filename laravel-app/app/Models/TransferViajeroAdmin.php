<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Cambiar la extensión a Authenticatable

class TransferViajeroAdmin extends Authenticatable
{
    use HasFactory;

    protected $table = 'transfer_viajeros_admin'; // Nombre de la tabla
    protected $primaryKey = 'id_viajero_admin'; // Clave primaria
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

    // Método adaptado para registrar un administrador
    public static function registrarAdmin($data)
    {
        $data['password'] = bcrypt($data['password']); // Cifrar contraseña
        return self::create($data);
    }

    // Método para obtener administrador por email
    public static function getUserByEmail($email)
    {
        return self::where('email', $email)->first();
    }
}
