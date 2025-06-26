<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Privacidad - Mi Dulce Rincón</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="logo">
                <h1>Mi Dulce Rincón</h1>
                <p>Nuestra política de privacidad</p>
            </div>
            <button class="menu-toggle" aria-label="Menú">
                <i class="fas fa-bars"></i>
            </button>
            <nav class="navbar">
                <ul>
                    <li><a href="index.php#inicio">Inicio</a></li>
                    <li><a href="index.php#blog">Blog</a></li>
                    <li><a href="index.php#contacto">Contacto</a></li>
                    <?php 
                    session_start(); // Asegúrate de iniciar la sesión para comprobar si el usuario está logueado
                    if (isset($_SESSION['user_id'])): ?>
                        <li class="user-menu">
                            <a href="#"><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?></a>
                            <ul class="dropdown">
                                <li><a href="mi-perfil.php"><i class="fas fa-user"></i> Mi perfil</a></li>
                                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</a></li>
                        <li><a href="registro.php"><i class="fas fa-user-plus"></i> Registrarse</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </header>

        <main class="privacy-policy-content">
            <section class="policy-section" data-aos="fade-up">
                <h2>Política de Privacidad de Mi Dulce Rincón</h2>
                <p>Fecha de última actualización: 25 de junio de 2025</p>

                <h3>1. Introducción</h3>
                <p>En Mi Dulce Rincón, nos comprometemos a proteger la privacidad de nuestros usuarios. Esta Política de Privacidad explica cómo recopilamos, usamos, divulgamos y protegemos su información cuando visita nuestro blog https://www.blogger.com/. Lea esta política detenidamente.</p>

                <h3>2. Información que Recopilamos</h3>
                <p>Podemos recopilar información personal que usted nos proporciona voluntariamente, como su nombre, dirección de correo electrónico y cualquier otra información que elija proporcionar al registrarse, comentar en publicaciones o contactarnos.</p>
                <p>También recopilamos automáticamente cierta información no personal cuando visita nuestro blog, como su dirección IP, tipo de navegador, sistema operativo, páginas visitadas y el tiempo y la fecha de su visita. Esto nos ayuda a comprender cómo los visitantes usan nuestro sitio y a mejorar su experiencia.</p>

                <h3>3. Uso de la Información</h3>
                <p>La información recopilada se utiliza para:</p>
                <ul>
                    <li>Gestionar su cuenta y proporcionarle acceso a las funciones del blog.</li>
                    <li>Publicar sus comentarios y gestionar sus interacciones (como los "me gusta").</li>
                    <li>Mejorar el contenido y la experiencia de usuario de nuestro blog.</li>
                    <li>Responder a sus consultas y proporcionarle soporte.</li>
                    <li>Enviar comunicaciones relevantes, como actualizaciones del blog o boletines (si se ha suscrito).</li>
                </ul>

                <h3>4. Divulgación de la Información</h3>
                <p>No vendemos, comerciamos ni alquilamos su información personal a terceros. Podemos compartir información con proveedores de servicios de confianza que nos asisten en la operación de nuestro sitio web, siempre que acepten mantener esta información confidencial.</p>
                <p>Podemos divulgar su información cuando creamos que es apropiado para cumplir con la ley, hacer cumplir las políticas de nuestro sitio o proteger nuestros derechos, propiedad o seguridad.</p>

                <h3>5. Cookies y Tecnologías de Seguimiento</h3>
                <p>Utilizamos cookies para mejorar su experiencia en nuestro blog. Las cookies son pequeños archivos de datos que se almacenan en su dispositivo y nos ayudan a recordar sus preferencias y a comprender cómo usa nuestro sitio. Puede configurar su navegador para que rechace todas las cookies o para que le notifique cuándo se envía una cookie.</p>

                <h3>6. Seguridad de la Información</h3>
                <p>Tomamos medidas razonables para proteger la información personal que recopilamos. Sin embargo, ninguna transmisión de datos por Internet o método de almacenamiento electrónico es 100% seguro. Por lo tanto, no podemos garantizar su seguridad absoluta.</p>

                <h3>7. Enlaces a Sitios Web de Terceros</h3>
                <p>Nuestro blog puede contener enlaces a sitios web de terceros. No somos responsables de las prácticas de privacidad de estos sitios. Le recomendamos que revise las políticas de privacidad de cualquier sitio de terceros que visite.</p>

                <h3>8. Sus Derechos</h3>
                <p>Usted tiene derecho a acceder, corregir o eliminar su información personal. Si desea ejercer estos derechos, contáctenos a través de [tu_email@tudominio.com].</p>

                <h3>9. Cambios en esta Política de Privacidad</h3>
                <p>Nos reservamos el derecho de actualizar esta Política de Privacidad en cualquier momento. Le notificaremos sobre cualquier cambio publicando la nueva Política de Privacidad en esta página. Se le aconseja revisar esta Política de Privacidad periódicamente para cualquier cambio.</p>

                <h3>10. Contacto</h3>
                <p>Si tiene alguna pregunta sobre esta Política de Privacidad, contáctenos en: [tu_email@tudominio.com]</p>
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