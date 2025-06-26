<?php
session_start();

// Habilitar la visualización de todos los errores de PHP para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Requerir el archivo de verificación de autenticación
require 'auth_check.php';

// TEMPORALMENTE COMENTA ESTA LÍNEA PARA DEPURAR LA CARGA DE POSTS.
// Si tu página de inicio es pública, no deberías requerir autenticación aquí.
// Si esta línea causa redirección, impedirá que veas los posts.
// requireAuth(); 

// Requerir el archivo de conexión a la base de datos.
// Asegúrate de que db.php NO contenga duplicados de define('DB_HOST',...)
// y que SOLO esté incluido una vez en tu aplicación.
require 'db.php';

$posts = [];
try {
    // Consulta para obtener las publicaciones más recientes
    $stmt_posts = $conn->query("SELECT id, title, image_url, category, created_at, read_time_minutes, SUBSTRING(content, 1, 150) AS content_excerpt FROM posts ORDER BY created_at DESC LIMIT 3");
    $posts = $stmt_posts->fetchAll(PDO::FETCH_ASSOC);

    // --- INICIO DE DEPURACIÓN TEMPORAL ---
    // ESTO MOSTRARÁ EL CONTENIDO DE LA VARIABLE $posts EN LA PÁGINA.
    // ES VITAL PARA SABER SI LA CONSULTA A LA DB ESTÁ TRAYENDO DATOS.
   // echo "<pre>";
   // echo "Contenido de \$posts:\n";
    //print_r($posts);
    //echo "</pre>";
    // --- FIN DE DEPURACIÓN TEMPORAL ---

} catch (PDOException $e) {
    // Registra el error en el log del servidor
    error_log("Error al obtener posts para index.php: " . $e->getMessage());
    // Opcional: Muestra un mensaje amigable al usuario
    // echo "<p>Ha ocurrido un error al cargar las publicaciones. Por favor, inténtalo de nuevo más tarde.</p>";
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Dulce Rincón - Blog Personal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <meta name="description" content="Blog personal de Melissa compartiendo pensamientos, recetas, viajes y momentos especiales">
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
                    <li><a href="#inicio">Inicio</a></li>
                    <li><a href="#sobre-mi">Sobre Mí</a></li>
                    <li><a href="#blog">Blog</a></li>
                    <li><a href="#contacto">Contacto</a></li>
                    <?php if (isset($_SESSION['user_id'])): // Mostrar opciones de usuario si está logueado ?>
                        <li class="user-menu">
                            <a href="#"><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuario'); ?></a>
                            <ul class="dropdown">
                                <li><a href="mi-perfil.php"><i class="fas fa-user"></i> Mi perfil</a></li>
                                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
                            </ul>
                        </li>
                    <?php else: // Mostrar opciones para invitados ?>
                        <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</a></li>
                        <li><a href="registro.php"><i class="fas fa-user-plus"></i> Registrarse</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </header>

        <section id="inicio" class="hero">
            <div class="hero-content">
                <h2>Bienvenid@ a mi mundo</h2>
                <p>Un espacio cálido donde comparto mis pensamientos, recetas, viajes y momentos especiales</p>
                <a href="#blog" class="btn btn-primary">Explora mis publicaciones <i class="fas fa-arrow-down"></i></a>
            </div>
            <div class="hero-scroll">
                <a href="#sobre-mi" class="scroll-down">
                    <i class="fas fa-chevron-down"></i>
                </a>
            </div>
        </section>

        <section id="sobre-mi" class="about">
            <div class="about-img" data-aos="fade-right">
                <img src="img/Imagen de WhatsApp 2025-06-13 a las 18.40.58_8226c2f3.jpg" alt="Foto de perfil de Melissa" loading="lazy">
            </div>
            <div class="about-content" data-aos="fade-left">
                <h2>Sobre Mí</h2>
                <p>¡Hola! Soy <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Meli'); ?>, una estudiante universitaria apasionada por la fotografía y los pequeños detalles que hacen cada día especial. Este blog es mi espacio personal donde comparto mis experiencias, reflexiones y todo aquello que me inspira.</p>
                <p>Me encanta cocinar postres, viajar a lugares con encanto y coleccionar momentos felices. Creo firmemente que la belleza está en las cosas simples y quiero transmitir nuevas etapas en mi vida diaria.</p>
                <div class="social-icons">
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="Pinterest"><i class="fab fa-pinterest"></i></a>
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                    <a href="mailto:melisitavz@gmail.com" aria-label="Email"><i class="fas fa-envelope"></i></a>
                </div>
            </div>
        </section>

        <section id="blog" class="blog">
            <div class="section-header">
                <h2>Mis Publicaciones Recientes</h2>
                <div class="category-filter">
                    <button class="filter-btn active" data-category="all">Todas</button>
                    <button class="filter-btn" data-category="Lifestyle">Lifestyle</button>
                    <button class="filter-btn" data-category="Lectura">Lectura</button>
                    <button class="filter-btn" data-category="Viajes">Viajes</button>
                </div>
            </div>

            <div class="blog-grid">
                <?php if (!empty($posts)): ?>
                    <?php foreach ($posts as $post): ?>
                        <article class="post" data-category="<?php echo htmlspecialchars($post['category']); ?>" data-aos="fade-up">
                            <div class="post-img">
                                <img src="<?php echo htmlspecialchars($post['image_url']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" loading="lazy">
                                <span class="category"><?php echo htmlspecialchars($post['category']); ?></span>
                            </div>
                            <div class="post-content">
                                <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                                <div class="post-meta">
                                    <span><i class="far fa-calendar"></i> <?php echo date('d \d\e F, Y', strtotime($post['created_at'])); ?></span>
                                    <span><i class="far fa-clock"></i> <?php echo htmlspecialchars($post['read_time_minutes']); ?> min lectura</span>
                                </div>
                                <p><?php echo htmlspecialchars($post['content_excerpt']); ?>...</p>
                                <a href="post-detalle.php?id=<?php echo htmlspecialchars($post['id']); ?>" class="read-more">Leer más <i class="fas fa-arrow-right"></i></a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No hay publicaciones para mostrar.</p>
                <?php endif; ?>
            </div>

            <div class="text-center">
                <a href="blog.php" class="btn btn-outline">Ver todas las publicaciones</a>
            </div>
        </section>

        <section class="newsletter">
            <div class="newsletter-content" data-aos="fade-up">
                <h2>Suscríbete a mi newsletter</h2>
                <p>Recibe mis últimas publicaciones, recetas exclusivas y contenido especial directamente en tu correo.</p>
                <form class="newsletter-form" id="newsletterForm">
                    <div class="form-group">
                        <input type="email" id="newsletter-email" placeholder="Tu correo electrónico" required>
                        <button type="submit" class="btn btn-primary">Suscribirme</button>
                    </div>
                    <div class="newsletter-message" id="newsletterMessage"></div>
                </form>
            </div>
        </section>

        <section id="contacto" class="contact">
            <h2 data-aos="fade-up">Contáctame</h2>
            <div class="contact-container">
                <div class="contact-info" data-aos="fade-right">
                    <h3>¡Hablemos!</h3>
                    <p>Me encantaría conocer tus impresiones, sugerencias o simplemente charlar sobre temas que nos apasionen.</p>
                    <ul>
                        <li><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($_SESSION['user_email'] ?? 'tu-correo@ejemplo.com'); ?></li>
                        <li><i class="fas fa-phone"></i> +52 971 220 7370</li>
                        <li><i class="fas fa-map-marker-alt"></i> Ixtepec, Oaxaca, México</li>
                    </ul>
                    <div class="contact-social">
                        <a href="https://www.instagram.com/m_li19tyu/" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="https://mx.pinterest.com/melisitavz/" aria-label="Pinterest"><i class="fab fa-pinterest"></i></a>
                        <a href="https://www.facebook.com/meli.vasquez.243696" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                        <a href="mailto:melisitavz@gmail.com" aria-label="Email"><i class="fas fa-envelope"></i></a>
                    </div>
                </div>

                <form class="contact-form" id="contactForm" data-aos="fade-left">
                    <div class="form-group">
                        <input type="text" id="contact-name" placeholder="Tu nombre" required>
                    </div>
                    <div class="form-group">
                        <input type="email" id="contact-email" placeholder="Tu correo electrónico" required>
                    </div>
                    <div class="form-group">
                        <textarea id="contact-message" placeholder="Tu mensaje" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Enviar mensaje</button>
                    <div class="form-message" id="contactMessage"></div>
                </form>
            </div>
        </section>

        <footer class="footer">
            <div class="footer-content">
                <div class="footer-logo">
                    <h3>Mi Dulce Rincón</h3>
                    <p>Compartiendo momentos dulces desde 2020</p>
                </div>
                <div class="footer-links">
                    <h4>Enlaces rápidos</h4>
                    <ul>
                        <li><a href="#inicio">Inicio</a></li>
                        <li><a href="#sobre-mi">Sobre Mí</a></li>
                        <li><a href="#blog">Blog</a></li>
                        <li><a href="#contacto">Contacto</a></li>
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