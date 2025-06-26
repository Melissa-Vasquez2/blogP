<?php
session_start();
require 'db.php';

// Si el usuario ya está logueado, redirigir al inicio
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Mi Dulce Rincón</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="logo">
                <h1>Mi Dulce Rincón</h1>
                <p>Únete a nuestra comunidad</p>
            </div>
        </header>

        <section class="auth-form">
            <div class="form-container">
                <h2>Crear una cuenta</h2>
                
                <?php if(isset($_GET['error'])): ?>
                <div class="error-message">
                    <?php 
                    switch($_GET['error']) {
                        case 'empty_fields':
                            echo "Por favor completa todos los campos";
                            break;
                        case 'invalid_email':
                            echo "El correo electrónico no es válido";
                            break;
                        case 'email_exists':
                            echo "Este correo ya está registrado";
                            break;
                        case 'password_mismatch':
                            echo "Las contraseñas no coinciden";
                            break;
                        default:
                            echo "Ocurrió un error al registrarse";
                    }
                    ?>
                </div>
                <?php endif; ?>

                <?php if(isset($_GET['registro']) && $_GET['registro'] === 'success'): ?>
                <div class="success-message">
                    ¡Registro exitoso! Por favor inicia sesión.
                </div>
                <?php endif; ?>

                <form action="procesar_registro.php" method="post">
                    <div class="form-group">
                        <label for="nombre">Nombre completo</label>
                        <input type="text" id="nombre" name="nombre" required 
                               value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Correo electrónico</label>
                        <input type="email" id="email" name="email" required
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirmar contraseña</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn">Registrarse</button>
                </form>
                <p class="auth-link">¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
            </div>
        </section>
    </div>
</body>
</html>