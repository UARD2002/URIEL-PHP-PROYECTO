
<?php
include 'db.php';

$nombre = $_POST['nombre'];
$correo = $_POST['correo'];
$telefono = $_POST['telefono'];
$id_servicio = $_POST['id_servicio'];
$fecha_cita = $_POST['fecha_cita'];
$hora_cita = $_POST['hora_cita'];

$stmt = $conn->prepare("SELECT id_cliente FROM clientes WHERE correo = ?");
$stmt->bind_param("s", $correo);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
  $stmt->bind_result($id_cliente);
  $stmt->fetch();
} else {
  $stmt = $conn->prepare("INSERT INTO clientes (nombre, correo, telefono) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $nombre, $correo, $telefono);
  $stmt->execute();
  $id_cliente = $stmt->insert_id;
}

$stmt = $conn->prepare("INSERT INTO citas (id_cliente, id_servicio, fecha_cita, hora_cita) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiss", $id_cliente, $id_servicio, $fecha_cita, $hora_cita);

if ($stmt->execute()) {
  echo "<h2>✅ ¡Cita agendada con éxito! Nos vemos pronto.</h2>";
} else {
  echo "<h2>⚠️ Ya tienes una cita en ese horario. Intenta con otro.</h2>";
}
?>
