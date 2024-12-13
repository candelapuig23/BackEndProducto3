<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransferReserva;
use App\Models\TransferViajero;
use App\Models\TransferHotel;
use App\Models\TransferZona;
use App\Models\TransferVehiculo;
use App\Models\TransferTipoReserva;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    // Mostrar el formulario de reserva
    public function create()
    {
          // Obtener datos dinámicos para los selectores
    $tiposTrayecto = TransferTipoReserva::select('id_tipo_reserva', 'descripcion')->distinct()->get(); // Evitar duplicados
    $vehiculos = TransferVehiculo::select('id_vehiculo', 'descripcion')->get(); // Seleccionar columnas necesarias
    $zonas = TransferZona::select('id_zona', 'descripcion')->get();
    $hoteles = TransferHotel::select('id_hotel', 'usuario')->get();

    return view('reservations.make', compact('tiposTrayecto', 'vehiculos', 'zonas', 'hoteles'));
}

    // Procesar el formulario de reserva
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
            'idZona' => 'required|integer|exists:transfer_zona,id_zona',
            'idVehiculo' => 'required|integer|exists:transfer_vehiculo,id_vehiculo',
            'hotelDestino' => 'required|integer|exists:transfer_hotel,id_hotel',
            'numViajeros' => 'required|integer|min:1',
            'email' => 'required|email',
            'nombre' => 'required|string',
        ]);

        // Validar tiempo mínimo de 48 horas
        if ($request->trayecto !== 'ida y vuelta') {
            $fechaHora = strtotime($validated['diaLlegada'] . ' ' . $validated['horaLlegada']);
            if (($fechaHora - time()) < 48 * 3600) {
                return redirect()->back()->withErrors(['error' => 'No se puede realizar la reserva con menos de 48 horas de antelación.']);
            }
        }

        // Crear o buscar el usuario
        $usuario = TransferViajero::firstOrCreate(
            ['email' => $validated['email']],
            [
                'nombre' => $validated['nombre'],
                'direccion' => 'Dirección predeterminada',
                'codigoPostal' => '00000',
                'ciudad' => 'Ciudad predeterminada',
                'pais' => 'País predeterminado',
                'password' => bcrypt('password_predeterminado'),
            ]
        );

        // Determinar tipo de trayecto
        $idTipoReserva = TransferTipoReserva::where('descripcion', $validated['trayecto'])->first()->id_tipo_reserva;

        // Crear la reserva
        TransferReserva::create([
            'localizador' => uniqid('LOC-'),
            'id_hotel' => $validated['hotelDestino'],
            'id_tipo_reserva' => $idTipoReserva,
            'email_cliente' => $validated['email'],
            'fecha_reserva' => now(),
            'id_destino' => $validated['idZona'],
            'num_viajeros' => $validated['numViajeros'],
            'id_vehiculo' => $validated['idVehiculo'],
            'fecha_entrada' => $validated['diaLlegada'] ?? null,
            'hora_entrada' => $validated['horaLlegada'] ?? null,
            'numero_vuelo_entrada' => $validated['numeroVuelo'] ?? null,
            'origen_vuelo_entrada' => $validated['aeropuertoOrigen'] ?? null,
            'fecha_vuelo_salida' => $validated['diaVuelo'] ?? null,
            'hora_vuelo_salida' => $validated['horaVuelo'] ?? null,
        ]);

        return redirect()->route('reservations.create')->with('success', 'Reserva creada correctamente.');
    }

    public function adminDashboard()
{
    $reservations = TransferReserva::with(['hotel', 'tipoReserva'])->get();
    return view('admin.dashboard', compact('reservations'));
}


}
