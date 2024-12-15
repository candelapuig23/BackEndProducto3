@extends('layouts.app')

@section('title', 'Editar Perfil')

@section('content')
<div class="dashboard-container">
    <h2>Editar Perfil</h2>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Verificación si el usuario existe -->
    @if (isset($user) && $user->id_viajero_admin)
        <form action="{{ route('users.update', ['id' => $user->id_viajero_admin]) }}" method="POST">
            @csrf
            @method('PUT')

            <label for="name">Nombre:</label>
            <input type="text" id="name" name="name" value="{{ $user->nombre }}" required>

            <label for="email">Correo electrónico:</label>
            <input type="email" id="email" name="email" value="{{ $user->email }}" required>

            <label for="password">Nueva contraseña:</label>
            <input type="password" id="password" name="password">

            <label for="password_confirmation">Confirmar nueva contraseña:</label>
            <input type="password" id="password_confirmation" name="password_confirmation">

            <button type="submit">Guardar cambios</button>
        </form>
    @else
        <div class="alert alert-danger">
            <p>Error: No se pudo cargar el perfil del usuario. Por favor, intenta de nuevo.</p>
        </div>
    @endif
</div>
@endsection
