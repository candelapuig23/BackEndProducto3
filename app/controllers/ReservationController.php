<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransferReserva;
use App\Models\TransferViajero;
use App\Models\TransferHotel;

class ReservationController extends Controller
{
    // Método que maneja la creación de una reserva
    public function store(Request $request)
    {
        // Validar los datos del formulario
        $validated = $request->validate([
            'trayecto' => 'required|string',
            'diaLlegada' => 'nullable|date',
            'horaLlegada' => 'nullable|date_format:H:i',
            'numeroVuelo' => 'nullable|string|max:50',
            'aeropuertoOrigen' => 'nullable|string|max:50',
            'diaVuelo' => 'nullable|date',
            'horaVuelo' => 'nullable|date_format:H:i',
            'numeroVueloRegreso' => 'nullable|string|max:50',
            'horaRecogida' => 'nullable|date_format:H:i',
            'hotelDestino' => 'required|integer|exists:transfer_hotel,id_hotel',
            'numViajeros' => 'required|integer|min:1',
            'email' => 'required|email',
            'nombre' => 'required|string',
        ]);

        // Crear o buscar el usuario
        $usuario = TransferViajero::firstOrCreate(
            ['email' => $validated['email']],
            [
                'nombre' => $validated['nombre'],
                'direccion' => 'Dirección predeterminada', // Campo requerido en la tabla, puedes adaptarlo según sea necesario
                'codigoPostal' => '00000', // Valor predeterminado
                'ciudad' => 'Ciudad predeterminada',
                'pais' => 'País predeterminado',
                'password' => bcrypt('password_predeterminado'), // Genera una contraseña por defecto
            ]
        );

        // Crear la reserva
        $reserva = TransferReserva::create([
            'localizador' => uniqid('LOC-'),
            'id_hotel' => $validated['hotelDestino'],
            'id_tipo_reserva' => $validated['trayecto'] === 'ambos' ? 3 : 1, // Lógica para determinar el tipo de reserva
            'email_cliente' => $validated['email'],
            'fecha_reserva' => now(),
            'num_viajeros' => $validated['numViajeros'],
            'id_destino' => $validated['hotelDestino'], // Puedes ajustar esta lógica según tus necesidades
        ]);

        return response()->json([
            'message' => 'Reserva creada correctamente',
            'reserva' => $reserva,
        ], 201);
    }

    // Obtener todas las reservas de un usuario
    public function getUserReservations($email)
    {
        $reservas = TransferReserva::where('email_cliente', $email)
            ->with(['hotel', 'tipoReserva'])
            ->get();

        return response()->json($reservas, 200);
    }

    // Obtener los detalles de una reserva específica
    public function getReservationDetails($id)
    {
        $reserva = TransferReserva::with(['hotel', 'tipoReserva'])
            ->find($id);

        if (!$reserva) {
            return response()->json(['message' => 'Reserva no encontrada'], 404);
        }

        return response()->json($reserva, 200);
    }

    // Cancelar una reserva
    public function cancelReservation($id)
    {
        $reserva = TransferReserva::find($id);

        if (!$reserva) {
            return response()->json(['message' => 'Reserva no encontrada'], 404);
        }

        $reserva->delete();

        return response()->json(['message' => 'Reserva cancelada correctamente'], 200);
    }

    // Listar todas las reservas
    public function index()
    {
        // Obtener todas las reservas con relaciones
        $reservas = TransferReserva::with(['hotel', 'tipoReserva'])->get();

        return response()->json($reservas, 200);
    }
}
