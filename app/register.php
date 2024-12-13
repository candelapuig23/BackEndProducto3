<?php
ob_start();
session_start();

require_once 'controllers/UserController.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Capturamos los datos del formulario
    $nombre = $_POST['nombre'];
    $apellido1 = $_POST['apellido1'];
    $apellido2 = $_POST['apellido2'];
    $direccion = $_POST['direccion'];
    $codigoPostal = $_POST['codigoPostal'];
    $ciudad = $_POST['ciudad'];
    $pais = $_POST['pais'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = 'user'; // El rol se setea como 'user' porque es el tipo de registro para viajeros

    // Instanciamos el controlador de usuario
    $userController = new UserController();

    // Intentamos registrar al viajero
    $registerSuccess = $userController->registrarViajero($nombre, $apellido1, $apellido2, $direccion, $codigoPostal, $ciudad, $pais, $email, $password);

    // Si el registro es exitoso, redirigimos a login
    if ($registerSuccess) {
        header("Location: /views/login.php?success=1");
        exit();
    } else {
        echo "Error al registrar el usuario. Intenta nuevamente.";
    }
}
?>