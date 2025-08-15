<?php
require_once 'includes/config.php';

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    die("ID inválido");
}

$post_id = (int)$_POST['id'];

$sql = "SELECT subtopic_id FROM posts WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $post_id, PDO::PARAM_INT);
$stmt->execute();
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    die("Post não encontrado");
}

$result = deletePost($pdo, $post_id);

$redirect_url = "subtopic.php?subtopic_id=" . $post['subtopic_id'];
if ($result) {
    $redirect_url .= "&msg=success";
} else {
    $redirect_url .= "&msg=error";
}

if (!headers_sent()) {
    header("Location: " . $redirect_url);
    exit();
} else {
    // Se headers já foram enviados, usar JavaScript para redirecionar
    echo "<script>window.location.href='" . $redirect_url . "';</script>";
    exit();
}
?>
