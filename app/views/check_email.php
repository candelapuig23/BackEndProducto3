<?php
require_once "../models/Database.php";
require_once "../models/ReservationModel.php";

// Conexión a la base de datos
$database = new Database();
$db = $database->getConnection();
$reservationModel = new ReservationModel($db);

if (isset($_POST['email'])) {
    $email = $_POST['email'];
    $user = $reservationModel->checkEmail($email);
    if ($user) {
        echo json_encode([
            'exists' => true,
            'data' => [
                'nombre' => $user['nombre'],
                'telefono' => $user['telefono']
            ]
        ]);
    } else {
        echo json_encode(['exists' => false]);
    }
}
?>
