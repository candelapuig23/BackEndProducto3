<?php
ob_start();
session_start();

require_once 'controllers/UserController.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $zona = $_POST['id_zona'];
    $comision = $_POST['comision'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $userController = new UserController();

    $registerSuccess = $userController->registrarHotel($zona, $comision, $email, $password);

    if ($registerSuccess) {
        header("Location: /views/login.php?success=1");
        exit();
    } else {
        echo "Error al registrar el hotel. Intenta nuevamente.";
    }
}
?>