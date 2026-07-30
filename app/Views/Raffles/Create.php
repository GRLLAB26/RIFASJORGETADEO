<h2>Nueva Rifa</h2>

<form method="POST" action="/raffles/store">

    <label>Título</label><br>
    <input type="text" name="title" required><br><br>

    <label>Descripción</label><br>
    <textarea name="description"></textarea><br><br>

    <label>Precio por boleto</label><br>
    <input type="number" step="0.01" name="ticket_price" required><br><br>

    <label>Cantidad de números</label><br>
    <input type="number" name="total_numbers" value="100"><br><br>

    <label>Fecha del sorteo</label><br>
    <input type="datetime-local" name="draw_date"><br><br>

    <label>WhatsApp</label><br>
    <input type="text" name="whatsapp"><br><br>

    <label>CLABE</label><br>
    <input type="text" name="clabe"><br><br>

    <button type="submit">
        Crear Rifa
    </button>

</form>