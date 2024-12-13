<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Hotel</title>
    <link rel="stylesheet" href="/css/style.css"> <!-- Enlace al CSS -->
</head>
<body>
    <header>
        <h1>Registro de Hotel</h1>
    </header>
    <main>
        <form method="POST" action="/../register_hotel.php">
            <div>
                <label for="id_zona">Zona:</label>
                <input type="number" id="id_zona" name="id_zona" required>
            </div>
            <div>
                <label for="comision">Comisión:</label>
                <input type="number" id="comision" name="comision" required>
            </div>
            <div>
                <label for="email">Usuario (Email):</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Registrar</button>
        </form>
    </main>
</body>
</html>