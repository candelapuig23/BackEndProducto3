@extends('layouts.app')

@section('title', 'Editar Perfil')

@section('content')
<div class="dashboard-container">
    <h2>Editar Perfil</h2>

    <form action="{{ route('users.update', auth()->id()) }}" method="POST">
        @csrf
        @method('PUT')

        <label for="name">Nombre:</label>
        <input type="text" name="name" id="name" value="{{ auth()->user()->name }}" required>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="{{ auth()->user()->email }}" required>

        <label for="password">Nueva Contraseña:</label>
        <input type="password" name="password" id="password">

        <button type="submit">Actualizar Perfil</button>
    </form>
</div>
@endsection
