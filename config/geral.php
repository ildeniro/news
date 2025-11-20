<?PHP
//------------------------------------------------------------------------------
//Arquivo principal de configurações do sistema
//------------------------------------------------------------------------------
//TIMEZONE
date_default_timezone_set('Brazil/Acre');
//------------------------------------------------------------------------------
//DEFINIÇÕES DE BANCO DE DADOS LOCAL
define("DB", 'mysql');
define("DB_HOST", "localhost");
define("DB_NAME", "news");
define("DB_USER", "root");
define("DB_PASS", "");
//------------------------------------------------------------------------------
//DEFINIÇÃO DE URLS LOCAL
define("PORTAL_URL", "http://localhost/news/");
define("FOLDER_IMG", "http://localhost/news/assets/img/");
define("FOLDER_VIEW", "http://localhost/news/view/");
define("FOLDER_DAO", "http://localhost/news/dao/");
define("FOLDER_SCRIPTS", "http://localhost/news/scripts/");
define("FOLDER_PLUGINS", "http://localhost/news/assets/plugins/");
define("FOLDER_ASSETS", "http://localhost/news/assets/");
define("FOLDER_CSS", "http://localhost/news/assets/css/");
define("FOLDER_JS", "http://localhost/news/assets/js/");
define("FOLDER_CONFIG", "http://localhost/news/config/");
define("PORTAL_URL_SERVER", $_SERVER["DOCUMENT_ROOT"] . "/news/");
//------------------------------------------------------------------------------
define("TITULO", "Portal de Notícias");
define("SUBTITULO", "Notícias do Acre");
define("TITULO_PORTAL", "Portal de Notícias");
//------------------------------------------------------------------------------
// ADICIONAR CLASSE DE CONEÇÃO
include_once("Conexao.class.php");
//------------------------------------------------------------------------------
?>
