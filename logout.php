<?php
session_start();

// 🔥 Eliminar todas las variables de sesión
$_SESSION = array();

// 🧹 Eliminar la cookie de sesión (si existe)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 💣 Destruir la sesión
session_destroy();

// 🚫 Headers anti-caché reforzados
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// 🔁 Redirigir al login
header("Location: login.php?cerrado=true");
exit();
?>