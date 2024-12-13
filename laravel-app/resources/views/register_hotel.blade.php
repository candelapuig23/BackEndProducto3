<form action="{{ route('register.hotel') }}" method="POST">
    @csrf
    <input type="number" name="id_zona" placeholder="Zona" required>
    <input type="number" name="comision" placeholder="Comisión" required>
    <input type="email" name="usuario" placeholder="Usuario (Email)" required>
    <input type="password" name="password" placeholder="Contraseña" required>
    <input type="password" name="password_confirmation" placeholder="Confirmar Contraseña" required>
    <button type="submit">Registrar</button>
</form>
