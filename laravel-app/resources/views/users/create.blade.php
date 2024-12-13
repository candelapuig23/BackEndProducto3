<!DOCTYPE html>
<html>
<head>
    <title>Create User</title>
</head>
<body>
    <h1>Create a New User</h1>

    <!-- Mostrar mensaje de éxito si existe -->
    @if (session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <!-- Formulario para crear un usuario -->
    <form method="POST" action="{{ route('users.create') }}">
        @csrf <!-- Token de seguridad de Laravel -->
        <div>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" placeholder="Name" required>
        </div>
        <div>
            <label for="apellido1">First Surname:</label>
            <input type="text" id="apellido1" name="apellido1" placeholder="First Surname" required>
        </div>
        <div>
            <label for="apellido2">Second Surname:</label>
            <input type="text" id="apellido2" name="apellido2" placeholder="Second Surname" required>
        </div>
        <div>
            <label for="direccion">Address:</label>
            <input type="text" id="direccion" name="direccion" placeholder="Address" required>
        </div>
        <div>
            <label for="codigoPostal">Postal Code:</label>
            <input type="text" id="codigoPostal" name="codigoPostal" placeholder="Postal Code" required>
        </div>
        <div>
            <label for="ciudad">City:</label>
            <input type="text" id="ciudad" name="ciudad" placeholder="City" required>
        </div>
        <div>
            <label for="pais">Country:</label>
            <input type="text" id="pais" name="pais" placeholder="Country" required>
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Email" required>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Password" required>
        </div>
        <div>
            <label for="password_confirmation">Confirm Password:</label>
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" required>
        </div>
        <div>
            <button type="submit">Create User</button>
        </div>
    </form>

    <!-- Mostrar errores de validación -->
    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</body>
</html>
