<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReservationController;

// Login y registro de usuarios
Route::get('/login', [UserController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UserController::class, 'login']);
Route::get('/register', [UserController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [UserController::class, 'register'])->name('register');

// Panel de administrador (sin middleware)
Route::get('/admin/dashboard', [UserController::class, 'adminDashboard'])->name('admin.dashboard');


// Ruta para obtener los trayectos (JSON)
Route::get('/admin/trayectos', [ReservationController::class, 'getTrayectos'])->name('admin.trayectos');

// Ruta para editar perfil
Route::get('/profile/edit', [UserController::class, 'editProfile'])->name('profile.edit');

// Ruta para cerrar sesión
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');

// Panel de usuario (sin middleware)
Route::get('/user/dashboard', [UserController::class, 'userDashboard'])->name('user.dashboard');

// Funcionalidades de reservas (sin middleware)
Route::get('/reservations/create', [ReservationController::class, 'create'])->name('reservations.create');
Route::post('/reservations/store', [ReservationController::class, 'store'])->name('reservations.store');
Route::get('/reservations/{id}/edit', [ReservationController::class, 'edit'])->name('reservations.edit');
Route::delete('/reservations/{id}', [ReservationController::class, 'cancel'])->name('reservations.cancel');

// Página principal
Route::get('/', function () {
    return view('home');
})->name('home');

// Página de inicio alternativa (opcional)
Route::get('/home', function () {
    return view('home');
});


// Nuevas rutas adicionales que pudiste requerir
Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
Route::get('/reservations/{id}', [ReservationController::class, 'show'])->name('reservations.show');
Route::put('/reservations/{id}', [ReservationController::class, 'update'])->name('reservations.update');


// Ruta para el registro de hoteles
Route::get('/register/hotel', function () {
    return view('register_hotel'); // Asegúrate de tener esta vista
})->name('register.hotel');

