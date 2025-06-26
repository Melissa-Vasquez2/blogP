<?php
session_start(); // Opcional, pero buena práctica si planeas usar sesiones aquí.
require 'db.php'; // Asegúrate de que db.php define $conn

// Habilitar la visualización de errores solo para depuración. ¡RECUERDA ELIMINAR O COMENTAR EN PRODUCCIÓN!
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validaciones básicas
    if (empty($nombre) || empty($email) || empty($password) || empty($confirm_password)) {
        header("Location: registro.php?error=empty_fields");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: registro.php?error=invalid_email");
        exit();
    }

    // Puedes añadir una validación de longitud de contraseña aquí, por ejemplo:
    if (strlen($password) < 6) { // Ejemplo: mínimo 6 caracteres
        header("Location: registro.php?error=password_too_short");
        exit();
    }

    if ($password !== $confirm_password) {
        header("Location: registro.php?error=password_mismatch");
        exit();
    }

    try {
        // Verificar si el email ya existe
        // Usa $conn en lugar de $pdo
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            header("Location: registro.php?error=email_exists");
            exit();
        }

        // Registrar nuevo usuario
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        // Usa $conn en lugar de $pdo
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, activo) VALUES (?, ?, ?, 1)");
        $stmt->execute([$nombre, $email, $hashedPassword]);

        // Redirigir a login con mensaje de éxito
        header("Location: login.php?registro=exitoso");
        exit();

    } catch(PDOException $e) {
        // En un entorno de producción, registra el error en un log en lugar de mostrarlo al usuario
        error_log("Error de base de datos en procesar_registro.php: " . $e->getMessage());
        header("Location: registro.php?error=db_error");
        exit();
    }
} else {
    // Si alguien intenta acceder directamente a procesar_registro.php sin POST
    header("Location: registro.php");
    exit();
}
?>