<?php

ob_start();
session_start();

//ADICIONAR A CONEXAO E URL AMIGAVEL
include_once("config/Url.php");
include_once("config/geral.php");
include_once("config/session.php");
include_once("config/funcoes.php");

//INSTANCIA A CONEXAO
$db = Conexao::getInstance();

$modulo = Url::getURL(0);
$mvc = Url::getURL(1);
$arquivomodulo = Url::getURL(2);
$parametromodulo = Url::getURL(3);

if ($modulo == 'index.php' || $modulo == 'index' || $modulo == '' || $modulo == null) {
    $modulo = "home";
    include_once $modulo . ".php";
    sessionOn();
    exit();
} else {
    if ($arquivomodulo == 'index.php' || $arquivomodulo == 'index' || $arquivomodulo == '' || $arquivomodulo == null) {
        //VERIFICA SE O ARQUIVO EXISTE E EXIBI
        if (file_exists($modulo . '/' . $mvc . '/' . "index.php")) {
            include_once $modulo . '/' . $mvc . '/' . "index.php";
            sessionOn();
            exit();
        } else {
            include_once "404.php";
            sessionOn();
            exit();
        }
    } else {
        if ($arquivomodulo == '' || $arquivomodulo == null) {
            //VERIFICA SE O ARQUIVO EXISTE E EXIBI
            if (file_exists($modulo . '/' . $mvc . '/' . "index.php")) {
                include_once $modulo . '/' . $mvc . '/' . "index.php";
                sessionOn();
                exit();
            } else {
                include_once "404.php";
                sessionOn();
                exit();
            }
        } else {
            //VERIFICA SE O ARQUIVO EXISTE E EXIBI
            if (file_exists($modulo . '/' . $mvc . '/' . $arquivomodulo . ".php")) {
                include_once $modulo . '/' . $mvc . '/' . $arquivomodulo . ".php";
                sessionOn();
                exit();
            } else {
                include_once "404.php";
                sessionOn();
                exit();
            }
        }
    }//END IF
}//END IF
?>  