<?php
session_start();
require 'db.php';

// Redirigir si ya está logueado
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
    <title>Login - Mi Dulce Rincón</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="logo">
                <h1>Mi Dulce Rincón</h1>
                <p>Bienvenido de vuelta</p>
            </div>
        </header>

        <section class="auth-form">
            <div class="form-container">
                <h2>Iniciar sesión</h2>
                
                <?php if (isset($_GET['registro']) && $_GET['registro'] === 'exitoso'): ?>
                    <div class="alert alert-success">
                        ¡Registro exitoso! Por favor inicia sesión.
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-error">
                        Credenciales incorrectas. Intenta nuevamente.
                    </div>
                <?php endif; ?>

                <form action="procesar_login.php" method="post">
                    <div class="form-group">
                        <label for="email">Correo electrónico</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Recordar mi sesión</label>
                    </div>
                    <button type="submit" class="btn">Iniciar sesión</button>
                </form>
                <p class="auth-link">¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a></p>
            </div>
        </section>
    </div>
</body>
</html>