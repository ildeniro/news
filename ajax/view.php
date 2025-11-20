<?php
// ajax/view.php - Incrementa view de forma segura
require_once "../config/database.php";
if (isset($_GET['slug']) && !empty($_GET['slug'])) {
    $slug = $_GET['slug'];
    $stmt = $mysqli->prepare("UPDATE articles SET views = views + 1 WHERE slug = ? AND status = 'published'");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
}
exit;
?>