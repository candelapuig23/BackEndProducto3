@extends('layouts.app')

@section('title', 'Panel de Usuario')

@section('content')
<div class="dashboard-container">
    @if ($user)
        <h2>Bienvenido, {{ $user->nombre }}!</h2>
    @else
        <h2>Bienvenido, Usuario!</h2>
        <p>Error al obtener los datos del usuario.</p>
    @endif

    <div class="reservations-section">
        <h3>Mis Reservas</h3>

        @if ($reservations->isEmpty())
            <div class="no-reservations">
                <p>No tienes reservas.</p>
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Localizador</th>
                        <th>Tipo de Trayecto</th>
                        <th>Vehículo</th>
                        <th>Fecha de Reserva</th>
                        <th>Realizado por</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reservations as $reservation)
                        <tr>
                            <td>{{ $reservation->localizador }}</td>
                            <td>{{ $reservation->tipo_reserva }}</td>
                            <td>{{ $reservation->vehiculo }}</td>
                            <td>{{ $reservation->fecha_reserva }}</td>
                            <td>{{ $reservation->realizado_por }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="dashboard-options">
        <h3>Opciones</h3>
        <ul>
            <li><a href="{{ route('reservations.create') }}">Hacer nueva reserva</a></li>
            <li><a href="{{ route('profile.edit') }}">Editar Perfil</a></li>
            <li>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" style="background: none; border: none; color: #007bff; cursor: pointer; text-decoration: underline;">
                        Cerrar sesión
                    </button>
                </form>
            </li>
        </ul>
    </div>
</div>
@endsection
