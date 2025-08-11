<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
date_default_timezone_set('America/Monterrey');
include 'db.php';

// Validar sesión
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

$id_cita = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id_cita <= 0) {
    echo "ID de cita inválido.";
    exit;
}

// Obtener datos de la cita
$stmt = $conn->prepare("SELECT * FROM citas WHERE id_cita = ?");
$stmt->bind_param("i", $id_cita);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Cita no encontrada.";
    exit;
}

$cita = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Confirmación de cita</title>
  <style>
    body {
      background: #fff4f7;
      font-family: sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }
    .confirmacion {
      background: white;
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(166,79,99,0.15);
      max-width: 400px;
      text-align: center;
    }
    h2 {
      color: #a64f63;
      margin-bottom: 1rem;
    }
    p {
      color: #4a2c2a;
      margin-bottom: 0.5rem;
    }
    a {
      color: #a64f63;
      text-decoration: none;
      display: inline-block;
      margin-top: 1rem;
      font-weight: bold;
    }
    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="confirmacion">
    <h2>¡Cita agendada con éxito!</h2>
    <p><strong>ID de tu cita:</strong> <?= htmlspecialchars($cita['id_cita']) ?></p>
    <p><strong>Nombre:</strong> <?= htmlspecialchars($cita['nombre']) ?></p>
    <p><strong>Fecha:</strong> <?= htmlspecialchars($cita['fecha_cita']) ?></p>
    <p><strong>Hora:</strong> <?= htmlspecialchars($cita['hora_cita']) ?></p>
    <a href="panel.php">Volver al panel</a>
  </div>
</body>
</html>