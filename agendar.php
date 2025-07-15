<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Daylight Studio | Agenda tu cita</title>
  <style>
    /* Reset */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #fff4f7;
      color: #4a2c2a;
      line-height: 1.6;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    header {
      background-color: #ffe0eb;
      padding: 1rem 2rem;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      text-align: center;
      color: #a64f63;
      font-size: 2rem;
      font-weight: bold;
    }
    main {
      flex: 1;
      max-width: 480px;
      margin: 2rem auto;
      background-color: #fff;
      padding: 2rem 2.5rem;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(166,79,99,0.15);
    }
    h2 {
      color: #a64f63;
      margin-bottom: 1rem;
      border-bottom: 2px solid #dba0ab;
      padding-bottom: 0.3rem;
      font-size: 1.7rem;
      text-align: center;
    }
    form {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }
    input[type="text"],
    input[type="email"],
    input[type="date"],
    input[type="time"],
    select {
      padding: 0.8rem 1rem;
      font-size: 1rem;
      border: 2px solid #dba0ab;
      border-radius: 25px;
      transition: border-color 0.3s;
      color: #4a2c2a;
    }
    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="date"]:focus,
    input[type="time"]:focus,
    select:focus {
      border-color: #a64f63;
      outline: none;
    }
    button {
      background-color: #a64f63;
      color: white;
      border: none;
      border-radius: 25px;
      padding: 0.75rem;
      font-size: 1.1rem;
      cursor: pointer;
      transition: background-color 0.3s;
      font-weight: bold;
    }
    button:hover {
      background-color: #812d3c;
    }
    footer {
      text-align: center;
      padding: 1rem 0;
      background-color: #ffe0eb;
      color: #7b404d;
      font-size: 0.9rem;
      box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
      margin-top: auto;
    }
  </style>
</head>
<body>
  <header>Daylight Studio</header>
  <main>
    <h2>Agenda tu cita</h2>
    <form method="POST" action="procesar_cita.php">
      <input type="text" name="nombre" placeholder="Tu nombre completo" required />
      <input type="email" name="correo" placeholder="Correo electrónico" required />
      <input type="text" name="telefono" placeholder="Número de teléfono" required />
      
      <select name="id_servicio" required>
        <option value="">Selecciona un servicio</option>
        <?php
          $servicios = $conn->query("SELECT id_servicio, nombre_servicio FROM servicios");
          while ($row = $servicios->fetch_assoc()) {
            $selected = (isset($_GET['servicio']) && $_GET['servicio'] == $row['id_servicio']) ? 'selected' : '';
            echo "<option value='{$row['id_servicio']}' $selected>{$row['nombre_servicio']}</option>";
          }
        ?>
      </select>

      <input type="date" name="fecha_cita" required min="<?php echo date('Y-m-d'); ?>" />
      <input type="time" name="hora_cita" required />

      <button type="submit">Agendar</button>
    </form>
  </main>
  <footer>&copy; 2025 Daylight Studio · Todos los derechos reservados</footer>
</body>
</html>
