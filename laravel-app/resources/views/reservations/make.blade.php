@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Mostrar errores -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (Auth::user() instanceof App\Models\TransferHotel)
    <form action="{{ route('hotel.reservations.store') }}" method="POST" id="reservationForm">
@else
    <form action="{{ route('reservations.store') }}" method="POST" id="reservationForm">
@endif

        @csrf
        <h2>Formulario de Reserva de Traslado</h2>
        <!-- Tipo de Trayecto -->
        <label for="trayecto">Tipo de Trayecto:</label>
        <select name="trayecto" id="trayecto" onchange="mostrarCamposTrayecto()" required>
            <option value="">Selecciona un trayecto</option>
            @foreach($tiposTrayecto as $tipo)
                <option value="{{ $tipo->descripcion }}">{{ ucfirst($tipo->descripcion) }}</option>
            @endforeach
        </select>
        <!-- Campos dinámicos según el trayecto -->
        <div id="aeropuertoHotelFields" style="display:none;">
            <h3>Trayecto: Aeropuerto a Hotel</h3>
            <label for="diaLlegada">Día de llegada:</label>
            <input type="date" id="diaLlegada" name="diaLlegada">
            <label for="horaLlegada">Hora de llegada:</label>
            <input type="time" id="horaLlegada" name="horaLlegada">
            <label for="numeroVuelo">Número de vuelo:</label>
            <input type="text" id="numeroVuelo" name="numeroVuelo">
            <label for="aeropuertoOrigen">Aeropuerto de Origen:</label>
            <input type="text" id="aeropuertoOrigen" name="aeropuertoOrigen">
        </div>
        <div id="hotelAeropuertoFields" style="display:none;">
            <h3>Trayecto: Hotel a Aeropuerto</h3>
            <label for="diaVuelo">Día del vuelo:</label>
            <input type="date" id="diaVuelo" name="diaVuelo">
            <label for="horaVuelo">Hora del vuelo:</label>
            <input type="time" id="horaVuelo" name="horaVuelo">
            <label for="horaRecogida">Hora de recogida:</label>
            <input type="time" id="horaRecogida" name="horaRecogida">
        </div>
        <!-- Selección de Zona -->
        <label for="idZona">Seleccione una Zona:</label>
        <select id="idZona" name="idZona" required>
            <option value="">Seleccione una Zona</option>
            @foreach($zonas as $zona)
                <option value="{{ $zona->id_zona }}">{{ $zona->descripcion }}</option>
            @endforeach
        </select>
        <!-- Selección de Vehículo -->
        <label for="idVehiculo">Seleccione un Vehículo:</label>
        <select id="idVehiculo" name="idVehiculo" required>
            <option value="">Seleccione un vehículo</option>
            @foreach($vehiculos as $vehiculo)
                <option value="{{ $vehiculo->id_vehiculo }}">{{ ucfirst($vehiculo->descripcion) }}</option>
            @endforeach
        </select>
        <!-- Selección de Hotel -->
@if (Auth::user() instanceof App\Models\TransferHotel)
    <!-- Campo oculto si el usuario autenticado es un hotel -->
    <input type="hidden" name="hotelDestino" value="{{ Auth::user()->id_hotel }}">
@else
    <!-- Selección manual del hotel para admin o particulares -->
    <label for="hotelDestino">Hotel de destino/recogida:</label>
    <select name="hotelDestino" id="hotelDestino" required>
        <option value="">Seleccione un hotel</option>
        @foreach($hoteles as $hotel)
            <option value="{{ $hotel->id_hotel }}">{{ ucfirst($hotel->usuario) }}</option>
        @endforeach
    </select>
@endif
        <!-- Número de Viajeros -->
        <label for="numViajeros">Número de viajeros:</label>
        <input type="number" id="numViajeros" name="numViajeros" min="1" required>
        <!-- Datos del Cliente -->
       <!-- Datos del Cliente -->
<h3>Datos del Cliente</h3>
<label for="email">Correo electrónico:</label>
<input type="email" id="email" name="email" value="{{ Auth::check() ? Auth::user()->email : '' }}" required>
<label for="nombre">Nombre completo:</label>
<input type="text" id="nombre" name="nombre" value="{{ Auth::check() ? Auth::user()->nombre : '' }}" required>

        <!-- Botón de Envío -->
        <button type="submit">Realizar reserva</button>
    </form>
</div>
<script>
    function mostrarCamposTrayecto() {
        const trayecto = document.getElementById("trayecto").value;
        const aeropuertoHotelFields = document.getElementById("aeropuertoHotelFields");
        const hotelAeropuertoFields = document.getElementById("hotelAeropuertoFields");
        aeropuertoHotelFields.style.display = (trayecto === "Solo ida" || trayecto === "Ida y vuelta") ? "block" : "none";
        hotelAeropuertoFields.style.display = (trayecto === "Solo vuelta" || trayecto === "Ida y vuelta") ? "block" : "none";
    }
    document.addEventListener("DOMContentLoaded", function() {
        mostrarCamposTrayecto();
    });
</script>
@endsection
