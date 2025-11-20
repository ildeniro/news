<?php
@session_start();
include_once('config/geral.php');
include_once('config/Url.php');

if (isset($_SESSION['id'])) {
    $sessao = $_SESSION['id'];
} else {
    $sessao = 0;
}

$idsessao = session_id();

$db = Conexao::getInstance();

$sair = $db->prepare("UPDATE seg_sessoes SET data_logout = NOW() WHERE usuario_id = ?");
$sair->bindValue(1, $sessao);
$sair->execute();

$atualizar = $db->prepare("UPDATE seg_usuarios SET online = 0 WHERE id = ?");
$atualizar->bindValue(1, $sessao);
$atualizar->execute();
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <title><?= TITULO ?></title>
        <!-- BEGIN META -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="keywords" content="your,keywords">
        <meta name="description" content="Short explanation about this website">
    </head>
    <body>
        <?php
        $_SESSION['id'] = "";
        $_SESSION['timeout'] = "";
        $_SESSION['nome'] = "";
        $_SESSION['telefone1'] = "";
        $_SESSION['email1'] = "";
        $_SESSION['login'] = "";
        $_SESSION['foto'] = "";
        $_SESSION['online'] = "";
        $_SESSION['foto_cut'] = "";
        $_SESSION['foto_origin'] = "";
        
        unset($_SESSION['id']);
        unset($_SESSION['timeout']);
        unset($_SESSION['nome']);
        unset($_SESSION['telefone1']);
        unset($_SESSION['email1']);
        unset($_SESSION['login']);
        unset($_SESSION['foto']);
        unset($_SESSION['online']);
        unset($_SESSION['foto_cut']);
        unset($_SESSION['foto_origin']);
        echo "<script 'text/javascript'>window.location = '" . PORTAL_URL . "admin';</script>";
        ?>
    </body>
</html>