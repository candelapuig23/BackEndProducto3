<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
</head>
<body>
    <h1>Iniciar Sesión</h1>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <label for="role">Tipo de Usuario:</label>
        <select name="role" id="role" required>
            <option value="user">Particular</option>
            <option value="admin">Administrador</option>
        </select>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Iniciar Sesión</button>
    </form>
</body>
</html>
