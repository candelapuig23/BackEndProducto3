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

// Ruta para obtener los trayectos (JSON)
Route::get('/admin/trayectos', [ReservationController::class, 'getTrayectos'])->name('admin.trayectos');

// Ruta para mostrar el formulario de edición yy editarlo
Route::get('/profile/edit', [UserController::class, 'editProfile'])->middleware('auth')->name('profile.edit');
Route::put('/profile/update/{id}', [UserController::class, 'updateProfile'])->middleware('auth')->name('users.update');



// Ruta para cerrar sesión
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
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

// Ruta para el registro de hoteles (si aplica)
Route::get('/register/hotel', function () {
    return view('register_hotel'); // Asegúrate de tener esta vista creada
})->name('register.hotel');
