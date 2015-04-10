<?php
/*
*
* Version Engine: 1.25.3
* Version File: 12
*
* 12.1
* add support initialize config before include page
* 12.2
* add support correct utf-8 text
*
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}
define("CLOSE_FUNCTION", ini_get("disable_functions"));

ini_set("max_execution_time", 0);
if(strpos(CLOSE_FUNCTION, "set_time_limit")===false) {
	set_time_limit(0);
}

$manifest = array(
	"before_ini_class" => array(), //configuration pages and modules before load
	"after_ini_class" => array(), //configuration pages and modules after load
	"mod_page" => array(), //in class templates
	"load_modules" => array(), //write modules loading in this page
	"user_pages" => array(), //modules user page
	"create_js" => array("full" => array(), "mini" => array()), //in functions/templates.php create_js
	"functions" => array(), //in functions
	"pages" => array(), //in page view
	"class_pages" => array(), //in page view
	"define" => array(), //in class defines
	"lang" => array(), //in class lang
	"bbcodes" => array(), //in colorit
	"cbbcode" => array(), //in clear_bbcode
	"const" => array(), //is define for modules
	"params" => array(), //is use in call module and get/send parameters
	"gzip" => false,
);

if(function_exists("ob_start")) {
	if(ini_get("zlib.output_compression")!="1") {
		ob_start("ob_gzhandler");
	} else {
		ob_start();
	}
	$manifest['gzip'] = true;
}
if(function_exists("ob_implicit_flush")) {
	ob_implicit_flush(0);
	$manifest['gzip'] = true;
}

session_start();

//define("NOT_DIE", true);

if(defined("DEBUG") || isset($_GET['debug'])) {
	ini_set('display_errors',1);
	error_reporting(E_ALL);
	define("DEBUG_ACTIVATED", true);
}


define("MEMORY_GET", memory_get_usage(true));
if(!defined("ROOT_PATH")) {
	define("ROOT_PATH", dirname(__FILE__)."/");
}
$phpEx = substr(strrchr(__FILE__, '.'), 1);
if(strpos($phpEx, '/') === false)
define("ROOT_EX", $phpEx);
$Timer = microtime();

require_once(ROOT_PATH."core/functions.".ROOT_EX);

$lang = array();
$db = new db();
$cache = new cache();
$cnf = new config();
$cnf->init();
$config = $cnf->all();
unset($cnf);
$langs = new lang();
$lang = $langs->init_lang();
unset($langs);
defines::add("CRON_TIME", config::Select("cardinal_time"));
defines::init();
new cardinal();
if(mb_internal_encoding($config['charset'])) {
	mb_internal_encoding($config['charset']);
}
date_default_timezone_set($config['date_timezone']);

if(strpos($_SERVER['HTTP_HOST'], $config['default_http_hostname'])===false) {
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: http://".$config['default_http_hostname'].$_SERVER['REQUEST_URI']);
die();
}


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
		db::doquery("UPDATE users SET last_activ = UNIX_TIMESTAMP(), last_ip = \"".HTTP::getip()."\" WHERE id = ".$user['id']);
	} else {
		$user = $cache->get("user_".$username);
	}
	define("IS_AUTH", true);
}

$templates = new templates();

header('Content-Type: text/html; charset='.$config['charset']);
header('X-UA-Compatible: IE=edge,chrome=1');
header('Cache-Control: max-age');
header_remove('x-powered-by');

?>