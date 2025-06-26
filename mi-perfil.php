<?php
// mi-perfil.php

session_start(); // Asegura que la sesión se inicie

// Habilitar la visualización de errores solo para depuración. ¡RECUERDA ELIMINAR ESTAS LÍNEAS EN PRODUCCIÓN!
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require 'auth_check.php';
requireAuth(); // Asegura que el usuario esté logueado

// Incluir el archivo de conexión a la base de datos.
// Ahora apunta a 'db.php' y esperamos que defina la variable $conn.
require 'db.php'; 

// Asumiendo que el ID del usuario está en la sesión después de iniciar sesión
$user_id = $_SESSION['user_id']; 

// Obtener datos del usuario desde la tabla 'usuarios'
// Usamos AS para darles un alias que coincida con el nombre que usamos en el HTML (username y created_at)
try {
    $stmt = $conn->prepare("SELECT nombre AS username, email, fecha_registro AS created_at FROM usuarios WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // En producción, esto debería ser un error_log, no un die().
    error_log("Error al obtener datos del usuario en mi-perfil.php: " . $e->getMessage());
    header("Location: logout.php?error=db_error"); // Redirigir a logout si no se puede cargar el perfil
    exit();
}


// Redirigir si el usuario no se encuentra (aunque requireAuth() ya debería manejar esto)
if (!$user) {
    header("Location: logout.php");
    exit();
}

// Lógica para obtener las publicaciones del usuario desde la tabla 'posts'
try {
    $stmt_posts = $conn->prepare("SELECT id, title, created_at FROM posts WHERE user_id = ? ORDER BY created_at DESC");
    $stmt_posts->execute([$user_id]);
    $user_posts = $stmt_posts->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // En producción, esto debería ser un error_log.
    error_log("Error al obtener publicaciones del usuario en mi-perfil.php: " . $e->getMessage());
    // No detenemos el script, solo no mostramos las publicaciones
    $user_posts = []; // Asegura que $user_posts sea un array vacío para evitar errores
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Mi Dulce Rincón</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="logo">
                <h1>Mi Dulce Rincón</h1>
                <p>Un espacio para compartir mis pensamientos y experiencias</p>
            </div>
            <button class="menu-toggle" aria-label="Menú">
                <i class="fas fa-bars"></i>
            </button>
            <nav class="navbar">
                <ul>
                    <li><a href="index.php#inicio">Inicio</a></li>
                    <li><a href="index.php#sobre-mi">Sobre Mí</a></li>
                    <li><a href="index.php#blog">Blog</a></li>
                    <li><a href="index.php#contacto">Contacto</a></li>
                    <li class="user-menu">
                        <a href="#"><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?></a>
                        <ul class="dropdown">
                            <li><a href="mi-perfil.php"><i class="fas fa-user"></i> Mi perfil</a></li>
                            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </header>

        <main class="profile-content">
            <section class="profile-section" data-aos="fade-up">
                <h2>Bienvenido, <?php echo htmlspecialchars($user['username']); ?>!</h2>
                <div class="profile-details">
                    <p><strong>Nombre de Usuario:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                    <p><strong>Correo Electrónico:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p><strong>Miembro desde:</strong> <?php echo date('d M, Y', strtotime($user['created_at'])); ?></p>
                </div>
                <div class="profile-actions">
                    <a href="edit_profile.php" class="btn btn-primary">Editar Perfil</a>
                    <a href="change_password.php" class="btn btn-secondary">Cambiar Contraseña</a>
                </div>
            </section>

            <hr> <section class="my-posts-section" data-aos="fade-up" data-aos-delay="100">
                <h3>Mis Publicaciones</h3>
                <?php if (!empty($user_posts)): ?>
                    <div class="posts-list">
                        <?php foreach ($user_posts as $post): ?>
                            <div class="post-item">
                                <h4><a href="post-detalle.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h4>
                                <p><small>Publicado el: <?php echo date('d M, Y', strtotime($post['created_at'])); ?></small></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>Aún no has creado ninguna publicación.</p>
                <?php endif; ?>
            </section>

        </main>

        <footer class="footer">
            <div class="footer-content">
                <div class="footer-logo">
                    <h3>Mi Dulce Rincón</h3>
                    <p>Compartiendo momentos dulces desde 2020</p>
                </div>
                <div class="footer-links">
                    <h4>Enlaces rápidos</h4>
                    <ul>
                        <li><a href="index.php#inicio">Inicio</a></li>
                        <li><a href="index.php#sobre-mi">Sobre Mí</a></li>
                        <li><a href="index.php#blog">Blog</a></li>
                        <li><a href="index.php#contacto">Contacto</a></li>
                        <li><a href="politica-privacidad.php">Política de Privacidad</a></li>
                    </ul>
                </div>
                <div class="footer-newsletter">
                    <h4>Newsletter</h4>
                    <p>Suscríbete para recibir actualizaciones</p>
                    <form class="footer-newsletter-form">
                        <input type="email" placeholder="Tu correo">
                        <button type="submit"><i class="fas fa-paper-plane"></i></button>
                    </form>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Mi Dulce Rincón. Todos los derechos reservados.</p>
            </div>
        </footer>
    </div>

    <a href="#" class="back-to-top" aria-label="Volver arriba">
        <i class="fas fa-arrow-up"></i>
    </a>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Inicializar AOS
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 800,
                easing: 'ease-in-out',
                once: true,
                offset: 120
            });
        });
    </script>
    <script src="script.js" defer></script>
</body>
</html>