<?php
session_start();

// Habilitar la visualización de todos los errores de PHP para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// No requerimos requireAuth() aquí si queremos que las publicaciones sean públicas para todos.
// Si quieres que solo usuarios logueados vean los detalles de los posts, descomenta la siguiente línea:
// require 'auth_check.php';
// requireAuth(); // Si la anterior línea está descomentada, esta también.

require 'db.php'; // Tu archivo de conexión a la base de datos, que define $conn

$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($post_id === 0) {
    // Redirigir si no hay ID de post válido
    header("Location: index.php#blog");
    exit();
}

// Obtener detalles de la publicación
try {
    $stmt = $conn->prepare("SELECT p.*, u.nombre AS author_name FROM posts p JOIN usuarios u ON p.user_id = u.id WHERE p.id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error al obtener detalles de la publicación en post-detalle.php: " . $e->getMessage());
    die("Lo siento, no se pudo cargar la publicación en este momento. Inténtalo de nuevo más tarde.");
}

if (!$post) {
    // Redirigir si la publicación no existe
    header("Location: index.php#blog");
    exit();
}

// Obtener el número de likes para esta publicación
try {
    $stmt_likes = $conn->prepare("SELECT COUNT(*) AS total_likes FROM likes WHERE post_id = ?");
    $stmt_likes->execute([$post_id]);
    $likes_count = $stmt_likes->fetch(PDO::FETCH_ASSOC)['total_likes'];
} catch (PDOException $e) {
    error_log("Error al obtener likes en post-detalle.php: " . $e->getMessage());
    $likes_count = 0; // Fallback seguro
}

// Verificar si el usuario actual ya le dio "me gusta" a esta publicación
$user_has_liked = false;
if (isset($_SESSION['user_id'])) {
    try {
        $stmt_user_like = $conn->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ? AND user_id = ?");
        $stmt_user_like->execute([$post_id, $_SESSION['user_id']]);
        $user_has_liked = ($stmt_user_like->fetchColumn() > 0);
    } catch (PDOException $e) {
        error_log("Error al verificar like de usuario en post-detalle.php: " . $e->getMessage());
    }
}

// Obtener comentarios para esta publicación
try {
    $stmt_comments = $conn->prepare("SELECT c.*, u.nombre AS username FROM comments c JOIN usuarios u ON c.user_id = u.id WHERE c.post_id = ? ORDER BY c.created_at DESC");
    $stmt_comments->execute([$post_id]);
    $comments = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error al obtener comentarios en post-detalle.php: " . $e->getMessage());
    $comments = []; // Fallback seguro
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?> - Mi Dulce Rincón</title>
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
                    <?php if (isset($_SESSION['user_id'])): ?>
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

        <main class="post-detail-content">
            <article class="full-post" data-aos="fade-up">
                <?php if ($post['image_url']): ?>
                    <div class="post-detail-img">
                        <img src="<?php echo htmlspecialchars($post['image_url']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" loading="lazy">
                        <span class="category"><?php echo htmlspecialchars($post['category']); ?></span>
                    </div>
                <?php endif; ?>
                <div class="post-detail-header">
                    <h1><?php echo htmlspecialchars($post['title']); ?></h1>
                    <div class="post-meta">
                        <span><i class="far fa-calendar"></i> <?php echo date('d \d\e F, Y', strtotime($post['created_at'])); ?></span>
                        <span><i class="fas fa-user"></i> Por <?php echo htmlspecialchars($post['author_name']); ?></span>
                        <?php if (isset($post['read_time_minutes'])): ?>
                            <span><i class="far fa-clock"></i> <?php echo htmlspecialchars($post['read_time_minutes']); ?> min lectura</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="post-detail-body">
                    <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                </div>

                <div class="post-actions">
                    <button class="like-button <?php echo $user_has_liked ? 'liked' : ''; ?>" data-post-id="<?php echo $post_id; ?>">
                        <i class="fas fa-heart"></i> <span class="likes-count"><?php echo $likes_count; ?></span> Me gusta
                    </button>
                    <a href="#comments-section" class="comment-scroll-btn"><i class="fas fa-comment"></i> Comentar</a>
                </div>
            </article>

            <hr>

            <section id="comments-section" class="comments-section" data-aos="fade-up" data-aos-delay="100">
                <h3>Comentarios (<span id="comments-count"><?php echo count($comments); ?></span>)</h3>
                <?php if (isset($_SESSION['user_id'])): ?>
                <div class="comment-form-container">
                    <h4>Deja un comentario</h4>
                    <form id="commentForm">
                        <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                        <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                        <div class="form-group">
                            <textarea name="comment_text" placeholder="Escribe tu comentario aquí..." rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Publicar Comentario</button>
                        <div id="commentMessage" class="form-message"></div>
                    </form>
                </div>
                <?php else: ?>
                    <p>Necesitas <a href="login.php">iniciar sesión</a> para dejar un comentario.</p>
                <?php endif; ?>

                <div class="comments-list" id="comments-list">
                    <?php if (!empty($comments)): ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="comment-item">
                                <p class="comment-author"><strong><?php echo htmlspecialchars($comment['username']); ?></strong> <small><?php echo date('d M, Y H:i', strtotime($comment['created_at'])); ?></small></p>
                                <p class="comment-text"><?php echo nl2br(htmlspecialchars($comment['comment_text'])); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Sé el primero en dejar un comentario.</p>
                    <?php endif; ?>
                </div>
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

            // Lógica para "Me gusta"
            const likeButton = document.querySelector('.like-button');
            if (likeButton) {
                likeButton.addEventListener('click', function() {
                    // Check if user is logged in before allowing like/unlike
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        alert('Necesitas iniciar sesión para dar "Me gusta".');
                        window.location.href = 'login.php'; // Redirect to login
                        return; // Stop execution
                    <?php endif; ?>

                    const postId = this.dataset.postId;
                    const isLiked = this.classList.contains('liked');
                    const action = isLiked ? 'unlike' : 'like';

                    fetch('handle_like.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `post_id=${postId}&action=${action}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const likesCountSpan = this.querySelector('.likes-count');
                            likesCountSpan.textContent = data.total_likes;
                            if (action === 'like') {
                                this.classList.add('liked');
                            } else {
                                this.classList.remove('liked');
                            }
                        } else {
                            // Show error message from server
                            alert(data.message || 'Ocurrió un error al procesar tu "Me gusta".');
                        }
                    })
                    .catch(error => console.error('Error:', error));
                });
            }

            // Lógica para Comentarios
            const commentForm = document.getElementById('commentForm');
            if (commentForm) {
                commentForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    // No es necesario verificar isLoggedIn aquí, el bloque PHP ya oculta el formulario
                    const formData = new FormData(this);

                    fetch('handle_comment.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        const commentMessage = document.getElementById('commentMessage');
                        if (data.success) {
                            commentMessage.textContent = data.message;
                            commentMessage.style.color = 'green';
                            // Limpiar el textarea
                            this.querySelector('textarea[name="comment_text"]').value = '';
                            // Añadir dinámicamente el nuevo comentario al DOM
                            addCommentToDOM(data.comment); 
                            updateCommentsCount(); // Actualiza el contador de comentarios
                        } else {
                            commentMessage.textContent = data.message;
                            commentMessage.style.color = 'red';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        document.getElementById('commentMessage').textContent = 'Ocurrió un error al enviar el comentario.';
                        document.getElementById('commentMessage').style.color = 'red';
                    });
                });
            }

            function addCommentToDOM(comment) {
                const commentsList = document.getElementById('comments-list');
                const noCommentsMessage = commentsList.querySelector('p'); // Get the "Sé el primero..." message

                // If "Sé el primero..." message exists, remove it
                if (noCommentsMessage && noCommentsMessage.textContent.includes('Sé el primero')) {
                    noCommentsMessage.remove();
                }

                const newCommentDiv = document.createElement('div');
                newCommentDiv.classList.add('comment-item');
                // Ensure htmlspecialchars and nl2br are applied
                newCommentDiv.innerHTML = `
                    <p class="comment-author"><strong>${htmlspecialchars(comment.username)}</strong> <small>${comment.created_at}</small></p>
                    <p class="comment-text">${nl2br(htmlspecialchars(comment.comment_text))}</p>
                `;
                commentsList.prepend(newCommentDiv); // Añade el nuevo comentario al principio
            }

            function updateCommentsCount() {
                const commentsCountSpan = document.getElementById('comments-count');
                let currentCount = parseInt(commentsCountSpan.textContent);
                commentsCountSpan.textContent = currentCount + 1;
            }

            // Función para escapar HTML (seguridad)
            function htmlspecialchars(str) {
                if (typeof str !== 'string') {
                    return ''; // Ensure it's a string
                }
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return str.replace(/[&<>"']/g, function(m) { return map[m]; });
            }

            // Función para nl2br (simular PHP nl2br)
            function nl2br(str) {
                if (typeof str !== 'string') {
                    return ''; // Ensure it's a string
                }
                return str.replace(/\n/g, '<br />');
            }
        });
    </script>
    <script src="script.js" defer></script>
</body>
</html>