<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die;
}

ini_set("max_execution_time", 0);
set_time_limit(0);

if(function_exists("ob_start")) {
	ob_start("ob_gzhandler");
}
if(function_exists("ob_implicit_flush")) {
	ob_implicit_flush(0);
}

session_start();

if(defined("DEBUG") || isset($_GET['debug'])) {
	ini_set('display_errors',1);
	error_reporting(E_ALL);
}

define("MEMORY_GET", memory_get_usage(true));
define("ROOT_PATH", dirname(__FILE__)."/");
$phpEx = substr(strrchr(__FILE__, '.'), 1);
if(strpos($phpEx, '/') === false)
define("ROOT_EX", $phpEx);
$Timer = microtime();


$manifest = array(
	"mod_page" => array(), //in class templates
	"create_js" => array("full" => array(), "mini" => array()), //in functions/templates.php create_js
	"functions" => array(), //in functions
	"pages" => array(), //in page view
	"class_pages" => array(), //in page view
	"lang" => array(), //in class lang
	"bbcodes" => array(), //in colorit
	"cbbcode" => array(), //in clear_bbcode
	"const" => array(), //is define for modules
	"params" => array(), //is use in call module and get/send parameters
);

require_once(ROOT_PATH."core/functions.".ROOT_EX);

$lang = array();
$db = new db();
$cache = new cache();
$cnf = new config();
$config = $cnf->all();
unset($cnf);
$langs = new lang();
$lang = $langs->init_lang();
unset($langs);
new cardinal();


$user = array();
if(!isset($_COOKIE['username']) or empty($_COOKIE['username'])) {
	$user['level'] = 0;
} else {
	$username = saves($_COOKIE['username']);
	$cache->delete("user_".$username);
	if(!$cache->exists("user_".$username)) {
		$row = db::doquery("SELECT * FROM users WHERE username = \"".$username."\" AND pass = \"".saves($_COOKIE['pass'])."\"");
		$cache->set("user_".$username, $row);
		$user = $row;
		db::doquery("UPDATE users SET last_activ = UNIX_TIMESTAMP(), last_ip = \"".getenv("REMOTE_ADDR")."\" WHERE id = ".$user['id']);
	} else {
		$user = $cache->get("user_".$username);
	}
}

$templates = new templates();

header('Content-Type: text/html; charset='.$config['charset']);
header('X-UA-Compatible: IE=edge,chrome=1');
header('Cache-Control: max-age');
header_remove('x-powered-by');

?>