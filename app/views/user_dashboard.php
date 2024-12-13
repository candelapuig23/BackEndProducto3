<?php
ob_start();
session_start(); 

// Verificar si el usuario está logueado y tiene el rol 'user'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: /views/login.php"); // Redirigir al login si no cumple
    exit();
}

// Incluir los archivos necesarios
require_once "../models/Database.php";
require_once "../models/ReservationModel.php";
require_once '../controllers/UserController.php';

$userController = new UserController();
$database = new Database();
$db = $database->getConnection();
$reservationModel = new ReservationModel($db);

// Obtener la información del usuario actual
$user = $userController->getUser($_SESSION['user_id']);
$reservations = $reservationModel->getReservationsWithDetails($_SESSION['user_email']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Usuario</title>
    <link rel="stylesheet" href="../css/user_dashboard.css"> <!-- Enlace al nuevo archivo de estilos -->
</head>
<body>
    <div class="dashboard-container">
        <?php if ($user && is_array($user)): ?>
            <h2>Bienvenido, <?php echo htmlspecialchars($user['nombre']); ?>!</h2>
        <?php else: ?>
            <h2>Bienvenido, Usuario!</h2>
            <p>Error al obtener los datos del usuario.</p>
        <?php endif; ?>

        <div class="reservations-section">
            <h3>Mis Reservas</h3>

            <?php if (empty($reservations)): ?>
                <div class="no-reservations">
                    <p>No tienes reservas.</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Localizador</th>
                            <th>Tipo de Trayecto</th>
                            <th>Vehículo</th>
                            <th>Fecha de Reserva</th>
                            <th>Realizado por</th> <!-- Nueva columna para mostrar el rol -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $reservation): ?>
                            <tr>
                                <td><?= htmlspecialchars($reservation['localizador']) ?></td>
                                <td><?= htmlspecialchars($reservation['tipo_trayecto']) ?></td>
                                <td><?= htmlspecialchars($reservation['vehiculo']) ?></td>
                                <td><?= htmlspecialchars($reservation['fecha_reserva']) ?></td>
                                <td>
                                    <?php
                                    // Mostrar el rol de la persona que realizó la reserva (basado en $_SESSION)
                                    if ($_SESSION['role'] === 'admin') {
                                        echo 'Admin';  // Si el que hace la reserva es un admin
                                    } else {
                                        echo 'Usuario'; // Si es un usuario normal
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="dashboard-options">
            <h3>Opciones</h3>
            <ul>
                <li><a href="make_reservation.php">Hacer nueva reserva</a></li>
                <li><a href="edit_profile.php">Editar Perfil</a></li>
                <li><a href="../logout.php">Cerrar sesión</a></li>
            </ul>
        </div>
    </div>
</body>
</html>
