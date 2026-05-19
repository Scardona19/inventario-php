<?php
// ============================================================
//  Configuración de conexión a la Base de Datos
// ============================================================
define('DB_HOST',   'localhost');
define('DB_USER',   'root');       // Cambia por tu usuario MySQL
define('DB_PASS',   '');           // Cambia por tu contraseña MySQL
define('DB_NAME',   'inventario_db');
define('DB_CHARSET','utf8mb4');

function conectar(): mysqli {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die('<div class="alert alert-danger"><strong>Error de conexión:</strong> '
            . htmlspecialchars($conn->connect_error) . '</div>');
    }
    $conn->set_charset(DB_CHARSET);
    return $conn;
}
