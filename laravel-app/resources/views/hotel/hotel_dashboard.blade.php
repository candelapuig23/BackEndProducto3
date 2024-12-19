<div class="container">
    <h2>Bienvenido, {{ $hotel->usuario }}</h2>
    <p>Este es tu panel de control.</p>

    <h3>Tus Reservas</h3>
    @if ($reservas->isEmpty())
        <p>No tienes reservas asignadas.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>ID Reserva</th>
                    <th>Fecha de Entrada</th>
                    <th>Número de Viajeros</th>
                    <th>Comisión</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reservas as $reserva)
                    <tr>
                        <td>{{ $reserva->id_reserva }}</td>
                        <td>{{ $reserva->fecha_entrada }}</td>
                        <td>{{ $reserva->num_viajeros }}</td>
                        <td>{{ $hotel->comision }} %</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Botón para crear una nueva reserva -->
<a href="{{ route('hotel.reservations.create') }}" class="btn btn-primary">Crear Nueva Reserva</a>

</div>
