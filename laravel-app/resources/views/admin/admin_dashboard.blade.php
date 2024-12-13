@extends('layouts.app')

@section('title', 'Panel de Administrador')

@section('content')
<div class="dashboard-container">
    <h2>Bienvenido, Administrador!</h2>

    <h3>Gestión de todas las Reservas</h3>
    @if ($reservations->isEmpty())
        <div class="no-reservations">
            <p>No hay reservas disponibles.</p>
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th>ID Reserva</th>
                    <th>Localizador</th>
                    <th>Email Cliente</th>
                    <th>Número de Viajeros</th>
                    <th>Fecha de Reserva</th>
                    <th>Fecha de Entrada</th>
                    <th>Tipo de Trayecto</th>
                    <th>Vehículo</th>
                    <th>Hotel</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reservations as $reservation)
                <tr>
                    <td>{{ $reservation->id_reserva }}</td>
                    <td>{{ $reservation->localizador }}</td>
                    <td>{{ $reservation->email_cliente }}</td>
                    <td>{{ $reservation->num_viajeros }}</td>
                    <td>{{ $reservation->fecha_reserva }}</td>
                    <td>{{ $reservation->fecha_entrada }}</td>
                    <td>{{ $reservation->tipo_reserva }}</td>
                    <td>{{ $reservation->vehiculo }}</td>
                    <td>{{ $reservation->hotel->nombre ?? 'N/A' }}</td>
                    <td>
                        <a href="{{ route('reservations.edit', $reservation->id_reserva) }}">Editar</a> |
                        <a href="{{ route('reservations.cancel', $reservation->id_reserva) }}">Cancelar</a>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <h3>Calendario de Trayectos</h3>
    <div class="view-options">
        <button onclick="cambiarVista('mensual')">Vista Mensual</button>
        <button onclick="cambiarVista('semanal')">Vista Semanal</button>
        <button onclick="cambiarVista('diaria')">Vista Diaria</button>
    </div>

    <div id="calendar" class="calendar"></div>
    <div id="eventos"></div>

    <h3>Opciones</h3>
    <div class="dashboard-options">
        <ul>
            <li><a href="{{ route('reservations.create') }}">Hacer nueva reserva</a></li>
            <li><a href="{{ route('profile.edit') }}">Editar Perfil</a></li>
            <li>
    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
        @csrf
        <button type="submit" style="background: none; border: none; color: #007bff; cursor: pointer; text-decoration: underline;">
            Cerrar sesión
        </button>
    </form>
</li>

        </ul>
    </div>
</div>

<script>
    let mesActual, anioActual, trayectos, vistaActual, fechaSeleccionada;

    document.addEventListener('DOMContentLoaded', function () {
        mesActual = new Date().getMonth() + 1;
        anioActual = new Date().getFullYear();
        vistaActual = 'mensual'; // La vista predeterminada es mensual

        fetch('{{ route('admin.trayectos') }}')
            .then(response => response.json())
            .then(data => {
                trayectos = data;
                generarCalendario(vistaActual, mesActual, anioActual);
            })
            .catch(error => console.error('Error al cargar los trayectos:', error));
    });

    function cambiarVista(vista) {
        vistaActual = vista;
        generarCalendario(vistaActual, mesActual, anioActual);
    }

    function generarCalendario(vista, mes, anio) {
        if (vista === 'mensual') {
            generarVistaMensual(mes, anio);
        }
    }

    function generarVistaMensual(mes, anio) {
        const diasEnMes = new Date(anio, mes, 0).getDate();
        const primerDiaSemana = new Date(anio, mes - 1, 1).getDay();
        let html = `<table><thead><tr>`;
        ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'].forEach(dia => {
            html += `<th>${dia}</th>`;
        });
        html += `</tr></thead><tbody><tr>`;

        for (let i = 1; i < primerDiaSemana; i++) {
            html += `<td></td>`;
        }

        for (let dia = 1; dia <= diasEnMes; dia++) {
            html += `<td>${dia}</td>`;
            if ((dia + primerDiaSemana - 1) % 7 === 0) {
                html += `</tr><tr>`;
            }
        }

        html += `</tr></tbody></table>`;
        document.getElementById('calendar').innerHTML = html;
    }
</script>
@endsection
