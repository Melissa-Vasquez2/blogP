<?php
// edit_profile.php
session_start();
require 'auth_check.php';
requireAuth(); // Requiere que el usuario esté logueado
require 'db.php'; // Conexión a la base de datos

$user_id = $_SESSION['user_id'];
$message = '';
$message_type = ''; // 'success' o 'error'

// Obtener datos actuales del usuario
try {
    $stmt = $conn->prepare("SELECT nombre, email FROM usuarios WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header("Location: logout.php"); // Si el usuario no existe, redirigir
        exit();
    }
} catch (PDOException $e) {
    error_log("Error al cargar datos de perfil: " . $e->getMessage());
    $message = "Error al cargar los datos del perfil.";
    $message_type = 'error';
    // Si hay un error al cargar, usamos valores predeterminados para que la página no se rompa
    $user = ['nombre' => '', 'email' => '']; 
}

// Lógica para procesar la actualización del perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_nombre = trim($_POST['nombre']);
    $new_email = trim($_POST['email']);

    if (empty($new_nombre) || empty($new_email)) {
        $message = "Por favor, completa todos los campos.";
        $message_type = 'error';
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $message = "El correo electrónico no es válido.";
        $message_type = 'error';
    } else {
        try {
            // Verificar si el nuevo email ya existe para otro usuario
            $stmt_check_email = $conn->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
            $stmt_check_email->execute([$new_email, $user_id]);
            if ($stmt_check_email->fetch()) {
                $message = "Este correo electrónico ya está en uso por otra cuenta.";
                $message_type = 'error';
            } else {
                // Actualizar el perfil
                $stmt_update = $conn->prepare("UPDATE usuarios SET nombre = ?, email = ? WHERE id = ?");
                $stmt_update->execute([$new_nombre, $new_email, $user_id]);

                // Actualizar la sesión con los nuevos datos
                $_SESSION['user_name'] = $new_nombre;
                $_SESSION['user_email'] = $new_email;

                $message = "Perfil actualizado con éxito.";
                $message_type = 'success';
                // Actualizar los datos locales para reflejar los cambios en el formulario
                $user['nombre'] = $new_nombre;
                $user['email'] = $new_email;
            }
        } catch (PDOException $e) {
            error_log("Error al actualizar perfil: " . $e->getMessage());
            $message = "Ocurrió un error al actualizar tu perfil.";
            $message_type = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - Mi Dulce Rincón</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="logo">
                <h1>Mi Dulce Rincón</h1>
                <p>Edita tu información personal</p>
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
                <h2>Editar Perfil</h2>

                <?php if ($message): ?>
                    <div class="<?php echo $message_type; ?>-message">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form action="edit_profile.php" method="post">
                    <div class="form-group">
                        <label for="nombre">Nombre completo</label>
                        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($user['nombre']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Correo electrónico</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <button type="submit" class="btn">Guardar Cambios</button>
                </form>
                <p class="auth-link"><a href="mi-perfil.php">Volver a Mi Perfil</a></p>
            </div>
        </section>
    </div>
    <script src="script.js" defer></script>
</body>
</html>