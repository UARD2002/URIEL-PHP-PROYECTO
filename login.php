
<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Admin | Daylight Studio</title>
  <link rel="stylesheet" href="../assets/estilos.css">
</head>
<body>
  <main>
    <h2>Acceso administrativo</h2>
    <form method="POST" action="validar.php">
      <input type="text" name="usuario" placeholder="Usuario" required>
      <input type="password" name="password" placeholder="Contraseña" required>
      <button type="submit">Entrar</button>
    </form>
  </main>
</body>
</html>
