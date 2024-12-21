@extends('layouts.app')

@section('title', 'Panel del Hotel')

@section('content')
<div class="dashboard-container">
    <h2>Bienvenido, {{ $hotel->usuario }}</h2>
    <p>Este es tu panel de control.</p>

    <h3>Tus Reservas</h3>
    @if ($reservas->isEmpty())
        <p>No tienes reservas asignadas.</p>
    @else
       <table>
    <thead>
        <tr>
            <th>ID Reserva</th>
            <th>Fecha de Reserva</th>
            <th>Fecha de Entrada</th>
            <th>Fecha de Salida</th>
            <th>Número de Viajeros</th>
            <th>Precio</th>
            <th>Comisión</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($reservas as $reserva)
            <tr>
                <td>{{ $reserva->id_reserva }}</td>
                <td>{{ $reserva->fecha_reserva }}</td> <!-- Fecha de la reserva -->
                <td>{{ $reserva->fecha_entrada_mostrada }}</td> <!-- Fecha de entrada -->
                <td>{{ $reserva->fecha_salida_mostrada }}</td> <!-- Fecha de salida -->
                <td>{{ $reserva->num_viajeros }}</td>
                <td>{{ number_format($reserva->precio, 2) }} €</td>
                <td>{{ number_format($reserva->comision, 2) }} €</td>
            </tr>
        @endforeach
    </tbody>
</table>

    @endif

    <h3>Resumen de Comisiones por Mes</h3>
    @if (!empty($comisionesPorMes))
        <table>
            <thead>
                <tr>
                    <th>Mes</th>
                    <th>Total Comisiones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($comisionesPorMes as $mes => $data)
                    <tr>
                        <td>{{ $mes }}</td>
                        <td>{{ number_format($data['total_comision'], 2) }} €</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No hay comisiones registradas.</p>
    @endif

    <!-- Botón para crear una nueva reserva -->
    <a href="{{ route('hotel.reservations.create') }}" class="btn btn-primary">Crear Nueva Reserva</a>
</div>
@endsection
