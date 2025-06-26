<?php
session_start();
header('Content-Type: application/json');

require 'db.php'; // Tu archivo de conexión a la base de datos, que define $conn

$response = ['success' => false, 'message' => '', 'comment' => null];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Debes iniciar sesión para comentar.';
    echo json_encode($response);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id']) && isset($_POST['comment_text'])) {
    $post_id = (int)$_POST['post_id'];
    $user_id = $_SESSION['user_id'];
    $comment_text = trim($_POST['comment_text']);

    if (empty($comment_text)) {
        $response['message'] = 'El comentario no puede estar vacío.';
        echo json_encode($response);
        exit();
    }

    try {
        $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment_text) VALUES (?, ?, ?)");
        $stmt->execute([$post_id, $user_id, $comment_text]);

        // Obtener los datos del comentario recién insertado para devolverlo
        $comment_id = $conn->lastInsertId();
        // Usamos u.nombre AS username para obtener el nombre del usuario desde la tabla 'usuarios'
        $stmt_fetch = $conn->prepare("SELECT c.*, u.nombre AS username FROM comments c JOIN usuarios u ON c.user_id = u.id WHERE c.id = ?");
        $stmt_fetch->execute([$comment_id]);
        $new_comment = $stmt_fetch->fetch(PDO::FETCH_ASSOC);
        
        // Formatear la fecha para que coincida con el formato de visualización en el frontend
        if ($new_comment) {
            $new_comment['created_at'] = date('d M, Y H:i', strtotime($new_comment['created_at']));
        }

        $response['success'] = true;
        $response['message'] = 'Comentario publicado con éxito.';
        $response['comment'] = $new_comment;

    } catch (PDOException $e) {
        error_log("Error al publicar comentario en handle_comment.php: " . $e->getMessage()); // Registrar el error
        $response['message'] = 'Ocurrió un error interno al publicar el comentario.';
    }

} else {
    $response['message'] = 'Solicitud inválida.';
}

echo json_encode($response);
?>