<?php
/*
 *
 * @version 5.4
 * @copyright 2014-2016 KilleR for Cardinal Engine
 * @author KilleR
 *
 * Version Engine: 5.4
 * Version File: 12
 *
 * 12.1
 * add support initialize config before include page
 * 12.2
 * add support correct utf-8 text
 * 12.3
 * add support before install default timezone
 * 12.4
 * add support installer cookie
 * 12.5
 * add admin cookie
 * 12.6
 * add re-include core
 * 12.7
 * rebuild logic cookie
 * 12.8
 * add route to manifest
 * 12.9
 * add support config as static class, rebuild get info memory for usage, add support special slash on Windows OS and rebuild logic auth
 * 12.10
 * add actually code
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}
/* fix for PHP float bug: http://bugs.php.net/bug.php?id=53632 (php 4 <= 4.4.9 and php 5 <= 5.3.4) */
if(strstr(str_replace('.', '', serialize(array_merge($_GET, $_POST, $_COOKIE))), '22250738585072011')) {
    header('Status: 422 Unprocessable Entity');
    die();
}
if(isset($_SERVER['QUERY_STRING']) && strpos(urldecode($_SERVER['QUERY_STRING']), chr(0)) !== false) {
	die();
}
if(function_exists("ini_get") && ini_get('register_globals') && isset($_REQUEST)) {
	foreach($_REQUEST as $key => $value) {
		$GLOBALS[$key] = "";
		unset($GLOBALS[$key]);
	}
}
$targets = array('PHP_SELF', 'HTTP_USER_AGENT', 'HTTP_REFERER', 'QUERY_STRING');
foreach($targets as $target) {
	$_SERVER[$target] = isset($_SERVER[$target]) ? htmlspecialchars($_SERVER[$target], ENT_QUOTES) : "";
}

if(!defined("CLOSE_FUNCTION")) {
	define("CLOSE_FUNCTION", ini_get("disable_functions"));
}

ini_set("max_execution_time", 0);
if(strpos(CLOSE_FUNCTION, "set_time_limit")===false) {
	set_time_limit(0);
}

$manifest = array(
	"before_ini_class" => array(), //configuration pages and modules before load
	"after_ini_class" => array(), //configuration pages and modules after load
	"mod_page" => array(), //in class templates
	"load_modules" => array(), //write modules loading in this page
	"log" => array(), //write modules loading in this page
	"user_pages" => array(), //modules user page
	"create_js" => array("full" => array(), "mini" => array(), "min" => array()), //in functions/templates.php create_js
	"functions" => array(), //in functions
	"pages" => array(), //in page view
	"class_pages" => array(), //in page view
	"route" => array(), //routification
	"define" => array(), //in class defines
	"lang" => array(), //in class lang
	"bbcodes" => array(), //in colorit
	"cbbcode" => array(), //in clear_bbcode
	"const" => array(), //is define for modules
	"params" => array(), //is use in call module and get/send parameters
	"dependency_modules" => array(), //dependency logic modules and need update his
	"applyParam" => array(),
	"gzip" => false,
	"session_destroy" => true,
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

//define("NOT_DIE", true);

if(defined("DEBUG") || isset($_GET['debug'])) {
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
	if(!defined("DEBUG_ACTIVATED")) {
		define("DEBUG_ACTIVATED", true);
	}
}

if(!defined("PHP_INT_MIN")) {
	define("PHP_INT_MIN", -2147483647);
}
if(!defined("PHP_INT_MAX")) {
	define("PHP_INT_MAX", 2147483647);
}
if(!defined("PHP_FLOAT_MIN")) {
	define("PHP_FLOAT_MIN", 3.4e-38);
}
if(!defined("PHP_FLOAT_MAX")) {
	define("PHP_FLOAT_MAX", 3.4e+38);
}

if(!defined("DS")) {
	define("DS", DIRECTORY_SEPARATOR);
}
if(file_exists(dirname(__FILE__).DS."core".DS."media".DS."definition.php")) {
	include_once(dirname(__FILE__).DS."core".DS."media".DS."definition.php");
}

if(!defined("WITHOUT_DB") && file_exists(dirname(__FILE__).DS."core".DS."media".DS."isFrame.lock")) {
	define("WITHOUT_DB", true);
}
if(!defined("ERROR_VIEW") && file_exists(dirname(__FILE__).DS."core".DS."media".DS."error.lock")) {
	define("ERROR_VIEW", true);
}
if(!defined("PERMISSION_PHP") && file_exists(dirname(__FILE__).DS."core".DS."media".DS."phpINtmp.lock")) {
	define("PERMISSION_PHP", true);
}

if(!defined("MEMORY_GET")) {
	define("MEMORY_GET", memory_get_usage());
}
if(!defined("ROOT_PATH")) {
	define("ROOT_PATH", dirname(__FILE__).DS);
}
$phpEx = substr(strrchr(__FILE__, '.'), 1);
if(!defined("ROOT_EX") && strpos($phpEx, '/') === false) {
	define("ROOT_EX", $phpEx);
}
$Timer = microtime();

require_once(ROOT_PATH."core".DS."functions.".ROOT_EX);

HTTP::setSaveMime(ROOT_PATH."core".DS."cache".DS."system".DS."mimeList.json");
Validate::$host = config::Select("default_http_host");

$lang = array();
if(file_exists(ROOT_PATH."core".DS."media".DS."db.".ROOT_EX)) {
	$db = new db();
}
$cache = new cache();
$cnf = new config();
$cnf->init();
$config = $cnf->all();
unset($cnf);
if(!defined("WITHOUT_DB")) {
	if(isset($config['db_version'])) {
		updater::update(DB_VERSION, $config['db_version']);
	} else {
		updater::update(DB_VERSION, "");
	}
}
$langInit = new lang();
$lang = $langInit->init_lang();
if(!defined("WITHOUT_DB")) {
	defines::add("CRON_TIME", config::Select("cardinal_time"));
	defines::init();
	new cardinal();
} else {
	defines::init();
}
if(function_exists("mb_internal_encoding") && mb_internal_encoding($config['charset'])) {
	mb_internal_encoding($config['charset']);
}
if(function_exists("mb_regex_encoding") && mb_regex_encoding($config['charset'])) {
	mb_regex_encoding($config['charset']);
}
if(isset($config['date_timezone'])) {
	date_default_timezone_set($config['date_timezone']);
}
$config_templates = array(
	"gzip_output" => modules::get_config('gzip_output'),
	"skins_skins" => (!modules::get_config('skins', 'skins') ? "main" : modules::get_config('skins', 'skins')),
	"skins_test_shab" => (HTTP::CheckIp(modules::get_config('ip_test_shab')) ? modules::get_config('skins', 'test_shab') : ""),
	"skins_mobile" => modules::get_config('skins', 'mobile'),
);

cardinal::InstallFirst();

if(defined("WITHOUT_DB") || !defined("INSTALLER")) {
	if(!defined("WITHOUT_DB") && !defined("IS_CRON_FILE") && isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], $config['default_http_hostname'])===false) {
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: http://".$config['default_http_hostname'].$_SERVER['REQUEST_URI']);
	die();
	}
	User::PathUsers(ROOT_PATH."core".DS."cache".DS."system".DS);
	User::load();
}

$templates = new templates($config_templates);
templates::SetConfig($config_templates);
header('Content-Type: text/html; charset='.config::Select('charset'));
header('X-UA-Compatible: IE=edge,chrome=1');
header("X-Content-Type-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");
header('Cache-Control: max-age');
header("Cardinal: ".cardinal::SaveCardinal(VERSION));
header_remove('x-powered-by');
?>