<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="container">
    <h2>Iniciar Sesión</h2>
    <form action="/../login.php" method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <select name="role" required>
            <option value="user">Particular</option>
            <option value="admin">Admin</option>
        </select>
        <button type="submit">Iniciar Sesión</button>
        <div class="note">
            <p>¿No tienes cuenta? <a href="/views/register.php">Regístrate aquí</a></p>
        </div>
    </form>
</div>
</body>
</html>
