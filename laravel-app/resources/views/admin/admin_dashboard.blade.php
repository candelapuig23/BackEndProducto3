@extends('layouts.app')

@section('title', 'Panel de Administrador')

@section('header', 'Panel de Administrador')

@section('content')
<div class="dashboard-container">
    <h2>Bienvenido, Administrador!</h2>

    <!-- Gestión de todas las Reservas -->
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
                    <td>{{ $reservation->tipoReserva ? $reservation->tipoReserva->descripcion : 'N/A' }}</td>
                    <td>{{ $reservation->vehiculo ? $reservation->vehiculo->descripcion : 'N/A' }}</td>
                    <td>{{ $reservation->hotel ? $reservation->hotel->usuario : 'N/A' }}</td>
                    <td>
                        <a href="{{ route('reservations.edit', $reservation->id_reserva) }}">Editar</a>
                        |
                        <form action="{{ route('reservations.destroy', $reservation->id_reserva) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background: none; border: none; color: #007bff; cursor: pointer; text-decoration: underline;">Cancelar</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Reservas realizadas por Hoteles -->
    <h3>Reservas realizadas por Hoteles</h3>
    @if ($reservasPorHotel)
        <table>
            <thead>
                <tr>
                    <th>Hotel</th>
                    <th>Localizador</th>
                    <th>Precio</th>
                    <th>Total</th>
                    <th>Comisión</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reservasPorHotel as $hotelId => $hotelData)
                    @foreach ($hotelData['reservas'] as $reserva)
                        <tr>
                            <td>{{ $hotelData['nombre_hotel'] }}</td>
                            <td>{{ $reserva['localizador'] }}</td>
                            <td>{{ $reserva['precio'] }} €</td>
                            <td>{{ $reserva['total'] }} €</td>
                            <td>{{ $reserva['comision'] }} €</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="5">
                            <strong>Total Comisión para {{ $hotelData['nombre_hotel'] }}: {{ $hotelData['total_comision'] }} €</strong>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No hay reservas realizadas por hoteles.</p>
    @endif

    <!-- Calendario de Trayectos -->
    <h3>Calendario de Trayectos</h3>
    <div class="view-options">
        <button onclick="cambiarVista('mensual')">Vista Mensual</button>
        <button onclick="cambiarVista('semanal')">Vista Semanal</button>
        <button onclick="cambiarVista('diaria')">Vista Diaria</button>
    </div>

    <div id="calendar" class="calendar"></div>
    <div id="nav-buttons" style="margin-top: 10px;"></div>
    <div id="eventos"></div>

    <!-- Opciones para crear reserva, editar perfil y cerrar sesión -->
    <h3>Opciones</h3>
    <div class="dashboard-options">
        <ul>
            <li>
                <a href="{{ route('reservations.create') }}">Hacer nueva reserva</a>
            </li>
            <li>
                <a href="{{ route('profile.edit') }}">Editar Perfil</a>
            </li>
            <li>
                <a href="{{ route('register.hotel.form') }}">Registrar Nuevo Hotel</a>
            </li>
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

     <!-- Modal para mostrar detalles de las reservas -->
<div id="modalReservas" style="display: none; position: fixed; top: 20%; left: 50%; transform: translate(-50%, -20%); background: white; padding: 20px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5); z-index: 1000;">
    <div>
        <!-- Botón para cerrar el modal -->
        <span onclick="cerrarModal()" style="cursor: pointer; float: right; font-size: 20px;">&times;</span>
        <h4 id="modalTitulo" style="margin-top: 0;">Detalles de las Reservas</h4>
        <ul id="modalContenido" style="list-style: none; padding: 0; margin: 0;">
            <!-- Aquí se generará dinámicamente el contenido de las reservas -->
        </ul>
    </div>
</div>

    <!-- Script con calendario actualizado -->
    <script>
        let mesActual, anioActual, trayectos, vistaActual, fechaSeleccionada;
        document.addEventListener('DOMContentLoaded', function () {
            mesActual = new Date().getMonth();
            anioActual = new Date().getFullYear();
            fechaSeleccionada = new Date();
            vistaActual = 'mensual'; 
            cargarTrayectos();
        });
        function cargarTrayectos() {
            fetch('{{ route('admin.trayectos') }}')
                .then(response => response.json())
                .then(data => {
                    trayectos = data;
                    generarCalendario(vistaActual);
                })
                .catch(error => console.error('Error al cargar los trayectos:', error));
        }
        function cambiarVista(vista) {
            vistaActual = vista;
            generarCalendario(vistaActual);
        }
        function generarCalendario(vista) {
            if (vista === 'mensual') {
                generarVistaMensual();
                mostrarBotonesNavegacion('mensual');
            } else if (vista === 'semanal') {
                generarVistaSemanal();
                mostrarBotonesNavegacion('semanal');
            } else if (vista === 'diaria') {
                generarVistaDiaria();
                mostrarBotonesNavegacion('diaria');
            }
        }
        function generarVistaMensual() {
    const diasEnMes = new Date(anioActual, mesActual + 1, 0).getDate();
    const primerDiaSemana = new Date(anioActual, mesActual, 1).getDay();
    const nombreMes = new Date(anioActual, mesActual).toLocaleString('default', { month: 'long' });
    let html = `<h4>${nombreMes} ${anioActual}</h4><table><tr>`;
    for (let i = 0; i < primerDiaSemana; i++) {
        html += `<td></td>`;
    }
    for (let dia = 1; dia <= diasEnMes; dia++) {
        const fecha = new Date(anioActual, mesActual, dia);
        const reservasDelDia = trayectos.filter(trayecto => {
            let fechaTrayecto = new Date(trayecto.fecha_entrada);
            return fechaTrayecto.toDateString() === fecha.toDateString();
        });
        let indicadorReservas = reservasDelDia.length > 0 ? `<span style="color: red;">●</span>` : '';
        html += `<td onclick="mostrarReservasDelDia(${anioActual}, ${mesActual + 1}, ${dia})">
                    ${dia} ${indicadorReservas}
                 </td>`;
        if ((dia + primerDiaSemana) % 7 === 0) html += `</tr><tr>`;
    }
    html += `</tr></table>`;
    document.getElementById('calendar').innerHTML = html;
}
        function generarVistaSemanal() {
    let hoy = new Date(fechaSeleccionada);
    let inicioSemana = new Date(hoy);
    inicioSemana.setDate(hoy.getDate() - hoy.getDay() + 1); // Lunes de la semana
    let finSemana = new Date(inicioSemana);
    finSemana.setDate(inicioSemana.getDate() + 6); // Domingo de la semana
    let html = `<h4>Semana del ${inicioSemana.toDateString()} al ${finSemana.toDateString()}</h4><table><tr>`;
    for (let i = 0; i < 7; i++) {
        let dia = new Date(inicioSemana);
        dia.setDate(inicioSemana.getDate() + i);
        // Filtrar reservas para el día actual
        let reservasDelDia = trayectos.filter(trayecto => {
            let fechaTrayecto = new Date(trayecto.fecha_entrada);
            return fechaTrayecto.toDateString() === dia.toDateString();
        });
        // Mostrar indicador si hay reservas
        let indicadorReservas = reservasDelDia.length > 0 ? `<span style="color: red;">●</span>` : '';
        html += `<td onclick="mostrarReservasDelDia(${dia.getFullYear()}, ${dia.getMonth() + 1}, ${dia.getDate()})">
                    ${dia.toDateString()} ${indicadorReservas}
                 </td>`;
    }
    html += `</tr></table>`;
    document.getElementById('calendar').innerHTML = html;
}
        function generarVistaDiaria() {
    let dia = new Date(fechaSeleccionada);
    const diaStr = dia.toDateString();
    let reservasDelDia = trayectos.filter(trayecto => {
        let fechaTrayecto = new Date(trayecto.fecha_entrada);
        return fechaTrayecto.toDateString() === diaStr;
    });
    let html = `<h4>Reservas del ${diaStr}</h4><ul>`;
    if (reservasDelDia.length === 0) {
        html += `<li>No hay reservas para este día</li>`;
    } else {
        reservasDelDia.forEach(reserva => {
            html += `<li>${reserva.localizador} - ${reserva.tipo_trayecto} - ${reserva.vehiculo} - ${reserva.hotel}</li>`;
        });
    }
    html += `</ul>`;
    document.getElementById('calendar').innerHTML = html;
}
        function mostrarBotonesNavegacion(vista) {
            let html = '';
            if (vista === 'mensual') {
                html = `<button onclick="mesActual--; generarVistaMensual()">← Mes Anterior</button>
                        <button onclick="mesActual++; generarVistaMensual()">Mes Siguiente →</button>`;
            } else if (vista === 'semanal') {
                html = `<button onclick="fechaSeleccionada.setDate(fechaSeleccionada.getDate() - 7); generarVistaSemanal()">← Semana Anterior</button>
                        <button onclick="fechaSeleccionada.setDate(fechaSeleccionada.getDate() + 7); generarVistaSemanal()">Semana Siguiente →</button>`;
            } else if (vista === 'diaria') {
                html = `<button onclick="fechaSeleccionada.setDate(fechaSeleccionada.getDate() - 1); generarVistaDiaria()">← Día Anterior</button>
                        <button onclick="fechaSeleccionada.setDate(fechaSeleccionada.getDate() + 1); generarVistaDiaria()">Día Siguiente →</button>`;
            }
            document.getElementById('nav-buttons').innerHTML = html;
        }
        function mostrarReservasDelDia(anio, mes, dia) {
    console.log(`Día seleccionado: ${anio}-${mes}-${dia}`); // Comprobar si la función se ejecuta
    console.log('Trayectos disponibles:', trayectos); // Verificar los datos cargados
            fechaSeleccionada = new Date(anio, mes - 1, dia);
            const diaStr = fechaSeleccionada.toDateString();
            let reservasDelDia = trayectos.filter(trayecto => {
                let fechaTrayecto = new Date(trayecto.fecha_entrada);
                return fechaTrayecto.toDateString() === diaStr;
            });
            const modalTitulo = document.getElementById('modalTitulo');
            const modalContenido = document.getElementById('modalContenido');
            modalTitulo.textContent = `Reservas del ${diaStr}`;
            modalContenido.innerHTML = '';
            if (reservasDelDia.length === 0) {
                modalContenido.innerHTML = '<li>No hay reservas para este día</li>';
            } else {
                reservasDelDia.forEach(reserva => {
                    modalContenido.innerHTML += `
                        <li>
                            Localizador: ${reserva.localizador || 'N/A'}<br>
                            Hotel: ${reserva.hotel ? reserva.hotel.usuario : 'N/A'}<br>
                            Tipo de Trayecto: ${reserva.tipoReserva ? reserva.tipoReserva.descripcion : 'N/A'}<br>
                            Hora de Entrada: ${reserva.hora_entrada || 'N/A'}
                        </li>
                        <hr>
                    `;
                });
            }
            document.getElementById('modalReservas').style.display = 'block';
        }
        function cerrarModal() {
            document.getElementById('modalReservas').style.display = 'none';
        }
    </script>
</div>
@endsection