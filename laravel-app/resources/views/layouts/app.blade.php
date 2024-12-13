<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('resources/css/admin_dashboard.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header>
        @hasSection('header')
            <h1>@yield('header')</h1>
        @else
            <h1>Encabezado por defecto</h1>
        @endif
    </header>

    <main>
        @yield('content')
    </main>
    <footer>
        <p>&copy; {{ date('Y') }} Tu Empresa. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
