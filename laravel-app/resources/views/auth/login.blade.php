@extends('layouts.app')

@section('title', 'Iniciar Sesión')

@section('content')
<div class="login-container">
    <h1>Iniciar Sesión</h1>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <label for="role">Tipo de Usuario:</label>
        <select name="role" id="role" required>
            <option value="user">Particular</option>
            <option value="admin">Administrador</option>
        </select>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Iniciar Sesión</button>
    </form>
</div>
@endsection
