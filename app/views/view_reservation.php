<?php
session_start();

// Verificar si el usuario está logueado y es un usuario con rol 'user'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    // Redirigir al login si no está logueado o no tiene el rol adecuado
    header("Location: /views/login.php");
    exit();
}

// Crear instancia del controlador de usuario
require_once '../controllers/UserController.php';
$userController = new UserController();

// Obtener la información del usuario actual
$user = $userController->getUser($_SESSION['user_id']);

// Obtener la información de la reserva
$reservationId = $_GET['id'];
$reservation = $userController->getReservationDetails($reservationId);

if (!$reservation) {
    echo "Reserva no encontrada.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de la Reserva</title>
</head>
<body>
    <h2>Detalles de la Reserva #<?php echo $reservation['id']; ?></h2>

    <p><strong>Tipo de trayecto:</strong> <?php echo htmlspecialchars($reservation['type']); ?></p>
    <p><strong>Fecha:</strong> <?php echo htmlspecialchars($reservation['date']); ?></p>
    <p><strong>Hora:</strong> <?php echo htmlspecialchars($reservation['time']); ?></p>
    <p><strong>Número de vuelo:</strong> <?php echo htmlspecialchars($reservation['flight_number']); ?></p>
    <p><strong>Aeropuerto de origen:</strong> <?php echo htmlspecialchars($reservation['airport']); ?></p>
    <p><strong>Hotel de destino/recogida:</strong> <?php echo htmlspecialchars($reservation['hotel']); ?></p>
    <p><strong>Número de viajeros:</strong> <?php echo htmlspecialchars($reservation['travellers']); ?></p>
    <p><strong>Localizador:</strong> <?php echo htmlspecialchars($reservation['locator']); ?></p>

    <br>
    <a href="user_dashboard.php">Volver al Panel de Usuario</a>
</body>
</html>
