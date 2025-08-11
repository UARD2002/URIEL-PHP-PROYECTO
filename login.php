<?php
// Esto debe ser lo PRIMERO en el archivo (sin espacios ni saltos antes)
ob_start();
session_start();
if (isset($_SESSION['usuario'])) {
    // Redirige según el rol
    if ($_SESSION['rol'] === 'admin') {
        header("Location: citas/listar.php");
    } else {
        header("Location: panel.php");
    }
    exit();
}
date_default_timezone_set('America/Monterrey');

include 'db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';

    // Validación básica
    if (empty($usuario) || empty($contrasena)) {
        $error = "Usuario y contraseña son obligatorios";
    } else {
        // Consulta preparada segura
        $stmt = $conn->prepare("SELECT id_usuario, usuario, contrasena, rol FROM usuarios WHERE usuario = ? AND activo = 1 LIMIT 1");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

 if ($resultado->num_rows === 1) {
    $usuarioData = $resultado->fetch_assoc();

    if (password_verify($contrasena, $usuarioData['contrasena'])) {
        $_SESSION = [
            'id_usuario' => $usuarioData['id_usuario'],
            'usuario' => $usuarioData['usuario'],
            'rol' => $usuarioData['rol'],
            'ultimo_acceso' => time()
        ];

        // Redirección según rol
        if ($_SESSION['rol'] === 'admin') {
            header("Location: citas/listar.php");
        } else {
            header("Location: panel.php");
        }
        exit;
    } else {
        $error = "Contraseña incorrecta";
    }

} else {
    $error = "Usuario no encontrado o inactivo";
}

    }
}
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login | Daylight Studio</title>
    <style>
        body {
            background: #fff4f7;
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(166, 79, 99, 0.15);
            width: 100%;
            max-width: 400px;
        }
        .form-input {
            width: 100%;
            padding: 12px;
            margin-bottom: 1rem;
            border: 2px solid #dba0ab;
            border-radius: 25px;
            font-size: 1rem;
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            background-color: #a64f63;
            color: white;
            border: none;
            border-radius: 25px;
            font-size: 1rem;
            cursor: pointer;
        }
        .error-message {
            color: #e74c3c;
            text-align: center;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 style="text-align: center; color: #a64f63; margin-bottom: 1.5rem;">Iniciar sesión</h2>
        
        <?php if (!empty($error)): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <input type="text" name="usuario" placeholder="Usuario" required 
                   class="form-input" value="<?= htmlspecialchars($usuario ?? '') ?>">
            
            <input type="password" name="contrasena" placeholder="Contraseña" required 
                   class="form-input">
            
            <button type="submit" class="btn-login">Entrar</button>
        </form>

        <div style="text-align: center; margin-top: 1rem;">
            <a href="registro.php" style="color: #a64f63; text-decoration: none; font-weight: bold;">
                Crear cuenta nueva
            </a>
        </div>
</body>
</html>