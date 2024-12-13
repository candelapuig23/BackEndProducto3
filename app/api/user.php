<?php
require_once __DIR__ . '/../controllers/UserController.php';

// Cabeceras para permitir CORS y solicitudes JSON
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Obtener el método de la solicitud
$method = $_SERVER['REQUEST_METHOD'];
$userController = new UserController();

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            // Obtener usuario específico
            $id = intval($_GET['id']);
            $userController->getUser($id);
        } else {
            // Obtener todos los usuarios
            $userController->listUsers();
        }
        break;

    case 'POST':
        // Leer el cuerpo de la solicitud JSON
        $data = json_decode(file_get_contents("php://input"), true);
        $userController->createUser($data['name'], $data['email']);
        break;

    case 'PUT':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $data = json_decode(file_get_contents("php://input"), true);
            $userController->updateUser($id, $data);
        }
        break;

    case 'DELETE':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $userController->deleteUser($id);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['message' => 'Método no permitido']);
        break;
}
?>
