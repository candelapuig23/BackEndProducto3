<?php
// Incluimos el controlador de usuarios
require_once 'controllers/UserController.php';

// Verifica si se pasó un ID en la URL y si la solicitud es de tipo DELETE
if (isset($_GET['id']) && $_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = $_GET['id'];

    // Crear una instancia del controlador
    $userController = new UserController();

    // Llamar al método delete del controlador y pasar el ID del usuario
    $userController->deleteUser($id);
} else {
    echo json_encode(["error" => "Método no soportado o no se especificó un ID de usuario."]);
}
?>
