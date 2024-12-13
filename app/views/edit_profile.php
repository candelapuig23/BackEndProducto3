<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: /views/login.php"); // Redirigir al login si no está autenticado
    exit();
}

require_once "../models/Database.php";
require_once "../models/UserModel.php"; // Modelo para manejar la actualización del usuario

// Conexión a la base de datos
$database = new Database();
$db = $database->getConnection();
$userModel = new UserModel($db);

// Obtener la información del usuario actual
$user = $userModel->getUserById($_SESSION['user_id'], $_SESSION['role']); // Considerar el rol al obtener los datos del usuario

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger los datos del formulario
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    $role = $_SESSION['role']; // Obtener el rol del usuario desde la sesión

    // Validar los campos
    if (empty($nombre) || empty($email)) {
        $error = "El nombre y el correo son obligatorios.";
    } elseif (!empty($newPassword) && $newPassword !== $confirmPassword) {
        $error = "Las contraseñas no coinciden.";
    } else {
        // Si hay una nueva contraseña, la encriptamos
        $hashedPassword = !empty($newPassword) ? password_hash($newPassword, PASSWORD_BCRYPT) : null;

        // Actualizar el usuario en la base de datos
        $success = $userModel->updateUser($_SESSION['user_id'], $nombre, $email, $role, $hashedPassword);

        if ($success) {
            header("Location: user_dashboard.php"); // Redirigir al dashboard
            exit();
        } else {
            $error = "Hubo un problema al guardar los cambios.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="../css/edit_profile.css">
</head>
<body>
    <div class="container">
        <h2>Editar Perfil</h2>

        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($user['nombre']); ?>" required>

            <label for="email">Correo electrónico:</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="password">Contraseña actual:</label>
            <input type="password" name="password" id="password">

            <label for="new_password">Nueva contraseña:</label>
            <input type="password" name="new_password" id="new_password">

            <label for="confirm_password">Confirmar nueva contraseña:</label>
            <input type="password" name="confirm_password" id="confirm_password">

            <button type="submit">Guardar cambios</button>
        </form>
    </div>
</body>
</html>
