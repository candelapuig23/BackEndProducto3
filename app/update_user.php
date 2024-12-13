<?php
// Incluir el controlador de usuario
require_once __DIR__ . '/controllers/UserController.php';

// Verificar que la solicitud sea PUT
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Recoger los datos del cuerpo de la solicitud
    parse_str(file_get_contents("php://input"), $putData);
    
    // Validar que los parámetros necesarios están presentes
    $id = $putData['id'] ?? null;
    $name = $putData['name'] ?? null;
    $email = $putData['email'] ?? null;
    
    if ($id && $name && $email) {
        echo "Intentando actualizar el usuario con ID: " . $id . "<br>";

        // Llamar al controlador de actualización
        $userController = new UserController();
        $userController->updateUser($id, [
            'name' => $name,
            'apellido1' => $putData['apellido1'] ?? '',
            'apellido2' => $putData['apellido2'] ?? '',
            'direccion' => $putData['direccion'] ?? '',
            'codigoPostal' => $putData['codigoPostal'] ?? '',
            'ciudad' => $putData['ciudad'] ?? '',
            'pais' => $putData['pais'] ?? '',
            'email' => $email
        ]);
    } else {
        echo "Faltan parámetros. Asegúrese de enviar ID, nombre y correo electrónico.";
    }
} else {
    echo "Método no permitido.";
}
