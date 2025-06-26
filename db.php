<?php
// db.php

// Definiciones de las constantes de conexión a la base de datos
// Estas líneas DEBEN estar SOLO aquí para evitar los warnings "already defined".
define('DB_HOST', 'localhost');
define('DB_NAME', 'midulcerincon_db');
define('DB_USER', 'root');
define('DB_PASS', 'root'); // Asegúrate que esta es la correcta para tu configuración de MAMP

try {
    // Intenta establecer la conexión PDO
    $conn = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASS
    );

    // Configura el modo de errores para PDO: lanza excepciones en caso de error
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Configura el modo de obtención de resultados por defecto a array asociativo
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Si la conexión es exitosa, no necesitamos un mensaje.
    // **¡IMPORTANTE! Elimina la línea de echo de conexión, si la tenías aquí.**
    // Si la tenías, causaba que el texto "Conexión exitosa" apareciera en la parte superior de tu página.
    // echo "Conexión a la base de datos exitosa."; // ELIMINAR O COMENTAR ESTA LÍNEA

} catch (PDOException $e) {
    // Si hay un error en la conexión, muestra un mensaje y termina el script
    // error_log("Error de conexión a la base de datos: " . $e->getMessage()); // Para registrar en logs
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>