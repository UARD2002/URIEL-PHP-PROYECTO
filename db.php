<?php
// archivo: db.php

$host = "sql310.infinityfree.com"; // En InfinityFree, normalmente es "sqlXXX.epizy.com"
$usuario = "if0_38917665"; // Cambia esto por tu usuario de base de datos (ej: epiz_12345678)
$contrasena = "Uriel032301"; // Tu contraseña de base de datos
$basededatos = "if0_38917665_dt_proyectochidito"; // Nombre de la base de datos (ej: epiz_12345678_daylight)

$conn = new mysqli($host, $usuario, $contrasena, $basededatos);

// Verificar conexión
if ($conn->connect_error) {
    die("Error al conectar con la base de datos:" . $conn->connect_error);
}
?>