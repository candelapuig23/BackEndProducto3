<?php
session_start();

require_once "../models/Database.php";
require_once "../models/ReservationModel.php";

$database = new Database();
$db = $database->getConnection();
$reservationModel = new ReservationModel($db);

$id = $_GET['id'];
$reservation = $reservationModel->getReservationDetailsById($id);

// Función para validar el tiempo mínimo de 48 horas
function validarPeriodoReserva($fecha, $hora) {
    $fechaHora = strtotime($fecha . ' ' . $hora);
    return ($fechaHora - time() >= 48 * 3600);
}

// Verificar si la reserva puede ser cancelada
if (!validarPeriodoReserva($reservation['fecha_entrada'], $reservation['hora_entrada'])) {
    die("No se puede cancelar una reserva con menos de 48 horas de antelación.");
}

// Intentar cancelar la reserva
if ($reservationModel->cancelReservation($id)) {
    header("Location: admin_dashboard.php?message=Reserva cancelada");
    exit();
} else {
    echo "Error al cancelar la reserva.";
}
?>
