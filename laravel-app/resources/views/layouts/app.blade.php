<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    
    {{-- Incluye el CSS específico de la vista actual --}}
    @if (Request::is('admin/dashboard*'))
        <link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}">
    @elseif (Request::is('user/dashboard*'))
        <link rel="stylesheet" href="{{ asset('css/user_dashboard.css') }}">
    @elseif (Request::is('reservations/create*'))
    <link rel="stylesheet" href="{{ asset('css/make_form.css') }}">
    @elseif (Request::is('profile/edit*'))
    <link rel="stylesheet" href="{{ asset('css/edit_profile.css') }}">
    @elseif (Request::is('admin/register-hotel*'))
    <link rel="stylesheet" href="{{ asset('css/register_hotel.css') }}">
    @elseif (Request::is('/'))
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    @elseif (Request::is('login'))
        <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    @elseif (Request::is('hotel/login*'))
    <link rel="stylesheet" href="{{ asset('css/hotel_login.css') }}">
    @elseif (Request::is('hotel/dashboard*'))
    <link rel="stylesheet" href="{{ asset('css/hotel_dashboard.css') }}">
    @elseif (Request::is('reservations/*/edit*'))
    <link rel="stylesheet" href="{{ asset('css/edit_reservation.css') }}">
    @elseif (Request::is('reservations/create*') || Request::is('hotel/reservations/create*'))
    <link rel="stylesheet" href="{{ asset('css/make_form.css') }}">
@endif


    {{-- Script común para todas las vistas --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header style="text-align: center; padding: 20px; background-color: #f8f9fa; border-bottom: 1px solid #ddd;">
    @hasSection('header')
        <h1 style="font-size: 2.5em; font-weight: bold; margin: 0;">@yield('header')</h1>
    @else
        <h1 style="font-size: 2em; font-weight: bold; margin: 0;">Isla Transfers</h1>
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
