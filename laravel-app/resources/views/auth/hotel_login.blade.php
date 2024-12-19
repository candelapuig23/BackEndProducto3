<h2>Iniciar sesión - Hotel</h2>
<form method="POST" action="{{ route('hotel.login.post') }}">
    @csrf
    <label for="usuario">Email del Hotel:</label>
    <input type="email" name="usuario" required>
    <label for="password">Contraseña:</label>
    <input type="password" name="password" required>
    <button type="submit">Iniciar sesión</button>
</form>
