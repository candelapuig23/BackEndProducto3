<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="{{ asset('resources/css/style.css') }}">
</head>
<body>
<div class="container">
    <h2>Registro de Usuario</h2>
    <form action="{{ route('register') }}" method="POST">
        @csrf
        <!-- Campo para seleccionar el tipo de usuario -->
        <label for="role">Escoge el tipo de Usuario:</label>
        <select name="role" id="role" required>
            <option value="user">Particular</option>
            <option value="admin">Administrador</option>
        </select>

        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="text" name="apellido1" placeholder="Primer Apellido" required>
        <input type="text" name="apellido2" placeholder="Segundo Apellido" required>
        <input type="text" name="direccion" placeholder="Dirección" required>
        <input type="text" name="codigoPostal" placeholder="Código Postal" required>
        <input type="text" name="ciudad" placeholder="Ciudad" required>
        <input type="text" name="pais" placeholder="País" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <input type="password" name="password_confirmation" placeholder="Confirmar Contraseña" required>

        <button type="submit">Registrarse</button>
        <div class="note">
            <p>¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión aquí</a></p>
        </div>
    </form>
</div>
</body>
</html>
