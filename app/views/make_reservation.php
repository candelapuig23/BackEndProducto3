<?php
session_start();
require_once "../models/ReservationModel.php";
require_once "../models/UserModel.php";
require_once "../controllers/UserController.php";
if (!function_exists('getLoggedUserData')) {
    function getLoggedUserData() {
        if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'user') {
            global $userModel;
            return $userModel->getUserById($_SESSION['user_id']);
        }
        return null;
    }
}
require_once "../models/Database.php";

// Conexión a la base de datos
$database = new Database();
$db = $database->getConnection();
$reservationModel = new ReservationModel($db);
$userModel = new UserModel($db);

// Obtener la lista de vehículos y tipos de trayecto
$vehiculos = $reservationModel->getVehiculos();
$tiposTrayecto = $reservationModel->getTiposTrayecto();
$hoteles = $reservationModel->gethoteles();
$zonas = $reservationModel->getZonas();

// Obtener datos del usuario logueado
$userData = getLoggedUserData();
$nombre = $userData ? $userData['nombre'] : '';
$direccion = $userData ? $userData['direccion'] : '';
$email = $userData ? $userData['email'] : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Reserva</title>
    <link rel="stylesheet" href="../css/style.css"> <!-- Link a los estilos -->
</head>
<body>

<div class="container">
    <form action="procesar_reserva.php" method="POST" id="reservationForm" class="formulario">
        <h2>Formulario de Reserva de Traslado</h2>

        <!-- Selección del tipo de trayecto desde la base de datos -->
        <label for="trayecto">Tipo de Trayecto:</label>
        <select name="trayecto" id="trayecto" onchange="mostrarCamposTrayecto()" required>
            <option value="">Selecciona un trayecto</option>
            <?php foreach ($tiposTrayecto as $tipo): ?>
                <option value="<?= strtolower($tipo['Descripción']) ?>"><?= $tipo['Descripción'] ?></option>
            <?php endforeach; ?>
        </select>
        <span id="trayectoError" class="error-message"></span><br><br>

        <!-- Campos de Aeropuerto a Hotel -->
        <div id="aeropuertoHotelFields" class="trayecto-fields" style="display:none;">
            <h3>Aeropuerto a Hotel</h3>
            <label for="diaLlegada">Día de llegada:</label>
            <input type="date" id="diaLlegada" name="diaLlegada"><br><br>

            <label for="horaLlegada">Hora de llegada:</label>
            <input type="time" id="horaLlegada" name="horaLlegada"><br><br>

            <label for="numeroVuelo">Número de vuelo:</label>
            <input type="text" id="numeroVuelo" name="numeroVuelo"><br><br>

            <label for="aeropuertOrigen">Aeropuerto de Origen:</label>
            <input type="text" id="aeropuertOrigen" name="aeropuertOrigen"><br><br>
        </div>

        <!-- Campos de Hotel a Aeropuerto -->
        <div id="hotelAeropuertoFields" class="trayecto-fields" style="display:none;">
            <h3>Hotel a Aeropuerto</h3>
            <label for="diaVuelo">Día del vuelo:</label>
            <input type="date" id="diaVuelo" name="diaVuelo"><br><br>

            <label for="horaVuelo">Hora del vuelo:</label>
            <input type="time" id="horaVuelo" name="horaVuelo"><br><br>

            <label for="numeroVueloRegreso">Número de vuelo:</label>
            <input type="text" id="numeroVueloRegreso" name="numeroVueloRegreso"><br><br>

            <label for="horaRecogida">Hora de recogida:</label>
            <input type="time" id="horaRecogida" name="horaRecogida"><br><br>
        </div>

        <!-- Selección de Zona -->
        <label for="idZona">Seleccione una Zona:</label>
        <select id="idZona" name="idZona" required>
            <option value="">Seleccione una Zona</option>
            <?php foreach ($zonas as $zona): ?>
                <option value="<?= $zona['id_zona'] ?>"><?= $zona['descripcion'] ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <!-- Selección de vehículo -->
        <label for="idVehiculo">Seleccione un Vehículo:</label>
        <select id="idVehiculo" name="idVehiculo" required>
            <option value="">Seleccione un vehículo</option>
            <?php foreach ($vehiculos as $vehiculo): ?>
                <option value="<?= $vehiculo['id_vehiculo'] ?>"><?= $vehiculo['Descripción'] ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <!-- Hotel de destino/recogida -->
        <label for="hotelDestino">Hotel de destino/recogida:</label>
        <select name="hotelDestino" id="hotelDestino" required>
            <option value="">Seleccione un hotel</option>
            <?php foreach ($hoteles as $hotel): ?>
                <option value="<?= $hotel['id_hotel']; ?>"><?= $hotel['usuario']; ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <!-- Número de viajeros -->
        <label for="numViajeros">Número de viajeros:</label>
        <input type="number" id="numViajeros" name="numViajeros" min="1" required><br><br>

        <!-- Datos del Cliente -->
        <h3>Datos del Cliente</h3>
        <label for="email">Correo electrónico:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required><br><br>

        <label for="nombre">Nombre completo:</label>
        <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($nombre) ?>" required><br><br>

        <button type="submit">Realizar reserva</button>
    </form>
</div>

<script>
// Mostrar campos de trayecto
function mostrarCamposTrayecto() {
    const trayecto = document.getElementById("trayecto").value;
    const aeropuertoHotelFields = document.getElementById("aeropuertoHotelFields");
    const hotelAeropuertoFields = document.getElementById("hotelAeropuertoFields");

    aeropuertoHotelFields.style.display = trayecto.includes("aeropuerto a hotel") || trayecto.includes("ida y vuelta") ? "block" : "none";
    hotelAeropuertoFields.style.display = trayecto.includes("hotel a aeropuerto") || trayecto.includes("ida y vuelta") ? "block" : "none";
}
</script>

</body>
</html>
