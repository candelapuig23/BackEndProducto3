<?php
session_start();

// Verificar si el usuario tiene el rol 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /views/login.php");
    exit();
}

// Crear instancia del controlador de usuario
require_once "../models/Database.php";
require_once "../models/ReservationModel.php";
require_once '../controllers/UserController.php';

$userController = new UserController();
$database = new Database();
$db = $database->getConnection();
$reservationModel = new ReservationModel($db);

// Obtener la información del usuario actual
$user = $userController->getUser($_SESSION['user_id']);
$reservations = $reservationModel->getAllReservations();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css"> <!-- Enlace al nuevo archivo de estilos -->
</head>
<body>
    <div class="dashboard-container">
        <?php if ($user && is_array($user)): ?>
            <h2>Bienvenido, <?php echo htmlspecialchars($user['nombre']); ?>!</h2>
        <?php else: ?>
            <h2>Bienvenido, Administrador!</h2>
        <?php endif; ?>

        <h3>Gestión de todas las Reservas</h3>

        <?php if (empty($reservations)): ?>
            <div class="no-reservations">
                <p>No hay reservas disponibles.</p>
            </div>
        <?php else: ?>
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
                    <?php foreach ($reservations as $reservation): ?>
                        <tr>
                            <td><?= htmlspecialchars($reservation['id']) ?></td>
                            <td><?= htmlspecialchars($reservation['locator']) ?></td>
                            <td><?= htmlspecialchars($reservation['email']) ?></td>
                            <td><?= htmlspecialchars($reservation['num_viajeros']) ?></td>
                            <td><?= htmlspecialchars($reservation['date']) ?></td>
                            <td><?= htmlspecialchars($reservation['fecha_entrada']) ?></td>
                            <td><?= htmlspecialchars($reservation['tipo_trayecto']) ?></td>
                            <td><?= htmlspecialchars($reservation['vehiculo']) ?></td>
                            <td><?= htmlspecialchars($reservation['hotel']) ?></td>
                            <td>
                                <a href="edit_reservation.php?id=<?= htmlspecialchars($reservation['id']) ?>">Editar</a> |
                                <a href="cancel_reservation.php?id=<?= htmlspecialchars($reservation['id']) ?>">Cancelar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <h3>Calendario de Trayectos</h3>

        <!-- Botones para cambiar la vista -->
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
                <li><a href="make_reservation.php">Hacer nueva reserva</a></li>
                <li><a href="edit_profile.php">Editar Perfil</a></li>
                <li><a href="../logout.php">Cerrar sesión</a></li>
            </ul>
        </div>
    </div>

    <script>
        let mesActual, anioActual, trayectos, vistaActual, fechaSeleccionada;

        document.addEventListener('DOMContentLoaded', function () {
            mesActual = new Date().getMonth() + 1;
            anioActual = new Date().getFullYear();
            vistaActual = 'mensual'; // La vista predeterminada es mensual
            fechaSeleccionada = new Date(); // Fecha seleccionada inicialmente

            // Carga los trayectos desde el servidor
            fetch('/views/get_trayectos.php')
                .then(response => response.json())
                .then(data => {
                    trayectos = data;
                    generarCalendario(vistaActual, mesActual, anioActual);
                })
                .catch(error => console.error('Error al cargar los trayectos:', error));
        });

        // Cambiar la vista de calendario
        function cambiarVista(vista) {
            vistaActual = vista;
            generarCalendario(vistaActual, mesActual, anioActual);
        }

        function generarCalendario(vista, mes, anio) {
            if (vista === 'mensual') {
                generarVistaMensual(mes, anio);
            } else if (vista === 'semanal') {
                generarVistaSemanal(fechaSeleccionada);
            } else if (vista === 'diaria') {
                generarVistaDiaria(fechaSeleccionada);
            }
        }

        // Generar la vista mensual
        function generarVistaMensual(mes, anio) {
            const primerDia = new Date(anio, mes - 1, 1);
            const ultimoDia = new Date(anio, mes, 0);
            const diasEnMes = ultimoDia.getDate();
            const primerDiaSemana = (primerDia.getDay() + 6) % 7;

            let diaActual = new Date(primerDia);
            let celdas = 0;
            let eventos = "";
            let calendarHtml = `
                <div class="table calendar">
                    <div class="caption">
                        <button onclick="mesAnterior()">Mes Anterior</button>
                        <span>${new Intl.DateTimeFormat('es', { month: 'long', year: 'numeric' }).format(diaActual)}</span>
                        <button onclick="mesSiguiente()">Mes Siguiente</button>
                    </div>
                    <table>
                        <tr>
                            <th>Lunes</th><th>Martes</th><th>Miércoles</th>
                            <th>Jueves</th><th>Viernes</th><th>Sábado</th><th>Domingo</th>
                        </tr>
                        <tr>
            `;

            // Días vacíos antes del primer día del mes
            for (let i = 0; i < primerDiaSemana; i++) {
                calendarHtml += '<td></td>';
            }

            for (let dia = 1; dia <= diasEnMes; dia++) {
                calendarHtml += '<td>';
                calendarHtml += `<div class="dia">${dia}</div>`;

                const eventosDelDia = trayectos.filter(trayecto => {
                    const fechaTrayecto = new Date(trayecto.start);
                    return (
                        fechaTrayecto.getDate() === dia &&
                        fechaTrayecto.getMonth() + 1 === mes &&
                        fechaTrayecto.getFullYear() === anio
                    );
                });

                if (eventosDelDia.length > 0) {
                    calendarHtml += '<ul>';
                    eventosDelDia.forEach(evento => {
                        calendarHtml += `
                            <li>
                                <span class="titulo">${evento.title}</span>
                                <button onclick="mostrarInfo(${evento.id})" class="more">+</button>
                            </li>
                        `;
                        eventos += `
                            <div id="info_${evento.id}" class="popup" style="display: none;">
                                <div class="info_popup">
                                    <div class="info">
                                        <p><b>Título:</b> ${evento.title}</p>
                                        <p><b>Descripción:</b> ${evento.description}</p>
                                        <p><b>Inicio:</b> ${evento.start}</p>
                                        <p><b>Fin:</b> ${evento.end || 'No definido'}</p>
                                    </div>
                                    
                                    <button class="close-btn" onclick="cerrarPopup('info_${evento.id}')">Cerrar</button>
                                    </div>
                            </div>
                        `;
                    });
                    calendarHtml += '</ul>';
                }
                calendarHtml += '</td>';

                if (diaActual.getDay() === 0 && dia !== diasEnMes) {
                    celdas = 0;
                    calendarHtml += '</tr><tr>';
                }

                diaActual.setDate(diaActual.getDate() + 1);
                celdas++;
            }

            while (celdas <= 7) {
                calendarHtml += '<td></td>';
                celdas++;
            }

            calendarHtml += '</tr></table></div>';
            document.getElementById('calendar').innerHTML = calendarHtml;
            document.getElementById('eventos').innerHTML = eventos;
        }

        // Generar la vista semanal
        function generarVistaSemanal(fecha) {
            const primerDiaDeLaSemana = new Date(fecha);
            primerDiaDeLaSemana.setDate(fecha.getDate() - fecha.getDay() + 1);

            let calendarHtml = `
                <div class="table calendar">
                    <div class="caption">
                        <button onclick="semanaAnterior()">Semana Anterior</button>
                        <span>${new Intl.DateTimeFormat('es', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' }).format(primerDiaDeLaSemana)} - ${new Intl.DateTimeFormat('es', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' }).format(new Date(primerDiaDeLaSemana).setDate(primerDiaDeLaSemana.getDate() + 6))}</span>
                        <button onclick="semanaSiguiente()">Semana Siguiente</button>
                    </div>
                    <table>
                        <tr>
                            <th>Lunes</th><th>Martes</th><th>Miércoles</th><th>Jueves</th><th>Viernes</th><th>Sábado</th><th>Domingo</th>
                        </tr>
                        <tr>
            `;

            for (let i = 0; i < 7; i++) {
                const fechaDia = new Date(primerDiaDeLaSemana);
                fechaDia.setDate(primerDiaDeLaSemana.getDate() + i);
                
                const eventosDelDia = trayectos.filter(trayecto => {
                    const fechaTrayecto = new Date(trayecto.start);
                    return (
                        fechaTrayecto.getDate() === fechaDia.getDate() &&
                        fechaTrayecto.getMonth() === fechaDia.getMonth() &&
                        fechaTrayecto.getFullYear() === fechaDia.getFullYear()
                    );
                });

                calendarHtml += `<td>${fechaDia.getDate()}<ul>`;
                eventosDelDia.forEach(evento => {
                    calendarHtml += `
                        <li>
                            <span class="titulo">${evento.title}</span>
                            <button onclick="mostrarInfo(${evento.id})" class="more">+</button>
                        </li>
                    `;
                });
                calendarHtml += `</ul></td>`;
            }

            calendarHtml += '</tr></table></div>';

            document.getElementById('calendar').innerHTML = calendarHtml;
        }

        // Generar la vista diaria
// Generar la vista diaria
function generarVistaDiaria(fecha) {
    let calendarHtml = `
        <div class="table calendar">
            <div class="caption">
                <button onclick="diaAnterior()">Día Anterior</button>
                <span>${new Intl.DateTimeFormat('es', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }).format(fecha)}</span>
                <button onclick="diaSiguiente()">Día Siguiente</button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Evento</th>
                        <th>Hora de Inicio</th>
                        <th>Hora de Fin</th>
                    </tr>
                </thead>
                <tbody>
    `;

    // Filtrar eventos para la fecha seleccionada
    const eventosDelDia = trayectos.filter(trayecto => {
        const fechaTrayecto = new Date(trayecto.start);
        return (
            fechaTrayecto.getDate() === fecha.getDate() &&
            fechaTrayecto.getMonth() === fecha.getMonth() &&
            fechaTrayecto.getFullYear() === fecha.getFullYear()
        );
    });

    if (eventosDelDia.length === 0) {
        calendarHtml += `
            <tr>
                <td colspan="3">No hay eventos programados para este día.</td>
            </tr>
        `;
    } else {
        eventosDelDia.forEach(evento => {
            const startTime = new Date(evento.start).toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
            const endTime = evento.end ? new Date(evento.end).toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' }) : 'No definido';
            calendarHtml += `
                <tr>
                    <td>
                        <span class="titulo">${evento.title}</span>
                        <button class="more" onclick="mostrarInfo(${evento.id})">+</button>
                    </td>
                    <td>${startTime}</td>
                    <td>${endTime}</td>
                </tr>
                <div id="info_${evento.id}" class="popup" style="display: none;">
                    <div class="info_popup">
                        <div class="info">
                            <p><b>Título:</b> ${evento.title}</p>
                            <p><b>Descripción:</b> ${evento.description}</p>
                            <p><b>Inicio:</b> ${evento.start}</p>
                            <p><b>Fin:</b> ${evento.end || 'No definido'}</p>
                        </div>
                        <button class="close-btn" onclick="cerrarPopup('info_${evento.id}')">Cerrar</button>
                    </div>
                </div>
            `;
        });
    }

    calendarHtml += `
                </tbody>
            </table>
        </div>
    `;

    document.getElementById('calendar').innerHTML = calendarHtml;
}

// Función para mostrar el pop-up
function mostrarInfo(id) {
    document.getElementById(`info_${id}`).style.display = 'flex';
}

// Función para cerrar el pop-up
function cerrarPopup(id) {
    document.getElementById(id).style.display = 'none';
}


// Navegación entre días
function diaAnterior() {
    fechaSeleccionada.setDate(fechaSeleccionada.getDate() - 1);
    generarVistaDiaria(fechaSeleccionada);
}

function diaSiguiente() {
    fechaSeleccionada.setDate(fechaSeleccionada.getDate() + 1);
    generarVistaDiaria(fechaSeleccionada);
}

// Funciones de navegación para meses y semanas
function mesAnterior() {
    mesActual--;
    if (mesActual < 1) {
        mesActual = 12;
        anioActual--;
    }
    generarCalendario(vistaActual, mesActual, anioActual);
}

function mesSiguiente() {
    mesActual++;
    if (mesActual > 12) {
        mesActual = 1;
        anioActual++;
    }
    generarCalendario(vistaActual, mesActual, anioActual);
}

function semanaAnterior() {
    fechaSeleccionada.setDate(fechaSeleccionada.getDate() - 7);
    generarVistaSemanal(fechaSeleccionada);
}

function semanaSiguiente() {
    fechaSeleccionada.setDate(fechaSeleccionada.getDate() + 7);
    generarVistaSemanal(fechaSeleccionada);
}


        function diaAnterior() {
            fechaSeleccionada.setDate(fechaSeleccionada.getDate() - 1);
            generarVistaDiaria(fechaSeleccionada);
        }

        function diaSiguiente() {
            fechaSeleccionada.setDate(fechaSeleccionada.getDate() + 1);
            generarVistaDiaria(fechaSeleccionada);
        }

        function mostrarInfo(id) {
            document.getElementById(`info_${id}`).style.display = 'flex';
        }

        function cerrarPopup(id) {
            document.getElementById(id).style.display = 'none';
        }
    </script>
</body>
</html>
