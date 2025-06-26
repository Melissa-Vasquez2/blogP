<?php
session_start();
require 'db.php'; // Asegúrate de que db.php define $conn

// Habilitar la visualización de errores solo para depuración. ¡RECUERDA ELIMINAR O COMENTAR EN PRODUCCIÓN!
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    try {
        // Usa $conn en lugar de $pdo
        $stmt = $conn->prepare("SELECT id, nombre, email, password FROM usuarios WHERE email = ? AND activo = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Establecer sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nombre'];
            $_SESSION['user_email'] = $user['email'];

            // Cookie de "recordarme"
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $expiry = date('Y-m-d H:i:s', strtotime('+30 days'));
                
                // Usa $conn en lugar de $pdo
                $stmt = $conn->prepare("UPDATE usuarios SET remember_token = ?, token_expiry = ? WHERE id = ?");
                $stmt->execute([$token, $expiry, $user['id']]);
                
                setcookie(
                    'remember_token',
                    $token,
                    [
                        'expires' => time() + (86400 * 30), // 30 días
                        'path' => '/',
                        'httponly' => true, // La cookie solo es accesible a través de HTTP(S)
                        'secure' => isset($_SERVER['HTTPS']), // Solo envía si la conexión es HTTPS
                        'samesite' => 'Lax' // Protege contra CSRF
                    ]
                );
            }

            header("Location: index.php");
            exit();
        } else {
            // Log de intentos de login fallidos (para seguridad, no lo muestres al usuario)
            error_log("Intento de login fallido para email: " . $email . " desde IP: " . $_SERVER['REMOTE_ADDR']);
            header("Location: login.php?error=credenciales");
            exit();
        }
    } catch(PDOException $e) {
        // En un entorno de producción, registra el error en un log en lugar de mostrarlo al usuario
        error_log("Error de base de datos en procesar_login.php: " . $e->getMessage());
        header("Location: login.php?error=db_error");
        exit();
    }
} else {
    // Si alguien intenta acceder directamente a procesar_login.php sin POST
    header("Location: login.php");
    exit();
}
?>