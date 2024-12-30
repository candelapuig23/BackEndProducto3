@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Registrar Nuevo Hotel</h2>
    <form action="{{ route('register.hotel.post') }}" method="POST">
        @csrf
         <div>
            <label for="id_zona">Zona:</label>
            <select name="id_zona" required>
                <option value="">Seleccione una zona</option>
                @foreach ($zonas as $zona)
                    <option value="{{ $zona->id_zona }}">{{ $zona->descripcion }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="comision">Comisión (%):</label>
            <input type="number" name="comision" placeholder="Comisión (%)" min="0" max="100" required>
        </div>
        <div>
            <label for="usuario">Usuario (Email):</label>
            <input type="email" name="usuario" placeholder="Usuario (Email)" required>
        </div>
        <div>
            <label for="password">Contraseña:</label>
            <input type="password" name="password" placeholder="Contraseña" required>
        </div>
        <div>
            <label for="password_confirmation">Confirmar Contraseña:</label>
            <input type="password" name="password_confirmation" placeholder="Confirmar Contraseña" required>
        </div>
        <div>
            <button type="submit">Registrar Hotel</button>
        </div>
    </form>
</div>
@endsection
