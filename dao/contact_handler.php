<?php

$error = false;

$nome = trim($_POST['nome']);
$email = trim($_POST['email']);
$telefone = trim($_POST['telefone'] ?? '');
$assunto = trim($_POST['assunto']);
$mensagem = trim($_POST['mensagem']);
$usuario_id = $_SESSION['id'] ?? null;

if (strlen($mensagem) < 10) {
    echo 'Mensagem deve ter pelo menos 10 caracteres.';
    exit;
}

try {

    $db->beginTransaction();

    $stmt5 = $db->prepare("INSERT INTO eve_contatos (nome, email, telefone, assunto, mensagem, usuario_id, status) VALUES (?, ?, ?, ?, ?, ?, 1)");
    $stmt5->bindValue(1, $nome);
    $stmt5->bindValue(2, $email);
    $stmt5->bindValue(3, $telefone);
    $stmt5->bindValue(4, $assunto);
    $stmt5->bindValue(5, $mensagem);
    $stmt5->bindValue(6, $usuario_id);
    $stmt5->execute();

    $db->commit();

} catch (PDOException $e) {
    $db->rollback();
    echo "Erro ao tentar realizar a inscrição desejada:" . $e->getMessage();
    exit();
}
?>


