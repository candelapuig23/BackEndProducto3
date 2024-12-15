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
    public function create()
    {
        $tiposTrayecto = TransferTipoReserva::select('id_tipo_reserva', 'descripcion')->distinct()->get();
        $vehiculos = TransferVehiculo::select('id_vehiculo', 'descripcion')->get();
        $zonas = TransferZona::select('id_zona', 'descripcion')->get();
        $hoteles = TransferHotel::select('id_hotel', 'usuario')->get();

        return view('reservations.make', compact('tiposTrayecto', 'vehiculos', 'zonas', 'hoteles'));
    }

    public function store(Request $request)
    {
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

        if ($request->trayecto !== 'ida y vuelta') {
            $fechaHora = strtotime($validated['diaLlegada'] . ' ' . $validated['horaLlegada']);
            if (($fechaHora - time()) < 48 * 3600) {
                return redirect()->back()->withErrors(['error' => 'No se puede realizar la reserva con menos de 48 horas de antelación.']);
            }
        }

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

        $idTipoReserva = TransferTipoReserva::where('descripcion', $validated['trayecto'])->first()->id_tipo_reserva;

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
    \Log::info("El método adminDashboard ha sido llamado correctamente.");

    $reservations = TransferReserva::with(['hotel', 'tipoReserva', 'vehiculo'])->get();

    dd($reservations); // Esto debería mostrar los datos y detener la ejecución.
    return view('admin.dashboard', compact('reservations'));
}




    public function edit($id)
    {
        $reservation = TransferReserva::with(['hotel', 'tipoReserva', 'vehiculo', 'destino'])->findOrFail($id);
        $hoteles = TransferHotel::all();
        $tiposTrayecto = TransferTipoReserva::all();
        $destinos = TransferZona::all();
        $vehiculos = TransferVehiculo::select('id_vehiculo', 'descripcion')->get();

        return view('reservations.edit_reservation', compact('reservation', 'hoteles', 'tiposTrayecto', 'destinos', 'vehiculos'));
    }


    //metodo update con registro de errores
   public function update(Request $request, $id)
{
    try {
        \Log::info("Inicio del método update para la reserva con ID: $id");

        $reservation = TransferReserva::findOrFail($id);
        \Log::info("Reserva encontrada: " . json_encode($reservation));

        // Ajustar el formato de hora_entrada antes de la validación
        if ($request->has('hora_entrada')) {
            $request->merge([
                'hora_entrada' => substr($request->input('hora_entrada'), 0, 5), // Solo HH:mm
            ]);
        }

        if ($request->has('hora_vuelo_salida')) {
            $request->merge([
                'hora_vuelo_salida' => substr($request->input('hora_vuelo_salida'), 0, 5),
            ]);
        }

        // Validar los datos del formulario
        $validated = $request->validate([
            'id_hotel' => 'required|integer|exists:transfer_hotel,id_hotel',
            'id_tipo_reserva' => 'required|integer|exists:transfer_tipo_reserva,id_tipo_reserva',
            'email_cliente' => 'required|email',
            'id_zona' => 'required|integer|exists:transfer_zona,id_zona',
            'fecha_entrada' => 'required|date',
            'hora_entrada' => 'required|date_format:H:i',
            'numero_vuelo_entrada' => 'nullable|string|max:50',
            'origen_vuelo_entrada' => 'nullable|string|max:50',
            'hora_vuelo_salida' => 'nullable|date_format:H:i',
            'fecha_vuelo_salida' => 'nullable|date',
            'num_viajeros' => 'required|integer|min:1',
            'id_vehiculo' => 'required|integer|exists:transfer_vehiculo,id_vehiculo',
        ]);
        \Log::info("Datos validados: " . json_encode($validated));

        // Actualizar los datos en la reserva
        $reservation->fill($validated);
        $reservation->id_destino = $request->input('id_zona');
        $reservation->save();
        \Log::info("Reserva actualizada correctamente.");

        return redirect()->route('admin.dashboard')->with('success', 'Reserva actualizada correctamente.');
    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error("Error de validación al actualizar reserva: " . $e->getMessage());
        return redirect()->back()->withErrors($e->validator->getMessageBag());
    } catch (\Exception $e) {
        \Log::error("Error inesperado al actualizar reserva: " . $e->getMessage());
        return redirect()->route('admin.dashboard')->with('error', 'Ocurrió un error inesperado al actualizar la reserva.');
    }
}


    public function destroy($id)
    {
        try {
            $reservation = TransferReserva::findOrFail($id);

            $fechaEntrada = strtotime($reservation->fecha_entrada . ' ' . $reservation->hora_entrada);
            if (($fechaEntrada - time()) < 48 * 3600) {
                return redirect()->route('admin.dashboard')->with('error', 'No se puede eliminar la reserva con menos de 48 horas de antelación.');
            }

            $reservation->delete();

            return redirect()->route('admin.dashboard')->with('success', 'Reserva eliminada correctamente.');
        } catch (\Exception $e) {
            \Log::error('Error al eliminar reserva: ' . $e->getMessage());
            return redirect()->route('admin.dashboard')->with('error', 'Ocurrió un error al eliminar la reserva.');
        }
    }

    public function getTrayectos()
{
    // Obtiene las reservas necesarias para el calendario
    $trayectos = TransferReserva::select('id_reserva', 'fecha_entrada', 'hora_entrada', 'id_destino')
        ->with('destino:id_zona,descripcion') // Relación con destino
        ->get();

    return response()->json($trayectos);
}


}
