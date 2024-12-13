<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransferViajero;
use App\Models\TransferViajeroAdmin;
use App\Models\TransferReserva;



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
    public function updateUserProfile(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:transfer_viajeros,email,' . $id,
            'password' => 'nullable|string|min:6',
        ]);

        $user = TransferViajero::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $user->update(array_filter([
            'nombre' => $validated['nombre'],
            'email' => $validated['email'],
            'password' => $validated['password'] ? bcrypt($validated['password']) : null,
        ]));

        return response()->json(['message' => 'Perfil actualizado correctamente'], 200);
    }

    // Listar todos los usuarios
    public function listUsers()
    {
        $users = TransferViajero::all();
        return response()->json($users);
    }

    public function editProfile()
{
    // Cargar los datos del perfil del usuario autenticado
    $user = auth()->user();

    return view('profile.edit', compact('user'));
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

public function adminDashboard()
{
    // Obtener todas las reservas
    $reservations = TransferReserva::with(['hotel', 'tipoReserva', 'vehiculo'])->get();

    // Retornar la vista del panel de administrador con las reservas
    return view('admin.admin_dashboard', compact('reservations'));
}


}