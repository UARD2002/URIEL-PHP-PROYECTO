<?php
ob_start();
session_start();
date_default_timezone_set('America/Monterrey');

// Bloqueo completo de caché del navegador
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Verificar sesión activa
if (!isset($_SESSION['id_usuario'])) {
    echo "<!DOCTYPE html>
    <html lang='es'>
    <head><meta charset='UTF-8'><script>
      alert('Tu sesión ha expirado o fue cerrada. Volviendo al login.');
      window.location.href = 'login.php';
    </script></head>
    <body></body></html>";
    exit;
}

include 'db.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Daylight Studio | Agenda tu cita</title>
  <style>
    /* Tu CSS original (lo mantuve igual) */
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #fff4f7; color: #4a2c2a; line-height: 1.6; min-height: 100vh; display: flex; flex-direction: column; }
    header { background-color: #ffe0eb; padding: 1rem 2rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05); text-align: center; color: #a64f63; font-size: 2rem; font-weight: bold; }
    main { flex: 1; max-width: 480px; margin: 2rem auto; background-color: #fff; padding: 2rem 2.5rem; border-radius: 12px; box-shadow: 0 4px 20px rgba(166,79,99,0.15); }
    h2 { color: #a64f63; margin-bottom: 1rem; border-bottom: 2px solid #dba0ab; padding-bottom: 0.3rem; font-size: 1.7rem; text-align: center; }
    form { display: flex; flex-direction: column; gap: 1rem; }
    input[type="text"], input[type="email"], input[type="date"], select { padding: 0.8rem 1rem; font-size: 1rem; border: 2px solid #dba0ab; border-radius: 25px; transition: border-color 0.3s; color: #4a2c2a; }
    input[type="text"]:focus, input[type="email"]:focus, input[type="date"]:focus, select:focus { border-color: #a64f63; outline: none; }
    button { background-color: #a64f63; color: white; border: none; border-radius: 25px; padding: 0.75rem; font-size: 1.1rem; cursor: pointer; transition: background-color 0.3s; font-weight: bold; }
    button:hover { background-color: #812d3c; }
    footer { text-align: center; padding: 1rem 0; background-color: #ffe0eb; color: #7b404d; font-size: 0.9rem; box-shadow: 0 -2px 10px rgba(0,0,0,0.05); margin-top: auto; }
    .error { color: #a64f63; text-align:center; padding: .5rem 0; }
  </style>
</head>
<body>
  <header style="display: flex; justify-content: space-between; align-items: center; background-color: #ffe0eb; padding: 1rem 2rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
    <span style="font-size: 2rem; font-weight: bold; color: #a64f63;">Daylight Studio</span>
    <a href="logout.php" style="color: #a64f63; font-weight: bold; text-decoration: none;">Cerrar sesión</a>
  </header>

  <main>
    <h2>Agenda tu cita</h2>

    <?php
      // Mensajes de error o éxito vía query string (procesar_cita.php redirige con error)
      if (isset($_GET['error'])) {
          $msg = htmlspecialchars($_GET['error']);
          echo "<div class='error'>{$msg}</div>";
      }
      if (isset($_GET['ok'])) {
          echo "<div class='error' style='color:green;'>Cita agendada correctamente</div>";
      }
    ?>

    <form method="POST" action="procesar_cita.php" id="formCita">
      <input type="text" name="nombre" placeholder="Tu nombre completo" required value="<?= isset($_GET['nombre']) ? htmlspecialchars($_GET['nombre']) : '' ?>" />
      <input type="email" name="correo" placeholder="Correo electrónico" required value="<?= isset($_GET['correo']) ? htmlspecialchars($_GET['correo']) : '' ?>" />
      <input type="text" name="telefono" placeholder="Número de teléfono" required value="<?= isset($_GET['telefono']) ? htmlspecialchars($_GET['telefono']) : '' ?>" />
      
      <select name="id_servicio" required>
        <option value="">Selecciona un servicio</option>
        <?php
          $servicios = $conn->query("SELECT id_servicio, nombre_servicio FROM servicios");
          while ($row = $servicios->fetch_assoc()) {
            $sel = (isset($_GET['servicio']) && $_GET['servicio'] == $row['id_servicio']) ? 'selected' : '';
            echo "<option value='{$row['id_servicio']}' $sel>{$row['nombre_servicio']}</option>";
          }
        ?>
      </select>

      <!-- fecha -->
      <input type="date" name="fecha_cita" id="fecha_cita" required min="<?php echo date('Y-m-d'); ?>" value="<?= isset($_GET['fecha']) ? htmlspecialchars($_GET['fecha']) : '' ?>" />

      <!-- HORA: select dinámico (mantiene el estilo por ser <select>) -->
      <select name="hora_cita" id="hora_cita" required>
        <option value="">Selecciona una hora</option>
      </select>

      <button type="submit">Agendar</button>
    </form>
  </main>

  <footer>&copy; 2025 Daylight Studio · Todos los derechos reservados</footer>

<script>
// helper: convierte "10:30:00" -> "10:30 a.m."
function formatTimeDisplay(hms) {
    if (!hms) return hms;
    let parts = hms.split(':');
    let h = parseInt(parts[0],10);
    let m = parts[1];
    let suffix = h >= 12 ? 'p.m.' : 'a.m.';
    let hour12 = h % 12;
    if (hour12 === 0) hour12 = 12;
    return hour12 + ':' + m + ' ' + suffix;
}

function cargarHorariosPara(fecha) {
    let selectHora = document.getElementById('hora_cita');
    selectHora.innerHTML = '<option value="">Cargando...</option>';
    fetch('horarios_disponibles.php?fecha=' + encodeURIComponent(fecha))
    .then(res => res.json())
    .then(data => {
        // Si el backend manda redirect, hacemos redirección
        if (data && data.redirect) {
            window.location.href = data.redirect;
            return;
        }
        selectHora.innerHTML = '<option value="">Selecciona una hora</option>';
        if (!Array.isArray(data) || data.length === 0) {
            // No hay horarios disponibles
            let opt = document.createElement('option');
            opt.value = '';
            opt.textContent = 'No hay horarios disponibles';
            selectHora.appendChild(opt);
            return;
        }
        data.forEach(function(hora) {
            let opt = document.createElement('option');
            opt.value = hora; // formato 'HH:MM:SS'
            opt.textContent = formatTimeDisplay(hora); // muestra '10:30 a.m.'
            selectHora.appendChild(opt);
        });
    })
    .catch(err => {
        console.error(err);
        selectHora.innerHTML = '<option value="">Error al cargar horarios</option>';
    });
}

// Evento: cuando cambie la fecha
document.getElementById('fecha_cita').addEventListener('change', function() {
    let fecha = this.value;
    if (fecha) cargarHorariosPara(fecha);
});

// Si la fecha ya viene en el input (por repoblado), cargar horarios al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    const fechaVal = document.getElementById('fecha_cita').value;
    if (fechaVal) cargarHorariosPara(fechaVal);
});
</script>

</body>
</html>