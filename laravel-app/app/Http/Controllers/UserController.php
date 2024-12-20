<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransferViajero;
use App\Models\TransferViajeroAdmin;
use App\Models\TransferReserva;
use App\Models\TransferHotel;
use App\Models\TransferPrecios;




use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    // Registrar un nuevo usuario o administrador
public function register(Request $request)
{
    // Validar los datos del formulario
    $validated = $request->validate([
        'nombre' => 'required|string|max:255',
        'apellido1' => 'required|string|max:255',
        'apellido2' => 'required|string|max:255',
        'direccion' => 'required|string|max:255',
        'codigoPostal' => 'required|string|max:10',
        'ciudad' => 'required|string|max:255',
        'pais' => 'required|string|max:255',
        'email' => 'required|email|unique:transfer_viajeros,email|unique:transfer_viajeros_admin,email',
        'password' => 'required|string|min:6|confirmed',
        'role' => 'required|string|in:admin,user',
    ]);

    // Crear usuario dependiendo del rol
    if ($validated['role'] === 'admin') {
        TransferViajeroAdmin::create([
            'nombre' => $validated['nombre'],
            'apellido1' => $validated['apellido1'],
            'apellido2' => $validated['apellido2'],
            'direccion' => $validated['direccion'],
            'codigoPostal' => $validated['codigoPostal'],
            'ciudad' => $validated['ciudad'],
            'pais' => $validated['pais'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);
    } else {
        TransferViajero::create([
            'nombre' => $validated['nombre'],
            'apellido1' => $validated['apellido1'],
            'apellido2' => $validated['apellido2'],
            'direccion' => $validated['direccion'],
            'codigoPostal' => $validated['codigoPostal'],
            'ciudad' => $validated['ciudad'],
            'pais' => $validated['pais'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);
    }

    return redirect()->route('login')->with('success', 'Usuario registrado correctamente.');
}




    // Iniciar sesión
   public function login(Request $request)
{
    // Registrar intento de inicio de sesión en los logs
    \Log::info('Intento de inicio de sesión con datos:', $request->all());

    // Validar las credenciales enviadas por el formulario
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'role' => 'required|in:user,admin', // Validar que el rol sea "user" o "admin"
    ]);

    // Si el rol es "admin"
    if ($credentials['role'] === 'admin') {
        $admin = TransferViajeroAdmin::where('email', $credentials['email'])->first();
        
        if ($admin) {
            \Log::info('Administrador encontrado:', ['email' => $admin->email]);
        } else {
            \Log::error('Administrador no encontrado en la tabla transfer_viajeros_admin.', ['email' => $credentials['email']]);
        }

        if ($admin && Hash::check($credentials['password'], $admin->password)) {
            Auth::login($admin); // Iniciar sesión como administrador
            \Log::info('Administrador autenticado:', ['email' => $admin->email]);
            return redirect()->route('admin.dashboard');
        }

        \Log::error('Fallo de autenticación del administrador:', ['email' => $credentials['email']]);
    }

    // Si el rol es "user"
    if ($credentials['role'] === 'user') {
        $user = TransferViajero::where('email', $credentials['email'])->first();

        if ($user) {
            \Log::info('Usuario encontrado:', ['email' => $user->email]);
        } else {
            \Log::error('Usuario no encontrado en la tabla transfer_viajeros.', ['email' => $credentials['email']]);
        }

        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user); // Iniciar sesión como usuario
            \Log::info('Usuario autenticado correctamente:', ['email' => $user->email]);
            return redirect()->route('user.dashboard'); // Redirigir al panel de usuario
        }

        \Log::error('Contraseña incorrecta para el usuario:', ['email' => $credentials['email']]);
    }

    // Si no se cumplen las condiciones, registrar error genérico y redirigir al login con errores
    \Log::error('Fallo de autenticación:', ['email' => $credentials['email']]);
    return back()->withErrors(['email' => 'Credenciales inválidas.']);
}




    // Mostrar el formulario de creación de usuario
    public function showCreateForm()
    {
        return view('users.create'); // Cargar la vista Blade para el formulario
    }

    // Manejar la creación de usuario desde el formulario
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'apellido1' => 'required|string|max:255',
            'apellido2' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'codigoPostal' => 'required|string|max:10',
            'ciudad' => 'required|string|max:255',
            'pais' => 'required|string|max:255',
            'email' => 'required|email|unique:transfer_viajeros,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        TransferViajero::create([
            'nombre' => $validated['name'],
            'apellido1' => $validated['apellido1'],
            'apellido2' => $validated['apellido2'],
            'direccion' => $validated['direccion'],
            'codigoPostal' => $validated['codigoPostal'],
            'ciudad' => $validated['ciudad'],
            'pais' => $validated['pais'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        return redirect()->route('users.create.form')->with('success', 'Usuario creado exitosamente!');
    }

    // Obtener usuario por ID
    public function getUserById($id)
    {
        $user = TransferViajero::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json($user, 200);
    }

    // Actualizar perfil del usuario
  public function updateProfile(Request $request, $id)
{
    \Log::info('Iniciando actualización de perfil', ['id' => $id]);

    // Buscar el usuario en la base de datos
    $user = TransferViajeroAdmin::find($id);

    if (!$user) {
        \Log::error('Usuario no encontrado en la base de datos.', ['id' => $id]);
        return redirect()->route('profile.edit')->withErrors(['error' => 'Usuario no encontrado.']);
    }

    \Log::info('Usuario encontrado para actualizar.', ['id' => $user->id_viajero_admin, 'nombre' => $user->nombre]);

    // Validar los datos del formulario
    try {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:transfer_viajeros_admin,email,' . $id . ',id_viajero_admin',
            'password' => 'nullable|min:6|confirmed',
        ]);
        \Log::info('Validación exitosa.', ['validated' => $validated]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('Error de validación.', ['errors' => $e->errors()]);
        return redirect()->route('profile.edit')->withErrors($e->errors());
    }

    // Asignar los nuevos valores
    try {
        $user->nombre = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
            \Log::info('Contraseña actualizada correctamente.');
        } else {
            \Log::info('No se actualizó la contraseña (campo vacío).');
        }

        // Guardar los cambios
        $user->save();
        \Log::info('Perfil actualizado exitosamente.', ['id' => $user->id_viajero_admin]);
    } catch (\Exception $e) {
        \Log::error('Error al guardar los datos en la base de datos.', ['error' => $e->getMessage()]);
        return redirect()->route('profile.edit')->withErrors(['error' => 'No se pudo actualizar el perfil.']);
    }

    // Confirmar que la actualización fue exitosa
   return redirect()->route('admin.dashboard')->with('success', 'Perfil actualizado correctamente.');
}

    // Listar todos los usuarios
    public function listUsers()
    {
        $users = TransferViajero::all();
        return response()->json($users);
    }

    public function editProfile()
{
    // Obtener al administrador autenticado
    $user = TransferViajeroAdmin::where('email', 'prueba@gmail.com')->first();

    // Verificar si el usuario tiene un ID válido
    if ($user && $user->id_viajero_admin) {
        \Log::info('Usuario cargado para edición de perfil:', ['id' => $user->id_viajero_admin, 'nombre' => $user->nombre]);
        return view('profile.edit', ['user' => $user]);
    } else {
        \Log::error('No se pudo cargar el perfil del usuario.');
        return redirect()->route('login')->withErrors(['error' => 'No se pudo cargar el perfil del usuario.']);
    }
}







 // Mostrar el formulario de login
    public function showLoginForm()
    {
        return view('auth.login');
    }


    // Mostrar el formulario de registro
    public function showRegisterForm()
    {
        return view('register');
    }

    
public function userDashboard()
{
    // Intentar obtener el usuario autenticado
    $user = Auth::user();

    // Validar que el usuario exista
    if (!$user) {
        \Log::error('Usuario no autenticado intentando acceder al dashboard.');
        return redirect()->route('login')->withErrors(['auth' => 'Debes iniciar sesión.']);
    }

    \Log::info('Usuario autenticado accediendo al dashboard.', ['email' => $user->email]);

    // Obtener las reservas asociadas al usuario autenticado
    $reservations = TransferReserva::where('email_cliente', $user->email)->get();

    // Validar si hay reservas
    if ($reservations->isEmpty()) {
        \Log::info('El usuario no tiene reservas registradas.', ['email' => $user->email]);
    } else {
        \Log::info('Reservas encontradas para el usuario.', ['email' => $user->email, 'reservas' => $reservations->count()]);
    }

    return view('user_dashboard', compact('user', 'reservations'));
}
//metodo para mostrar la informacion de las reservas en el panel de admin
public function adminDashboard()
{
    // Obtener las reservas generales con las relaciones necesarias
    $reservations = TransferReserva::with(['hotel', 'tipoReserva', 'vehiculo'])->get();

    foreach ($reservations as $reservation) {
        \Log::info('Reserva cargada:', [
            'ID' => $reservation->id_reserva,
            'Hotel' => $reservation->hotel ? $reservation->hotel->usuario : 'NULL',
            'Tipo de Trayecto' => $reservation->tipoReserva ? $reservation->tipoReserva->descripcion : 'NULL',
            'Vehículo' => $reservation->vehiculo ? $reservation->vehiculo->descripcion : 'NULL'
        ]);
    }

    // Obtener las reservas realizadas por hoteles
    $hoteles = TransferHotel::with(['reservas'])->get();
    $reservasPorHotel = [];

    foreach ($hoteles as $hotel) {
        $totalComision = 0;
        $reservas = $hotel->reservas;

        foreach ($reservas as $reserva) {
            $precio = TransferPrecios::where('id_hotel', $hotel->id_hotel)
                ->where('id_vehiculo', $reserva->id_vehiculo)
                ->value('precio');

            if ($precio) {
                $totalReserva = $precio; // Sin multiplicar por num_viajeros
                $comision = ($hotel->comision / 100) * $totalReserva;
                $totalComision += $comision;

                $reservasPorHotel[$hotel->id_hotel]['reservas'][] = [
                    'localizador' => $reserva->localizador,
                    'precio' => $precio,
                    'total' => $totalReserva,
                    'comision' => $comision,
                ];
            }
        }

        $reservasPorHotel[$hotel->id_hotel]['total_comision'] = $totalComision;
        $reservasPorHotel[$hotel->id_hotel]['nombre_hotel'] = $hotel->usuario;
    }

    // Combinar ambas funcionalidades en la vista
    return view('admin.admin_dashboard', [
        'reservations' => $reservations,
        'reservasPorHotel' => $reservasPorHotel
    ]);
}


public function getTrayectos(Request $request)
{
    $vista = $request->query('vista', 'mensual'); // mensual, semanal, diaria
    $fecha = $request->query('fecha', now());

    $query = TransferReserva::with(['hotel', 'tipoReserva', 'vehiculo']);

    if ($vista === 'mensual') {
        $inicioMes = Carbon::parse($fecha)->startOfMonth();
        $finMes = Carbon::parse($fecha)->endOfMonth();
        $query->whereBetween('fecha_entrada', [$inicioMes, $finMes]);
    } elseif ($vista === 'semanal') {
        $inicioSemana = Carbon::parse($fecha)->startOfWeek();
        $finSemana = Carbon::parse($fecha)->endOfWeek();
        $query->whereBetween('fecha_entrada', [$inicioSemana, $finSemana]);
    } elseif ($vista === 'diaria') {
        $query->whereDate('fecha_entrada', Carbon::parse($fecha));
    }

    $trayectos = $query->get();
    return response()->json($trayectos);
}

//METODOS PARA LOS HOTELES
// Mostrar el formulario de registro de hoteles des de panel admin
public function showRegisterHotelForm()
{
    return view('register_hotel'); // Asegúrate de tener esta vista en resources/views
}

// Registrar un nuevo hotel des de panel admin
public function registerHotel(Request $request)
{
    // Validar los datos del formulario
    $validated = $request->validate([
        'id_zona' => 'required|integer|exists:transfer_zona,id_zona',
        'comision' => 'required|numeric|min:0|max:100',
        'usuario' => 'required|email|unique:transfer_hotel,usuario',
        'password' => 'required|string|min:6|confirmed',
    ]);

    // Crear un nuevo hotel
    TransferHotel::create([
        'id_zona' => $validated['id_zona'],
        'comision' => $validated['comision'],
        'usuario' => $validated['usuario'],
        'password' => Hash::make($validated['password']),
    ]);

    return redirect()->route('admin.dashboard')->with('success', 'Hotel registrado correctamente.');
}

public function showHotelLoginForm()
{
    return view('auth.hotel_login'); // Vista para login de hoteles
}

public function loginHotel(Request $request)
{
    $validated = $request->validate([
        'usuario' => 'required|email',
        'password' => 'required|string',
    ]);

    // Buscar el hotel en la tabla transfer_hotel
    $hotel = TransferHotel::where('usuario', $validated['usuario'])->first();

    if ($hotel && Hash::check($validated['password'], $hotel->password)) {
        // Autenticar al hotel (usando Auth guard opcional)
        Auth::login($hotel);

        return redirect()->route('hotel.dashboard')->with('success', 'Bienvenido al panel del hotel.');
    }

    return back()->withErrors(['usuario' => 'Credenciales incorrectas.']);
}


//método login de hoteles

public function hotelLogin(Request $request)
{
    // Log del intento de inicio de sesión
    \Log::info('Intento de inicio de sesión de hotel:', $request->all());

    // Validar las credenciales enviadas por el formulario
    $validated = $request->validate([
        'usuario' => 'required|email', // El campo usuario es un email
        'password' => 'required|string',
    ]);

    // Buscar el hotel en la base de datos por el campo "usuario"
    $hotel = TransferHotel::where('usuario', $validated['usuario'])->first();

    if ($hotel) {
        \Log::info('Hotel encontrado:', ['usuario' => $hotel->usuario]);
    } else {
        \Log::error('Hotel no encontrado en la tabla transfer_hotel.', ['usuario' => $validated['usuario']]);
    }

    // Verificar si el hotel existe y la contraseña coincide
    if ($hotel && Hash::check($validated['password'], $hotel->password)) {
        // Almacenar el ID del hotel en la sesión manualmente
        session(['hotel_id' => $hotel->id_hotel]);
        \Log::info('Hotel autenticado correctamente:', ['usuario' => $hotel->usuario]);

        // Redirigir al panel del hotel
        return redirect()->route('hotel.dashboard')->with('success', 'Inicio de sesión exitoso.');
    }

    // Si falla la autenticación
    \Log::error('Credenciales incorrectas para el hotel.', ['usuario' => $validated['usuario']]);
    return back()->withErrors(['error' => 'Credenciales incorrectas.'])->withInput();
}

//método del panel del hoteles
public function hotelDashboard()
{
    // Obtener el ID del hotel autenticado almacenado en la sesión
    $hotelId = session('hotel_id');

    if (!$hotelId) {
        \Log::error('No hay un hotel autenticado en la sesión.');
        return redirect()->route('hotel.login')->withErrors(['error' => 'Debes iniciar sesión antes de acceder al formulario.']);

    }

    // Obtener los datos del hotel
    $hotel = TransferHotel::find($hotelId);

    if (!$hotel) {
        \Log::error('El hotel no existe en la base de datos.', ['id' => $hotelId]);
        return redirect()->route('hotel.login.form')->withErrors(['error' => 'El hotel no existe.']);
    }

    // Obtener las reservas asociadas a este hotel
    $reservas = TransferReserva::where('id_hotel', $hotel->id_hotel)->get();

    // Calcular precios y comisiones para cada reserva
    foreach ($reservas as $reserva) {
        $precio = TransferPrecios::where('id_hotel', $hotel->id_hotel)
            ->where('id_vehiculo', $reserva->id_vehiculo)
            ->value('precio');

        if ($precio) {
            $reserva->precio = $precio; // Precio base de la reserva
            $reserva->comision = ($hotel->comision / 100) * $precio; // Cálculo de la comisión
        } else {
            $reserva->precio = 0;
            $reserva->comision = 0;
        }
    }

    // Calcular las comisiones totales por mes
    $comisionesPorMes = [];
    foreach ($reservas as $reserva) {
        $mes = date('Y-m', strtotime($reserva->fecha_entrada)); // Formato "YYYY-MM"
        if (!isset($comisionesPorMes[$mes])) {
            $comisionesPorMes[$mes] = [
                'mes' => $mes,
                'total_comision' => 0,
            ];
        }
        $comisionesPorMes[$mes]['total_comision'] += $reserva->comision;
    }

    // Retornar la vista con el hotel, las reservas, y las comisiones por mes
    return view('hotel.hotel_dashboard', compact('hotel', 'reservas', 'comisionesPorMes'));
}






//metodo temporal para asignar el precio 50€ a todas las combinaciones de vehiculos y hotel

/*public function setPrecios()
{
    $precioFijo = 50; // Precio fijo que se asignará a todas las combinaciones

    // Obtener todos los hoteles y vehículos
    $hoteles = \App\Models\TransferHotel::all();
    $vehiculos = \App\Models\TransferVehiculo::all();

    foreach ($hoteles as $hotel) {
        foreach ($vehiculos as $vehiculo) {
            // Comprobar si ya existe un precio para esta combinación
            $existePrecio = \App\Models\TransferPrecios::where('id_hotel', $hotel->id_hotel)
                ->where('id_vehiculo', $vehiculo->id_vehiculo)
                ->first();

            if (!$existePrecio) {
                // Crear un nuevo precio si no existe
                \App\Models\TransferPrecios::create([
                    'id_hotel' => $hotel->id_hotel,
                    'id_vehiculo' => $vehiculo->id_vehiculo,
                    'precio' => $precioFijo,
                ]);
            }
        }
    }

    return "Precios asignados correctamente.";
}*/

}


