<?php
session_start();

require_once "../models/Database.php";
require_once "../models/ReservationModel.php";

// Conexión a la base de datos
$database = new Database();
$db = $database->getConnection();
$reservationModel = new ReservationModel($db);

// Obtener ID de la reserva desde el parámetro GET
$id = $_GET['id'];
$reservation = $reservationModel->getReservationDetailsById($id);

// Función para obtener opciones para campos relacionados
function getOptions($db, $table, $idField, $descField) {
    $query = "SELECT $idField, $descField FROM $table";
    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Listas desplegables
$tiposTrayecto = getOptions($db, 'transfer_tipo_reserva', 'id_tipo_reserva', 'Descripción');
$vehiculos = getOptions($db, 'transfer_vehiculo', 'id_vehiculo', 'Descripción');
$hoteles = getOptions($db, 'tranfer_hotel', 'id_hotel', 'usuario');
$destinos = getOptions($db, 'transfer_zona', 'id_zona', 'descripcion');

// Función para validar el tiempo mínimo de 48 horas
function validarPeriodoReserva($fecha, $hora) {
    $fechaHora = strtotime($fecha . ' ' . $hora);
    return ($fechaHora - time() >= 48 * 3600);
}

// Verificar si la reserva puede ser editada
if (!validarPeriodoReserva($reservation['fecha_entrada'], $reservation['hora_entrada'])) {
    die("No se puede modificar la reserva porque faltan menos de 48 horas para la fecha de entrada.");
}

// Procesar la actualización cuando el formulario se envíe por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updatedData = [
        'id_hotel' => $_POST['id_hotel'] ?? null,
        'id_tipo_reserva' => $_POST['id_tipo_reserva'],
        'email_cliente' => $_POST['email_cliente'],
        'fecha_modificacion' => date('Y-m-d H:i:s'), // Fecha actual
        'id_zona' => $_POST['id_zona'],
        'fecha_entrada' => $_POST['fecha_entrada'],
        'hora_entrada' => $_POST['hora_entrada'],
        'numero_vuelo_entrada' => $_POST['numero_vuelo_entrada'],
        'origen_vuelo_entrada' => $_POST['origen_vuelo_entrada'],
        'hora_vuelo_salida' => $_POST['hora_vuelo_salida'],
        'fecha_vuelo_salida' => $_POST['fecha_vuelo_salida'],
        'num_viajeros' => $_POST['num_viajeros'],
        'id_vehiculo' => $_POST['id_vehiculo']
    ];

    if ($reservationModel->updateReservation($id, $updatedData)) {
        header("Location: admin_dashboard.php?message=Reserva actualizada");
        exit();
    } else {
        echo "Error al actualizar la reserva.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Reserva</title>
</head>
<body>
    <h2>Editar Reserva #<?= htmlspecialchars($id) ?></h2>
    <form method="POST">
        <label>Localizador:</label>
        <input type="text" name="localizador" value="<?= htmlspecialchars($reservation['localizador']) ?>" disabled><br>

        <label>Hotel:</label>
        <select name="id_hotel">
            <?php foreach ($hoteles as $hotel): ?>
                <option value="<?= $hotel['id_hotel'] ?>" 
                    <?= $hotel['id_hotel'] == $reservation['id_hotel'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($hotel['usuario']) ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <label>Tipo de Trayecto:</label>
        <select name="id_tipo_reserva">
            <?php foreach ($tiposTrayecto as $tipo): ?>
                <option value="<?= $tipo['id_tipo_reserva'] ?>" 
                    <?= $tipo['id_tipo_reserva'] == $reservation['id_tipo_reserva'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($tipo['Descripción']) ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <label>Email Cliente:</label>
        <input type="email" name="email_cliente" value="<?= htmlspecialchars($reservation['email_cliente']) ?>" required><br>

        <label>Destino:</label>
        <select name="id_zona">
            <?php foreach ($destinos as $destino): ?>
                <option value="<?= $destino['id_zona'] ?>" 
                    <?= $destino['id_zona'] == $reservation['id_zona'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($destino['descripcion']) ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <label>Fecha de Entrada:</label>
        <input type="date" name="fecha_entrada" value="<?= htmlspecialchars($reservation['fecha_entrada']) ?>" required><br>

        <label>Hora de Entrada:</label>
        <input type="time" name="hora_entrada" value="<?= htmlspecialchars($reservation['hora_entrada']) ?>" required><br>

        <label>Número de Vuelo de Entrada:</label>
        <input type="text" name="numero_vuelo_entrada" value="<?= htmlspecialchars($reservation['numero_vuelo_entrada']) ?>"><br>

        <label>Origen Vuelo de Entrada:</label>
        <input type="text" name="origen_vuelo_entrada" value="<?= htmlspecialchars($reservation['origen_vuelo_entrada']) ?>"><br>

        <label>Hora de Vuelo de Salida:</label>
        <input type="time" name="hora_vuelo_salida" value="<?= htmlspecialchars($reservation['hora_vuelo_salida']) ?>"><br>

        <label>Fecha de Vuelo de Salida:</label>
        <input type="date" name="fecha_vuelo_salida" value="<?= htmlspecialchars($reservation['fecha_vuelo_salida']) ?>"><br>

        <label>Número de Viajeros:</label>
        <input type="number" name="num_viajeros" value="<?= htmlspecialchars($reservation['num_viajeros']) ?>" required><br>

        <label>Vehículo:</label>
        <select name="id_vehiculo">
            <?php foreach ($vehiculos as $vehiculo): ?>
                <option value="<?= $vehiculo['id_vehiculo'] ?>" 
                    <?= $vehiculo['id_vehiculo'] == $reservation['id_vehiculo'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($vehiculo['Descripción']) ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <button type="submit">Actualizar Reserva</button>
    </form>
</body>
</html>
