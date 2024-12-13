<?php

// ReservationModel.php
class ReservationModel {
    private $conn;
    private $tableName = "transfer_reservas";

    // Constructor que recibe la conexión a la base de datos
    public function __construct($db) {
        $this->conn = $db; // Almacena la conexión en la propiedad $db
    }

    // Método para obtener las zona disponibles
    public function getZonas() {
        $query = "SELECT id_zona, descripcion FROM transfer_zona"; // Asegúrate de usar el nombre correcto de la columna
        $stmt = $this->conn->prepare($query);  // Usa la conexión para preparar la consulta
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);  // Devuelve el resultado de la consulta
    }

    // Método para obtener los vehículos disponibles
    public function getVehiculos() {
        $query = "SELECT id_vehiculo, Descripción FROM transfer_vehiculo"; // Asegúrate de usar el nombre correcto de la columna
        $stmt = $this->conn->prepare($query);  // Usa la conexión para preparar la consulta
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);  // Devuelve el resultado de la consulta
    }

    // Método para obtener los tipos de trayecto
    public function getTiposTrayecto() {
        $query = "SELECT id_tipo_reserva, Descripción FROM transfer_tipo_reserva";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para obtener los hoteles
    public function getHoteles() {
        $query = "SELECT id_hotel, usuario FROM tranfer_hotel";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método actualizado para obtener todas las reservas de un usuario por su email
    public function getReservationsByUserEmail($email_cliente) {
        $stmt = $this->conn->prepare("SELECT * FROM transfer_reservas WHERE email_cliente = :email_cliente");
        $stmt->bindParam(':email_cliente', $email_cliente);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllReservations() {
        // Consulta mejorada para obtener todas las reservas con más detalles
        $query = "
            SELECT 
                r.id_reserva AS id,
                r.fecha_reserva AS date,
                r.localizador AS locator,
                r.email_cliente AS email,
                r.num_viajeros AS num_viajeros,
                r.fecha_entrada AS fecha_entrada,
                tt.Descripción AS tipo_trayecto,
                v.Descripción AS vehiculo,
                h.usuario AS hotel
            FROM transfer_reservas r
            LEFT JOIN transfer_tipo_reserva tt ON r.id_tipo_reserva = tt.id_tipo_reserva
            LEFT JOIN transfer_vehiculo v ON r.id_vehiculo = v.id_vehiculo
            LEFT JOIN tranfer_hotel h ON r.id_hotel = h.id_hotel
        ";
    
        // Preparar y ejecutar la consulta
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        // Retornar todas las reservas con detalles
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getReservationById($id) {
        $query = "SELECT * FROM transfer_reservas WHERE id_reserva = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    

    // Método para obtener detalles de las reservas
    public function getReservationsWithDetails($email_cliente) {
        $sql = "
            SELECT 
                r.id_reserva,
                r.fecha_reserva,
                r.localizador,
                tt.Descripción AS tipo_trayecto,
                v.Descripción AS vehiculo
            FROM transfer_reservas r
            LEFT JOIN transfer_tipo_reserva tt ON r.id_tipo_reserva = tt.id_tipo_reserva
            LEFT JOIN transfer_vehiculo v ON r.id_vehiculo = v.id_vehiculo
            WHERE r.email_cliente = :email_cliente
        ";
    
        // Preparamos la consulta
        $stmt = $this->conn->prepare($sql);
    
        // Depuración: Verifica el valor que se pasa a bindParam
        echo "Email cliente: " . $email_cliente; // Asegúrate de que esté recibiendo el valor correcto
    
        // Asegurémonos de que el parámetro es el correcto
        $stmt->bindParam(':email_cliente', $email_cliente, PDO::PARAM_STR);
    
        // Ejecutamos la consulta
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Si falla la ejecución, obtenemos el error
            print_r($stmt->errorInfo());  // Esto te ayudará a ver si hay algún problema con la ejecución
            return false;
        }
    }    

    // Método para obtener el id_tipo_reserva basado en la descripción
    public function getTipoReservaId($descripcion) {
        $query = "SELECT id_tipo_reserva FROM transfer_tipo_reserva WHERE Descripción = :descripcion";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? $result['id_tipo_reserva'] : null;
    }

    // Método para obtener detalles completos de la reserva por ID
    public function getReservationDetailsById($id) {
        $query = "
            SELECT 
                r.id_reserva AS id,
                r.localizador,
                r.id_hotel,
                h.usuario AS hotel,
                r.id_tipo_reserva,
                tt.Descripción AS tipo_trayecto,
                r.email_cliente,
                r.fecha_reserva,
                r.fecha_modificacion,
                r.id_destino,
                z.id_zona,
                z.descripcion AS destino,
                r.fecha_entrada,
                r.hora_entrada,
                r.numero_vuelo_entrada,
                r.origen_vuelo_entrada,
                r.hora_vuelo_salida,
                r.fecha_vuelo_salida,
                r.num_viajeros,
                r.id_vehiculo,
                v.Descripción AS vehiculo
            FROM transfer_reservas r
            LEFT JOIN transfer_tipo_reserva tt ON r.id_tipo_reserva = tt.id_tipo_reserva
            LEFT JOIN transfer_vehiculo v ON r.id_vehiculo = v.id_vehiculo
            LEFT JOIN tranfer_hotel h ON r.id_hotel = h.id_hotel
            LEFT JOIN transfer_zona z ON r.id_destino = z.id_zona
            WHERE r.id_reserva = :id
        ";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    

    // Método privado para generar un localizador único
    private function generarLocalizador() {
        return 'LOC-' . strtoupper(uniqid());
    }

    
    // Insertar una nueva reserva
    public function insertReserva($data) {
        // Obtener el id_tipo_reserva basado en la descripción
        $tipoReservaId = $this->getTipoReservaId($data['trayecto']);
        
        if ($tipoReservaId === null) {
            return false;
        }
    
        // Generar el localizador
        $localizador = $this->generarLocalizador();
        
        // Asignar los valores de forma separada
        $fechaEntrada = !empty($data['diaLlegada']) ? $data['diaLlegada'] : null;   
        $fechaVueloSalida = !empty($data['diaVuelo']) ? $data['diaVuelo'] : null;
        $horaEntrada = !empty($data['horaLlegada']) ? $data['horaLlegada'] : null;
    
        // Actualizar la consulta SQL, eliminando el campo 'rol_usuario'
        $query = "INSERT INTO " . $this->tableName . " (id_tipo_reserva, localizador, id_vehiculo, id_hotel, id_destino, email_cliente, num_viajeros, fecha_reserva, fecha_entrada, fecha_modificacion, fecha_vuelo_salida, hora_entrada, numero_vuelo_entrada, origen_vuelo_entrada)
                  VALUES (:id_tipo_reserva, :localizador, :id_vehiculo, :id_hotel, :id_destino, :email_cliente, :num_viajeros, NOW(), :fecha_entrada, :fecha_modificacion, :fecha_vuelo_salida, :hora_entrada, :numero_vuelo_entrada, :origen_vuelo_entrada)";
    
        $stmt = $this->conn->prepare($query);
    
        // Vincular los parámetros (sin 'rol_usuario')
        $stmt->bindParam(':id_tipo_reserva', $tipoReservaId);
        $stmt->bindParam(':localizador', $localizador);
        $stmt->bindParam(':fecha_modificacion', $data['fecha_modificacion']);
        $stmt->bindParam(':id_destino', $data['idZona']);
        $stmt->bindParam(':id_vehiculo', $data['idVehiculo']);
        $stmt->bindParam(':id_hotel', $data['hotelDestino']);
        $stmt->bindParam(':email_cliente', $data['email']);
        $stmt->bindParam(':num_viajeros', $data['numViajeros']);
        $stmt->bindParam(':fecha_entrada', $fechaEntrada, PDO::PARAM_STR);
        $stmt->bindParam(':hora_entrada', $horaEntrada, PDO::PARAM_STR);
        $stmt->bindParam(':fecha_vuelo_salida', $fechaVueloSalida, PDO::PARAM_STR);
        $stmt->bindParam(':numero_vuelo_entrada', $data['numeroVuelo']); 
        $stmt->bindParam(':origen_vuelo_entrada', $data['aeropuertOrigen']); 
     
        return $stmt->execute();
    }      
    
    // Comprobar si el email ya está registrado en la base de datos
    public function checkEmail($email) {
        $query = "SELECT * FROM usuarios WHERE email = :email"; // Cambia esto según tu base de datos
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // Método para actualizar una reserva
    public function updateReservation($id, $data) {
        $query = "
            UPDATE transfer_reservas 
            SET 
                id_tipo_reserva = :id_tipo_reserva,
                id_vehiculo = :id_vehiculo,
                id_hotel = :id_hotel,
                id_destino = :id_destino,
                email_cliente = :email_cliente,
                num_viajeros = :num_viajeros,
                fecha_entrada = :fecha_entrada,
                fecha_vuelo_salida = :fecha_vuelo_salida,
                numero_vuelo_entrada = :numero_vuelo_entrada,
                origen_vuelo_entrada = :origen_vuelo_entrada,
                fecha_modificacion = NOW()
            WHERE id_reserva = :id
        ";
    
        $stmt = $this->conn->prepare($query);
    
        // Verificar valores opcionales para evitar que se inserten como cadenas vacías
        $fecha_vuelo_salida = !empty($data['fecha_vuelo_salida']) ? $data['fecha_vuelo_salida'] : null;
        $numero_vuelo_entrada = !empty($data['numero_vuelo_entrada']) ? $data['numero_vuelo_entrada'] : null;
        $origen_vuelo_entrada = !empty($data['origen_vuelo_entrada']) ? $data['origen_vuelo_entrada'] : null;
    
        // Vincular los parámetros
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':id_tipo_reserva', $data['id_tipo_reserva']);
        $stmt->bindParam(':id_vehiculo', $data['id_vehiculo']);
        $stmt->bindParam(':id_hotel', $data['id_hotel']);
        $stmt->bindParam(':id_destino', $data['id_zona']);
        $stmt->bindParam(':email_cliente', $data['email_cliente']);
        $stmt->bindParam(':num_viajeros', $data['num_viajeros']);
        $stmt->bindParam(':fecha_entrada', $data['fecha_entrada']);
        $stmt->bindParam(':fecha_vuelo_salida', $fecha_vuelo_salida, PDO::PARAM_NULL); // Permitir valores nulos
        $stmt->bindParam(':numero_vuelo_entrada', $numero_vuelo_entrada, PDO::PARAM_STR);
        $stmt->bindParam(':origen_vuelo_entrada', $origen_vuelo_entrada, PDO::PARAM_STR);
    
        return $stmt->execute();
    }

    // Método para cancelar una reserva
    public function cancelReservation($id) {
        $query = "DELETE FROM transfer_reservas WHERE id_reserva = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

}

?>
