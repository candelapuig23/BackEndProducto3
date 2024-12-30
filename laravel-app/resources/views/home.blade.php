<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Isla Transfers - Página de Inicio</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">

</head>
<body>
<div class="container">
    <h2>¡Bienvenido a Isla Transfers!</h2>
    <p>Por favor, selecciona una opción:</p>
    <div class="buttons">
        <a href="{{ route('login') }}">Iniciar Sesión</a>
        <a href="{{ route('register.form') }}">Registrarse</a>
        <a href="{{ route('hotel.login') }}">Iniciar sesión como hotel</a>
    </div>
</div>
</body>
</html>
