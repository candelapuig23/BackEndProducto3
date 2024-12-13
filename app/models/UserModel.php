<?php

class UserModel {
    private $pdo; 

    public function __construct() {
        $host = 'mysql'; // Nombre del servicio en docker-compose.yml
        $db   = 'database'; // Nombre de la base de datos
        $user = 'user';     // Usuario de la base de datos
        $pass = 'password'; // Contraseña

        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Configura el manejo de errores
        } catch (PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
            exit();
        }
    }

    // Método para registrar un viajero en la tabla 'transfer_viajeros'
    public function registrarViajero($nombre, $apellido1, $apellido2, $direccion, $codigoPostal, $ciudad, $pais, $email, $password) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Consulta para insertar el nuevo viajero
        $sql = "INSERT INTO transfer_viajeros (nombre, apellido1, apellido2, direccion, codigoPostal, ciudad, pais, email, password) 
                VALUES (:nombre, :apellido1, :apellido2, :direccion, :codigoPostal, :ciudad, :pais, :email, :password)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido1', $apellido1);
        $stmt->bindParam(':apellido2', $apellido2);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':codigoPostal', $codigoPostal);
        $stmt->bindParam(':ciudad', $ciudad);
        $stmt->bindParam(':pais', $pais);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $passwordHash);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            return true; // Registro exitoso
        }
        return false; // Si algo salió mal
    }

    // Método para registrar un administrador en la tabla 'transfer_viajeros_admin'
    public function registrarAdmin($nombre, $apellido1, $apellido2, $direccion, $codigoPostal, $ciudad, $pais, $email, $password) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Consulta para insertar el nuevo administrador
        $sql = "INSERT INTO transfer_viajeros_admin (nombre, apellido1, apellido2, direccion, codigoPostal, ciudad, pais, email, password) 
                VALUES (:nombre, :apellido1, :apellido2, :direccion, :codigoPostal, :ciudad, :pais, :email, :password)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido1', $apellido1);
        $stmt->bindParam(':apellido2', $apellido2);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':codigoPostal', $codigoPostal);
        $stmt->bindParam(':ciudad', $ciudad);
        $stmt->bindParam(':pais', $pais);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $passwordHash);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            return true; // Registro exitoso
        }
        return false; // Si algo salió mal
    }

    // Método para obtener un viajero por email (Particular)
    public function getUserByEmailViajero($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM transfer_viajeros WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Método para obtener un admin por email
    public function getUserByEmailAdmin($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM transfer_viajeros_admin WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserByEmailFromViajeros($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM transfer_viajeros WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserByEmailFromAdmins($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM transfer_viajeros_admin WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }    
    
    public function getUserById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM transfer_viajeros WHERE id_viajero = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        return $user ?: false;
    }    

    // Actualizar datos de un usuario
    public function updateUser($userId, $nombre, $email, $role, $password = null) {
        // Determinar la tabla y la columna de identificación según el rol
        if ($role === 'admin') {
            $tableName = 'transfer_viajeros_admin';
            $identifierColumn = 'id_viajero_admin';
        } else {
            $tableName = 'transfer_viajeros';
            $identifierColumn = 'id_viajero';
        }
    
        // Construir la consulta
        $query = "UPDATE " . $tableName . " SET nombre = :nombre, email = :email";
        if ($password) {
            $query .= ", password = :password";
        }
        $query .= " WHERE " . $identifierColumn . " = :user_id";
    
        // Preparar la consulta
        $stmt = $this->pdo->prepare($query);
    
        // Vincular los parámetros
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':email', $email);
        if ($password) {
            $stmt->bindParam(':password', $password);
        }
        $stmt->bindParam(':user_id', $userId);
    
        // Ejecutar la consulta
        return $stmt->execute();
    }    

    // Método para obtener la conexión PDO
    public function getPDO() {
        return $this->pdo;
    }


    public function registrarHotelEnBaseDeDatos($id_zona, $comision, $usuario, $password) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
        $sql = "INSERT INTO tranfer_hotel (id_zona, Comision, usuario, password) 
                VALUES (:id_zona, :comision, :usuario, :password)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id_zona', $id_zona);
        $stmt->bindParam(':comision', $comision);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->bindParam(':password', $passwordHash);
    
        return $stmt->execute(); // Retorna true si la inserción fue exitosa
    }

}
?>