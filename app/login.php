<?php
ob_start();
session_start();
require_once 'controllers/UserController.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role']; // Capturar el rol seleccionado

    $userController = new UserController();

    // Verificar el inicio de sesión según el rol
    if ($role === 'user') {
        $loginSuccess = $userController->login($email, $password, 'user');
    } elseif ($role === 'admin') {
        $loginSuccess = $userController->login($email, $password, 'admin');
    } else {
        $loginSuccess = false;
    }

    // Redirección si el inicio de sesión es exitoso
    if ($loginSuccess) {
        // Asignar el 'user_id' y 'role' a la sesión
        $_SESSION['user_id'] = $loginSuccess['user_id']; // Suponiendo que el controlador devuelve el usuario
        $_SESSION['role'] = $role;

        if ($_SESSION['role'] === 'user') {
            header("Location: /views/user_dashboard.php");
            exit();
        } elseif ($_SESSION['role'] === 'admin') {
            header("Location: /views/admin_dashboard.php");
            exit();
        }
    } else {
        echo "Email o contraseña incorrectos.";
    }
}
?>
