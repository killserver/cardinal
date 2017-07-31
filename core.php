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

ini_set('scream.enabled', false);

if(defined("DEBUG") || isset($_GET['debug'])) {
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
	if(!defined("DEBUG_ACTIVATED")) {
		define("DEBUG_ACTIVATED", true);
	}
}

if(!defined('PHP_VERSION_ID')) {
	$version = explode('.', PHP_VERSION);
	define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
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
if(!defined("DS_DB")) {
	if(strtoupper(substr(PHP_OS, 0, 3))==='WIN') {
		define("DS_DB", DS.DS);
	} else {
		define("DS_DB", DS);
	}
}
if(!defined("PHP_WIN")) {
	if(strtoupper(substr(PHP_OS, 0, 3))==='WIN') {
		define("PHP_WIN", true);
	} else {
		define("PHP_WIN", false);
	}
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
if(file_exists(ROOT_PATH."core".DS."paths.".ROOT_EX)) {
	include_once(ROOT_PATH."core".DS."paths.".ROOT_EX);
} else if(file_exists(ROOT_PATH."core".DS."paths.default.".ROOT_EX)) {
	include_once(ROOT_PATH."core".DS."paths.default.".ROOT_EX);
} 

if(file_exists(PATH_MEDIA."definition.php")) {
	include_once(PATH_MEDIA."definition.php");
}

if(!defined("DEVELOPER_MODE") && file_exists(PATH_MEDIA."develop.lock")) {
	define("DEVELOPER_MODE", true);
	define("ERROR_VIEW", true);
}
if(!defined("WITHOUT_DB") && file_exists(PATH_MEDIA."isFrame.lock")) {
	define("WITHOUT_DB", true);
}
if(!defined("ERROR_VIEW") && file_exists(PATH_MEDIA."error.lock")) {
	define("ERROR_VIEW", true);
}
if(!defined("PERMISSION_PHP") && file_exists(PATH_MEDIA."phpINtmp.lock")) {
	define("PERMISSION_PHP", true);
}

$Timer = microtime(true);

require_once(ROOT_PATH."core".DS."functions.".ROOT_EX);

HTTP::setSaveMime(PATH_CACHE_SYSTEM."mimeList.json");
Validate::$host = config::Select("default_http_host");

$lang = array();
if(file_exists(PATH_MEDIA."db.".ROOT_EX)) {
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

HTTP::$protocol = "http";
if(
	   (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
	|| (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && !empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
	|| (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on')
	|| (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
	|| (isset($_SERVER['HTTP_X_FORWARDED_PORT']) && $_SERVER['HTTP_X_FORWARDED_PORT'] == 443)
	|| (isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https')
	|| (isset($_SERVER['CF_VISITOR']) && $_SERVER['CF_VISITOR'] == '{"scheme":"https"}')
	|| (isset($_SERVER['HTTP_CF_VISITOR']) && $_SERVER['HTTP_CF_VISITOR'] == '{"scheme":"https"}')
) {
	HTTP::$protocol = "https";
}




if(PHP_VERSION_ID>50600) {
	ini_set("default_charset", $config['charset']);
} else {
	ini_set("iconv.internal_encoding", $config['charset']);
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
	User::PathUsers(PATH_CACHE_SYSTEM);
	User::load();
}

$templates = new templates($config_templates);
templates::SetConfig($config_templates);
header('Content-Type: text/html; charset='.config::Select('charset'));
header('X-UA-Compatible: IE=edge');
header("X-Content-Type-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");
header('Cache-Control: max-age');
header("Cardinal: ".cardinal::SaveCardinal(VERSION));
ini_set("pcre.backtrack_limit", 1200000);
ini_set("pcre.recursion_limit", 1200000);
if(function_exists("header_remove")) {
	header_remove('x-powered-by');
} else {
	header('X-Powered-By:');
}
?>