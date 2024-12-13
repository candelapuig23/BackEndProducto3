<?php
// Database.php
class Database {
    private $pdo;

    public function __construct() {
        $host = 'mysql';  // Nombre del servicio en docker-compose.yml
        $db   = 'database'; // Nombre de la base de datos
        $user = 'user';     // Usuario de la base de datos
        $pass = 'password'; // Contraseña

        try {
            // Realiza la conexión con la base de datos
            $this->pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Configura el manejo de errores

            // Establecer la codificación UTF-8 para la conexión
            $this->pdo->exec("SET NAMES 'utf8'");  // Agrega esta línea
        } catch (PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
            exit();
        }
    }

    // Método para obtener la conexión
    public function getConnection() {
        return $this->pdo;
    }
}



?>