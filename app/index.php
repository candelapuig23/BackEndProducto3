<?php
// Configuración de la base de datos
$host = 'mysql';  // Nombre del servicio en Docker
$db = 'database';  // Nombre de la base de datos
$user = 'user';  // Nombre de usuario de la base de datos
$pass = 'password';  // Contraseña de la base de datos
$charset = 'utf8mb4';

// DSN (Data Source Name) para PDO
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Opciones de PDO
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Modo de error
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Establecer el modo de recuperación de datos
    PDO::ATTR_EMULATE_PREPARES => false, // No usar preparación emulada
];

// Crear la conexión PDO
try {
    $pdo = new PDO($dsn, $user, $pass, $options);  // Crear la instancia de PDO
} catch (PDOException $e) {
    // Si hay un error al conectar a la base de datos, se captura la excepción
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Importar el controlador del usuario
require_once __DIR__ . '/controllers/UserController.php';

// Crear una instancia del controlador
$userController = new UserController($pdo);

// Lógica de enrutamiento
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestUri === '/create_user.php' && $requestMethod === 'POST') {
    $userController->createUser($_POST['name'], $_POST['email']);
} elseif ($requestUri === '/list_users.php') {
    $userController->listUsers();
} else {
    // Página de inicio
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Página de Inicio</title>
        <link rel="stylesheet" href="/css/style.css">
    </head>
    <div class="container">
        <h2>¡Bienvenido a Isla Transfers!</h2>
        <p>Por favor, selecciona una opción:</p>
        <div class="buttons">
            <a href="/views/login.php">Iniciar Sesión</a>
            <a href="/views/register.php">Registrarse</a>
            <a href="/views/register_hotel.php">Registrarse como hotel</a>
        </div>
    </div>
    </html>
    <?php
}
