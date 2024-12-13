<?php
// create_user.php
require_once __DIR__ . '/controllers/UserController.php';

// Verificar que se ha enviado una solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger los datos del formulario
    $name = $_POST['name'] ?? '';
    $apellido1 = $_POST['apellido1'] ?? '';
    $apellido2 = $_POST['apellido2'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $codigoPostal = $_POST['codigoPostal'] ?? '';
    $ciudad = $_POST['ciudad'] ?? '';
    $pais = $_POST['pais'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validar que los campos no estén vacíos
    if (!empty($name) && !empty($email)) {
        // Crear la instancia del controlador UserController
        $userController = new UserController();

        // Llamar al método para crear un nuevo usuario
        $userController->createUser($name, $apellido1, $apellido2, $direccion, $codigoPostal, $ciudad, $pais, $email, $password);

        // Redirigir o mostrar un mensaje de éxito
        echo "Usuario creado exitosamente!";
    } else {
        echo "Por favor, complete todos los campos.";
    }
} else {
    // Si no es una solicitud POST, mostrar el formulario de creación
    ?>
    <form method="POST" action="/create_user.php">
        <input type="text" name="name" placeholder="Name" required>
        <input type="text" name="apellido1" placeholder="First Surname" required>
        <input type="text" name="apellido2" placeholder="Second Surname" required>
        <input type="text" name="direccion" placeholder="Address" required>
        <input type="text" name="codigoPostal" placeholder="Postal Code" required>
        <input type="text" name="ciudad" placeholder="City" required>
        <input type="text" name="pais" placeholder="Country" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Create User</button>
    </form>

    <?php
}
?>
