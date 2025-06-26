<?php
// auth_check.php
// Contiene funciones para verificar el estado de autenticación del usuario.

// Asegura que la sesión se inicie antes de usar $_SESSION
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// **IMPORTANTE:** ELIMINA LA SIGUIENTE LÍNEA.
// require_once 'db.php'; // <--- ESTA LÍNEA CAUSA LOS WARNINGS. BÓRRALA.

function requireAuth() {
    // Para que checkRememberMe pueda acceder a $conn,
    // $conn debe estar disponible GLOBALMENTE en el script principal
    // ANTES de que se llame a requireAuth().
    // Esto se logra incluyendo db.php en index.php (o el script principal)
    // ANTES de incluir auth_check.php.

    if (!isset($_SESSION['user_id'])) {
        if (!checkRememberMe()) {
            // Si no está logueado y no hay cookie válida, redirige al login
            header("Location: login.php");
            exit();
        }
    }
}

function checkRememberMe() {
    // Si $conn no está disponible en este scope, debemos asegurarnos de que lo esté.
    // Esto significa que el archivo principal (como index.php) debe hacer un
    // 'require_once 'db.php';' antes de 'require 'auth_check.php';'.
    global $conn; // Accede a la variable $conn que se definió en el script principal

    if (isset($_COOKIE['remember_token']) && !empty($_COOKIE['remember_token'])) {
        $token = $_COOKIE['remember_token'];

        try {
            // Asegúrate de que $conn esté disponible aquí. Si no lo está,
            // el script principal no incluyó db.php correctamente antes de auth_check.php.
            if (!isset($conn)) {
                // Esto es una medida de seguridad si la configuración es incorrecta.
                // En una configuración ideal, $conn ya existiría.
                error_log("Error: \$conn no está definido en checkRememberMe. Verifique el orden de inclusión.");
                setcookie('remember_token', '', time() - 3600, "/"); // Elimina la cookie por seguridad
                return false;
            }

            $stmt = $conn->prepare("SELECT id, nombre, email, token_expiry FROM usuarios WHERE remember_token = ?");
            $stmt->execute([$token]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && strtotime($user['token_expiry']) > time()) {
                // Token válido, recrea la sesión
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nombre'];
                $_SESSION['user_email'] = $user['email'];

                // Opcional: Extender la vida del token (sliding window)
                $new_expiry = date('Y-m-d H:i:s', strtotime('+30 days'));
                $stmt_update = $conn->prepare("UPDATE usuarios SET token_expiry = ? WHERE id = ?");
                $stmt_update->execute([$new_expiry, $user['id']]);
                // La cookie también debe tener el dominio y secure flags correctos si estás en HTTPS
                setcookie('remember_token', $token, [
                    'expires' => time() + (86400 * 30),
                    'path' => "/",
                    'domain' => '', // Deja vacío para el dominio actual
                    'secure' => isset($_SERVER['HTTPS']), // true si es HTTPS, false si es HTTP
                    'httponly' => true, // Previene acceso por JavaScript
                    'samesite' => 'Lax' // Protege contra CSRF (Lax o Strict)
                ]);

                return true;
            } else {
                // Token inválido o expirado, elimínalo
                setcookie('remember_token', '', time() - 3600, "/"); // Elimina la cookie
                return false;
            }
        } catch (PDOException $e) {
            error_log("Error de DB en checkRememberMe: " . $e->getMessage());
            setcookie('remember_token', '', time() - 3600, "/"); // Elimina la cookie por seguridad
            return false;
        }
    }
    return false;
}
?>