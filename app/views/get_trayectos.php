<?php
require_once "../models/Database.php";
require_once "../models/ReservationModel.php";

$database = new Database();
$db = $database->getConnection();
$reservationModel = new ReservationModel($db);

// Ajustar el nivel de error para no mostrar warnings al navegador
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

$reservations = $reservationModel->getAllReservations();
$data = [];

foreach ($reservations as $reservation) {
    $start = !empty($reservation['fecha_entrada']) ? date('Y-m-d\TH:i:s', strtotime($reservation['fecha_entrada'])) : null;
    $end = !empty($reservation['fecha_vuelo_salida']) ? date('Y-m-d\TH:i:s', strtotime($reservation['fecha_vuelo_salida'])) : null;
    
    $data[] = [
        'id' => $reservation['id'],
        'title' => "Trayecto: {$reservation['locator']}",
        'start' => $start,
        'end' => $end,
        'description' => "Cliente: {$reservation['email']}, Hotel: {$reservation['hotel']}"
    ];
}

// Enviar encabezados y salida JSON
header('Content-Type: application/json');
echo json_encode($data);
