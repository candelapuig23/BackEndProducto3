<?php

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/ReservationModel.php';

class UserController {
    private $userModel;
    private $reservationModel;

    public function __construct() {
        $this->userModel = new UserModel();
        $this->reservationModel = new ReservationModel($this->userModel->getPDO());  // Usamos la conexión de UserModel
    }

    // Método para listar usuarios
    public function listUsers() {
        $users = $this->userModel->getUsers();
        
        if (empty($users)) {
            echo "No hay usuarios en la base de datos.";
        } else {
            foreach ($users as $user) {
                echo $user['nombre'] . " - " . $user['apellido1'] . "<br>";
            }
        }
    }

    // Método para crear un usuario
    public function createUser($name, $email) {
        $this->userModel->createUser($name, $email);
    }

    public function getUser($user_id) {
        // Verificar que $user_id no esté vacío
        if (empty($user_id)) {
            return false;
        }
    
        // Obtener los datos del usuario desde el modelo
        $user = $this->userModel->getUserById($user_id);
    
        if ($user) {
            return $user;
        } else {
            return false; // Retorna false si no se encuentra el usuario
        }
    }   

    // Método para actualizar un usuario
    public function updateUser($user_id, $data) {
        if ($this->userModel->updateUser($user_id, $data)) {
            echo "Usuario actualizado correctamente.";
        } else {
            echo "Error al actualizar el usuario.";
        }
    }

    // Método para eliminar un usuario
    public function deleteUser($id) {
        if (!is_numeric($id)) {
            echo json_encode(["error" => "El ID especificado no es válido."]);
            return;
        }

        $result = $this->userModel->deleteUserById($id);

        if ($result) {
            echo json_encode(["message" => "Usuario con ID $id eliminado correctamente."]);
        } else {
            echo json_encode(["error" => "No se encontró un usuario con ID $id."]);
        }
    }

    // Función para registrar un usuario
    public function registrarViajero() {
        // Capturar todos los datos del formulario, incluyendo el campo 'role'
        $nombre = $_POST['nombre'];
        $apellido1 = $_POST['apellido1'];
        $apellido2 = $_POST['apellido2'];
        $direccion = $_POST['direccion'];
        $codigoPostal = $_POST['codigoPostal'];
        $ciudad = $_POST['ciudad'];
        $pais = $_POST['pais'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $role = $_POST['role']; // Aquí se captura el 'role'

        // Verificar el tipo de usuario
        if ($role == 'admin') {
            // Registrar en la tabla de administradores
            return $this->userModel->registrarAdmin($nombre, $apellido1, $apellido2, $direccion, $codigoPostal, $ciudad, $pais, $email, $password);
        } else {
            // Registrar en la tabla de viajeros
            return $this->userModel->registrarViajero($nombre, $apellido1, $apellido2, $direccion, $codigoPostal, $ciudad, $pais, $email, $password);
        }
    } 
     
    public function login($email, $password, $role) {
        // Buscar el usuario según el rol
        if ($role === 'user') {
            $user = $this->userModel->getUserByEmailFromViajeros($email);
        } elseif ($role === 'admin') {
            $user = $this->userModel->getUserByEmailFromAdmins($email);
        } else {
            return false; // Rol no válido
        }
    
        // Verificar si el usuario existe
        if (!$user) {
            echo "Usuario no encontrado";
            return false;
        }
    
        // Verificar la contraseña
        if (password_verify($password, $user['password'])) {
            // Iniciar sesión
            $_SESSION['user_id'] = $role === 'user' ? $user['id_viajero'] : $user['id_viajero_admin'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['role'] = $role;
    
            // Redirigir según el rol
            if ($role === 'user') {
                header("Location: /views/user_dashboard.php");
            } else {
                header("Location: /views/admin_dashboard.php");
            }
            exit();
        } else {
            echo "Contraseña incorrecta";
            return false;
        }
    }
    
    
    

    // Método para verificar si el usuario está logueado
    public function checkLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function logoutUser() {
        session_start();
        session_unset();
        session_destroy();
        header("Location: /views/login.php");
        exit();
    }

    // Nuevas funcionalidades de reserva y perfil

    // Obtener todas las reservas de un usuario usando su email
    public function getUserReservations($email_cliente) {
        return $this->reservationModel->getReservationsByUserEmail($email_cliente);
    }

    public function getAllReservations() {
        return $this->reservationModel->getAllReservations();  // Llamamos a un método nuevo en ReservationModel
    }

    // Crear una nueva reserva para un usuario
    public function createReservation($userId, $type, $date, $time, $flightNumber, $airport, $hotel, $travellers) {
        $reservationData = [
            'user_id' => $userId,
            'type' => $type,
            'date' => $date,
            'time' => $time,
            'flight_number' => $flightNumber,
            'airport' => $airport,
            'hotel' => $hotel,
            'travellers' => $travellers,
        ];

        // Llamar al modelo de reservas para insertar la nueva reserva
        $reservationId = $this->reservationModel->createReservation($reservationData);

        // Generar un localizador único y enviar correo al usuario
        if ($reservationId) {
            $locator = 'LOC-' . str_pad($reservationId, 6, '0', STR_PAD_LEFT);  // Formato de localizador
            $this->reservationModel->updateReservationLocator($reservationId, $locator); // Actualizar el localizador en la base de datos

            // Enviar correo con la confirmación de la reserva y localizador
            $user = $this->userModel->getUserById($userId);
            mail($user['email'], 'Confirmación de Reserva', "Tu reserva ha sido realizada. Localizador: $locator");

            return $locator;
        }

        return false;
    }

    // Obtener los detalles de una reserva específica
    public function getReservationDetails($reservationId) {
        return $this->reservationModel->getReservationById($reservationId);
    }

    // Actualizar el perfil de usuario
    public function updateUserProfile($userId, $name, $email, $password) {
        // Si se actualiza la contraseña, la ciframos
        if (!empty($password)) {
            $password = password_hash($password, PASSWORD_BCRYPT);
        }

        // Actualizar los datos en la base de datos
        $updateData = [
            'name' => $name,
            'email' => $email,
            'password' => $password
        ];

        return $this->userModel->updateUser($userId, $updateData);
    }

    // Obtener datos del usuario logueado
    function getLoggedUserData() {
        if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'user') {
            global $userModel;
            $userData = $userModel->getUserById($_SESSION['user_id']);
            return $userData;
        }
        return null;
    }


    // Método para registrar un hotel
    public function registrarHotel($id_zona, $comision, $usuario, $password) {
        $registerSuccess = $this->userModel->registrarHotelEnBaseDeDatos($id_zona, $comision, $usuario, $password);

        if ($registerSuccess) {
            // Redirige a la página de login
            header("Location: /views/login.php?success=1");
            exit();
        } else {
            echo "Error al registrar el hotel. Intenta nuevamente.";
        }
    }


    
}
?>