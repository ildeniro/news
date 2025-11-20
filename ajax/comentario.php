<?php
// ajax/comentario.php - Insere comentário (moderado)
session_start();
require_once "../config/database.php";

if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) { echo json_encode(['success' => false, 'message' => 'Token inválido']); exit; }

$stmt = $mysqli->prepare("INSERT INTO comments (article_id, user_id, content) VALUES (?, ?, ?)");
$stmt->bind_param('iis', $_POST['article_id'], $_SESSION['id'], $_POST['content']);
$success = $stmt->execute();
echo json_encode(['success' => $success, 'message' => $success ? '' : 'Erro ao enviar']);
$stmt->close();
?>