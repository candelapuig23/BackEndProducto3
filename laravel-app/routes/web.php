<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReservationController;

// Login y registro de usuarios
Route::get('/login', [UserController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UserController::class, 'login']);
Route::get('/register', [UserController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [UserController::class, 'register'])->name('register');

// Panel de administrador
Route::get('/admin/dashboard', [UserController::class, 'adminDashboard'])->name('admin.dashboard');

// Funcionalidades de reservas en el panel de administrador
Route::get('/reservations/create', [ReservationController::class, 'create'])->name('reservations.create');
Route::post('/reservations/store', [ReservationController::class, 'store'])->name('reservations.store');
Route::get('/reservations/{id}/edit', [ReservationController::class, 'edit'])->name('reservations.edit'); // Edición de reservas
Route::put('/reservations/{id}', [ReservationController::class, 'update'])->name('reservations.update'); // Actualización de reservas
Route::delete('/reservations/{id}', [ReservationController::class, 'destroy'])->name('reservations.destroy'); // Eliminación de reservas
Route::get('/admin/trayectos', [UserController::class, 'getTrayectos'])->name('admin.trayectos');

// Ruta para obtener los trayectos (JSON)
Route::get('/admin/trayectos', [ReservationController::class, 'getTrayectos'])->name('admin.trayectos');

// Ruta para mostrar el formulario de edición yy editarlo
Route::get('/profile/edit', [UserController::class, 'editProfile'])->middleware('auth')->name('profile.edit');
Route::put('/profile/update/{id}', [UserController::class, 'updateProfile'])->middleware('auth')->name('users.update');



// Ruta para cerrar sesión
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/'); // Redirige a la página de inicio
})->name('logout');

// Panel de usuario
Route::get('/user/dashboard', [UserController::class, 'userDashboard'])->name('user.dashboard');

// Página principal
Route::get('/', function () {
    return view('home');
})->name('home');

// Página de inicio alternativa (opcional)
Route::get('/home', function () {
    return view('home');
});

// Rutas adicionales que puedes requerir
Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index'); // Listado de reservas
Route::get('/reservations/{id}', [ReservationController::class, 'show'])->name('reservations.show'); // Detalles de una reserva

// Ruta para mostrar el formulario de registro de hoteles desde panel admin
Route::get('/admin/register-hotel', [UserController::class, 'showRegisterHotelForm'])
    ->name('register.hotel.form')
    ->middleware('auth'); // Protegida para administradores desde panel admin

// Ruta para almacenar el registro de hoteles
Route::post('/admin/register-hotel', [UserController::class, 'registerHotel'])
    ->name('register.hotel.post')
    ->middleware('auth');

    //ruta para iniciar sesioncomo hoteles des de la home y para mostrar el formulario de logn
    Route::get('/hotel/login', [UserController::class, 'showHotelLoginForm'])->name('hotel.login');
Route::post('/hotel/login', [UserController::class, 'hotelLogin'])->name('hotel.login.post');

//ruta para el panel de hoteles
Route::get('/hotel/dashboard', [UserController::class, 'hotelDashboard'])->name('hotel.dashboard');

// Ruta para mostrar el formulario de creación de reservas para hoteles
Route::get('/hotel/reservations/create', [ReservationController::class, 'createFromHotel'])
    ->name('hotel.reservations.create');


// Ruta para almacenar la reserva desde el panel de hoteles
Route::post('/hotel/reservations/store', function () {
    \Log::info('La ruta hotel.reservations.store fue alcanzada.');
});

// Ruta para ejecutar el método setPrecios en UserController
Route::get('/admin/set-precios', [UserController::class, 'setPrecios'])->name('admin.setPrecios');

Route::get('/api/reservas/zonas', [ReservationController::class, 'getReservasPorZonas'])->name('api.reservas.zonas');
