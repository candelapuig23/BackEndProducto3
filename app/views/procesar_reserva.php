<?php
session_start();

require_once "../models/Database.php";
require_once "../models/ReservationModel.php";

// Conexión a la base de datos
$database = new Database();
$db = $database->getConnection();
$reservationModel = new ReservationModel($db);

// Función para validar el tiempo mínimo de 48 horas
function validarPeriodoReserva($fecha, $hora) {
    $fechaHora = strtotime($fecha . ' ' . $hora);
    return ($fechaHora - time() >= 48 * 3600);
}

// Verificar que el rol está definido
if (!isset($_SESSION['role'])) {
    die("Error: Rol no definido en la sesión.");
}

// Recoger los datos del formulario
$data = [
    'trayecto' => $_POST['trayecto'],
    'idZona' => $_POST['idZona'],
    'idVehiculo' => $_POST['idVehiculo'],
    'hotelDestino' => $_POST['hotelDestino'],
    'numViajeros' => $_POST['numViajeros'],
    'email' => $_POST['email'],
    'nombre' => $_POST['nombre'],
    'diaLlegada' => $_POST['diaLlegada'],
    'horaLlegada' => $_POST['horaLlegada'],
    'numeroVuelo' => $_POST['numeroVuelo'],
    'diaVuelo' => $_POST['diaVuelo'],
    'aeropuertOrigen' => $_POST['aeropuertOrigen'],
    'role' => $_SESSION['role']
];

// Combinar la fecha y hora de entrada según el trayecto
if ($data['trayecto'] === 'aeropuerto a hotel' || $data['trayecto'] === 'ida y vuelta') {
    $fechaEntrada = $data['diaLlegada'];
    $horaEntrada = $data['horaLlegada'];
} elseif ($data['trayecto'] === 'hotel a aeropuerto') {
    $fechaEntrada = $data['diaVuelo'];
    $horaEntrada = $data['horaLlegada'];
} else {
    die("Debe especificar un trayecto válido.");
}

// Validar el tiempo mínimo de 48 horas
if (!validarPeriodoReserva($fechaEntrada, $horaEntrada)) {
    die("No se puede crear una reserva con menos de 48 horas de antelación.");
}

// Insertar la reserva en la base de datos
if ($reservationModel->insertReserva($data)) {
    echo "Reserva realizada con éxito.";
} else {
    echo "Hubo un error al procesar la reserva.";
}
?>
