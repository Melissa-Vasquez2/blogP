<?php
session_start();
header('Content-Type: application/json');

require 'db.php'; // Tu archivo de conexión a la base de datos, que define $conn

$response = ['success' => false, 'message' => '', 'total_likes' => 0];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Debes iniciar sesión para dar me gusta.';
    echo json_encode($response);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id']) && isset($_POST['action'])) {
    $post_id = (int)$_POST['post_id'];
    $user_id = $_SESSION['user_id'];
    $action = $_POST['action'];

    if ($action === 'like') {
        try {
            // Intentar insertar el like
            $stmt = $conn->prepare("INSERT INTO likes (post_id, user_id) VALUES (?, ?)");
            $stmt->execute([$post_id, $user_id]);
            $response['success'] = true;
            $response['message'] = 'Me gusta añadido.';
        } catch (PDOException $e) {
            // Código '23000' es para violación de restricción UNIQUE (ya le dio like)
            if ($e->getCode() == '23000') { 
                $response['success'] = true; // No es un error funcional
                $response['message'] = 'Ya te gusta esta publicación.';
            } else {
                error_log("Error al dar me gusta en handle_like.php: " . $e->getMessage()); // Registrar el error
                $response['message'] = 'Ocurrió un error interno al procesar tu solicitud.';
            }
        }
    } elseif ($action === 'unlike') {
        try {
            // Eliminar el like
            $stmt = $conn->prepare("DELETE FROM likes WHERE post_id = ? AND user_id = ?");
            $stmt->execute([$post_id, $user_id]);
            $response['success'] = true;
            $response['message'] = 'Me gusta eliminado.';
        } catch (PDOException $e) {
            error_log("Error al quitar me gusta en handle_like.php: " . $e->getMessage()); // Registrar el error
            $response['message'] = 'Ocurrió un error interno al procesar tu solicitud.';
        }
    } else {
        $response['message'] = 'Acción inválida.';
    }

    // Obtener el nuevo conteo de likes
    try {
        $stmt_likes = $conn->prepare("SELECT COUNT(*) AS total_likes FROM likes WHERE post_id = ?");
        $stmt_likes->execute([$post_id]);
        $response['total_likes'] = $stmt_likes->fetch(PDO::FETCH_ASSOC)['total_likes'];
    } catch (PDOException $e) {
        error_log("Error al obtener conteo de likes en handle_like.php: " . $e->getMessage());
        // Si hay un error aquí, la respuesta 'total_likes' será 0, que es un buen fallback.
    }

} else {
    $response['message'] = 'Solicitud inválida.';
}

echo json_encode($response);
?>