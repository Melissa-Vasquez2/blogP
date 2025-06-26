<?php
// change_password.php
session_start();
require 'auth_check.php';
requireAuth(); // Requiere que el usuario esté logueado
require 'db.php'; // Conexión a la base de datos

$user_id = $_SESSION['user_id'];
$message = '';
$message_type = ''; // 'success' o 'error'

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    // Obtener la contraseña actual hasheada del usuario
    try {
        $stmt = $conn->prepare("SELECT password FROM usuarios WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            header("Location: logout.php"); // Si el usuario no existe, redirigir
            exit();
        }

        // 1. Verificar que la contraseña actual sea correcta
        if (!password_verify($current_password, $user['password'])) {
            $message = "La contraseña actual es incorrecta.";
            $message_type = 'error';
        } 
        // 2. Validar que la nueva contraseña no esté vacía y coincida
        elseif (empty($new_password)) {
            $message = "La nueva contraseña no puede estar vacía.";
            $message_type = 'error';
        }
        elseif (strlen($new_password) < 6) { // Ejemplo: mínimo 6 caracteres
            $message = "La nueva contraseña debe tener al menos 6 caracteres.";
            $message_type = 'error';
        }
        elseif ($new_password !== $confirm_new_password) {
            $message = "Las nuevas contraseñas no coinciden.";
            $message_type = 'error';
        }
        // 3. No permitir que la nueva contraseña sea igual a la antigua
        elseif (password_verify($new_password, $user['password'])) {
            $message = "La nueva contraseña no puede ser igual a la anterior.";
            $message_type = 'error';
        }
        else {
            // 4. Hashear y actualizar la nueva contraseña
            $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt_update = $conn->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
            $stmt_update->execute([$hashed_new_password, $user_id]);

            $message = "Contraseña cambiada con éxito.";
            $message_type = 'success';
        }

    } catch (PDOException $e) {
        error_log("Error al cambiar contraseña: " . $e->getMessage());
        $message = "Ocurrió un error al cambiar tu contraseña.";
        $message_type = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña - Mi Dulce Rincón</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="logo">
                <h1>Mi Dulce Rincón</h1>
                <p>Cambia tu contraseña</p>
            </div>
            <button class="menu-toggle" aria-label="Menú">
                <i class="fas fa-bars"></i>
            </button>
            <nav class="navbar">
                <ul>
                    <li><a href="index.php#inicio">Inicio</a></li>
                    <li><a href="mi-perfil.php">Mi perfil</a></li>
                    <li><a href="logout.php">Cerrar sesión</a></li>
                </ul>
            </nav>
        </header>

        <section class="auth-form">
            <div class="form-container">
                <h2>Cambiar Contraseña</h2>

                <?php if ($message): ?>
                    <div class="<?php echo $message_type; ?>-message">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form action="change_password.php" method="post">
                    <div class="form-group">
                        <label for="current_password">Contraseña actual</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">Nueva contraseña</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_new_password">Confirmar nueva contraseña</label>
                        <input type="password" id="confirm_new_password" name="confirm_new_password" required>
                    </div>
                    <button type="submit" class="btn">Cambiar Contraseña</button>
                </form>
                <p class="auth-link"><a href="mi-perfil.php">Volver a Mi Perfil</a></p>
            </div>
        </section>
    </div>
    <script src="script.js" defer></script>
</body>
</html>