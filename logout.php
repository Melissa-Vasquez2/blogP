<?php
session_start();
require 'db.php'; // Asegúrate de que db.php define $conn

// Guarda el user_id antes de destruir la sesión si es necesario para la DB
$user_id = $_SESSION['user_id'] ?? null;

// Eliminar sesión
session_unset();    // Elimina todas las variables de sesión
session_destroy();  // Destruye la sesión

// Eliminar cookie de "recordarme"
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, "/", "", isset($_SERVER['HTTPS']), true); // Asegura httponly y secure

    // Opcional: Eliminar el token de la base de datos solo si teníamos un user_id válido
    if ($user_id) {
        try {
            $stmt = $conn->prepare("UPDATE usuarios 
                                    SET remember_token = NULL, token_expiry = NULL 
                                    WHERE id = ?");
            $stmt->execute([$user_id]);
        } catch (PDOException $e) {
            error_log("Error al limpiar remember_token en DB para user_id " . $user_id . ": " . $e->getMessage());
        }
    }
}

header("Location: login.php");
exit();
?>