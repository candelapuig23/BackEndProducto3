@extends('layouts.app')

@section('title', 'Editar Reserva')

@section('content')
<div class="container">
    <h2>Editar Reserva #{{ $reservation->id_reserva }}</h2>
    <form action="{{ route('reservations.update', $reservation->id_reserva) }}" method="POST">
        @csrf
        @method('PUT')

        <label>Hotel:</label>
        <select name="id_hotel" required>
            @foreach ($hoteles as $hotel)
                <option value="{{ $hotel->id_hotel }}" {{ $hotel->id_hotel == $reservation->id_hotel ? 'selected' : '' }}>
                    {{ $hotel->usuario }}
                </option>
            @endforeach
        </select>

        <label>Tipo de Trayecto:</label>
        <select name="id_tipo_reserva" required>
            @foreach ($tiposTrayecto as $tipo)
                <option value="{{ $tipo->id_tipo_reserva }}" {{ $tipo->id_tipo_reserva == $reservation->id_tipo_reserva ? 'selected' : '' }}>
                    {{ $tipo->descripcion }}
                </option>
            @endforeach
        </select>

        <label>Email Cliente:</label>
        <input type="email" name="email_cliente" value="{{ $reservation->email_cliente }}" required>

        <label>Destino:</label>
        <select name="id_zona" required>
            @foreach ($destinos as $destino)
                <option value="{{ $destino->id_zona }}" {{ $destino->id_zona == $reservation->id_destino ? 'selected' : '' }}>
                    {{ $destino->descripcion }}
                </option>
            @endforeach
        </select>

        <label>Fecha de Entrada:</label>
        <input type="date" name="fecha_entrada" value="{{ $reservation->fecha_entrada }}" required>

        <label>Hora de Entrada:</label>
        <input type="time" name="hora_entrada" value="{{ $reservation->hora_entrada }}" required>

        <label>Número de Vuelo de Entrada:</label>
        <input type="text" name="numero_vuelo_entrada" value="{{ $reservation->numero_vuelo_entrada }}">

        <label>Origen Vuelo de Entrada:</label>
        <input type="text" name="origen_vuelo_entrada" value="{{ $reservation->origen_vuelo_entrada }}">

        <label>Hora de Vuelo de Salida:</label>
        <input type="time" name="hora_vuelo_salida" value="{{ $reservation->hora_vuelo_salida }}">

        <label>Fecha de Vuelo de Salida:</label>
        <input type="date" name="fecha_vuelo_salida" value="{{ $reservation->fecha_vuelo_salida }}">

        <label>Número de Viajeros:</label>
        <input type="number" name="num_viajeros" value="{{ $reservation->num_viajeros }}" required>

        <label>Vehículo:</label>
<select name="id_vehiculo" required>
    @foreach ($vehiculos as $vehiculo)
        <option value="{{ $vehiculo->id_vehiculo }}" 
            {{ $vehiculo->id_vehiculo == $reservation->id_vehiculo ? 'selected' : '' }}>
            {{ $vehiculo->descripcion }}
        </option>
    @endforeach
</select>



        <button type="submit">Actualizar Reserva</button>
    </form>
</div>
@endsection
