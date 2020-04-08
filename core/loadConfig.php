<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

function cardinalAutoload($class) {
    if(stripos(ini_get('include_path'), $class)!==false && class_exists($class, false)) {
        return false;
    }
    $class = str_replace("\\", DIRECTORY_SEPARATOR, $class);
    if(file_exists(PATH_MODULES.$class.".".ROOT_EX)) {
        include_once(PATH_MODULES.$class.".".ROOT_EX);
    } elseif(file_exists(PATH_AUTOLOADS.$class.".".ROOT_EX)) {
        include_once(PATH_AUTOLOADS.$class.".".ROOT_EX);
    } elseif(file_exists(PATH_CLASS.$class.".".ROOT_EX)) {
        include_once(PATH_CLASS.$class.".".ROOT_EX);
    } elseif(file_exists(PATH_SYSTEM.$class.".".ROOT_EX)) {
        include_once(PATH_SYSTEM.$class.".".ROOT_EX);
    } elseif(file_exists(PATH_DB_DRIVERS.$class.".".ROOT_EX)) {
        include_once(PATH_DB_DRIVERS.$class.".".ROOT_EX);
    }
}
if(version_compare(PHP_VERSION, '5.1.2', '>=')) {
	include(dirname(__FILE__).DIRECTORY_SEPARATOR."register70.php");
} else {
	include(dirname(__FILE__).DIRECTORY_SEPARATOR."register53.php");
}

function addEvent() {
	$loader = debug_backtrace();
	cardinalEvent::loader($loader[0]);
	return call_user_func_array("cardinalEvent::addListener", func_get_args());
}
function addEventRef() {
	$loader = debug_backtrace();
	cardinalEvent::loader($loader[0]);
	return call_user_func_array("cardinalEvent::addListenerRef", func_get_args());
}
function removeEvent() {
	$loader = debug_backtrace();
	cardinalEvent::loader($loader[0]);
	return call_user_func_array("cardinalEvent::removeListener", func_get_args());
}
function removeEventRef() {
	$loader = debug_backtrace();
	cardinalEvent::loader($loader[0]);
	return call_user_func_array("cardinalEvent::removeListenerRef", func_get_args());
}
function execEvent() {
	$loader = debug_backtrace();
	cardinalEvent::loader($loader[0]);
	return call_user_func_array("cardinalEvent::execute", func_get_args());
}
function execEventRef($action, &$args1 = "", &$args2 = "", &$args3 = "", &$args4 = "", &$args5 = "", &$args6 = "", &$args7 = "", &$args8 = "", &$args9 = "", &$args10 = "") {
	$loader = debug_backtrace();
	cardinalEvent::loader($loader[0]);
	return call_user_func_array("cardinalEvent::executeRef", array($action, &$args1, &$args2, &$args3, &$args4, &$args5, &$args6, &$args7, &$args8, &$args9, &$args10));
}
function didEvent() {
	$loader = debug_backtrace();
	cardinalEvent::loader($loader[0]);
	return call_user_func_array("cardinalEvent::did", func_get_args());
}
function existsEvent() {
	$loader = debug_backtrace();
	cardinalEvent::loader($loader[0]);
	return call_user_func_array("cardinalEvent::exists", func_get_args());
}

function errorHeader() {
	if(!isset($_SERVER['HTTP_CF_VISITOR'])) {
		header("HTTP/1.0 503 Service Temporarily Unavailable");
		header('Status: 503 Service Temporarily Unavailable');
	} else {
		header("HTTP/1.0 404 Not found");
		header('Status: 404 Not found');
	}
}


$host = (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "online-killer.pp.ua");
HTTP::$protocol = $protocol = "http";
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
	HTTP::$protocol = $protocol = "https";
}
$hostMD5 = substr(md5($host), 0, 6);
if(isset($_SERVER['SCRIPT_NAME'])) {
	$link = str_replace(array("index.".ROOT_EX, "install.".ROOT_EX, ADMINCP_DIRECTORY."/"), "", $_SERVER['SCRIPT_NAME']);
	$link = str_replace(DS, "/", $link);
	$link = explode("/", $link);
	array_pop($link);
	$link = implode("/", $link)."/";
} else {
	$link = "/";
}

$config = array(
	"charset" => "utf-8",
);
$config = execEvent("before_load_config", $config);

if(file_exists(PATH_MEDIA."config.client.".ROOT_EX)) {
	if(!is_writable(PATH_MEDIA."config.client.".ROOT_EX)) {
		@chmod(PATH_MEDIA."config.client.".ROOT_EX, 0664);
	}
	require_once(PATH_MEDIA."config.client.".ROOT_EX);
}
if(file_exists(PATH_MEDIA."config.global.".ROOT_EX)) {
	require_once(PATH_MEDIA."config.global.".ROOT_EX);
}



if(!defined("COOK_USER")) {
	define("COOK_USER", "username_".$hostMD5);
}
if(!defined("COOK_PASS")) {
	define("COOK_PASS", "password_".$hostMD5);
}
if(!defined("COOK_ADMIN_USER")) {
	define("COOK_ADMIN_USER", "admin_username_".$hostMD5);
}
if(!defined("COOK_ADMIN_PASS")) {
	define("COOK_ADMIN_PASS", "admin_password_".$hostMD5);
}
if(defined("VERSION") && !defined("START_VERSION")) {
	define("START_VERSION", VERSION);
}
if(!defined("SUPPORT_WEBP")) {
	define("SUPPORT_WEBP", BrowserSupport::webp());
}
if(!defined("SUPPORT_JP2")) {
	define("SUPPORT_JP2", BrowserSupport::jp2());
}
if(!defined("SUPPORT_JXR")) {
	define("SUPPORT_JXR", BrowserSupport::jxr());
}
if(!defined("SUPPORT_GZIP")) {
	define("SUPPORT_GZIP", BrowserSupport::gzip());
}



$config = array_merge($config, array(
	"default_http_local" => $link,
	"default_http_hostname" => $host,
	"default_http_host" => $protocol."://".$host.$link,
));
unset($link);

if(file_exists(PATH_MEDIA."config.".ROOT_EX)) {
	require_once(PATH_MEDIA."config.".ROOT_EX);
}
if(file_exists(PATH_MEDIA."config.install.".ROOT_EX)) {
	require_once(PATH_MEDIA."config.install.".ROOT_EX);
}
if(file_exists(PATH_MEDIA."config.langSettings.".ROOT_EX)) {
	include_once(PATH_MEDIA."config.langSettings.".ROOT_EX);
}
if(file_exists(PATH_MEDIA."config.".str_replace("www.", "", $host).".".ROOT_EX)) {
	require_once(PATH_MEDIA."config.".str_replace("www.", "", $host).".".ROOT_EX);
}
if(file_exists(PATH_MEDIA."db.".ROOT_EX)) {
	require_once(PATH_MEDIA."db.".ROOT_EX);
}
if(file_exists(PATH_MEDIA."db.".str_replace("www.", "", $host).".".ROOT_EX)) {
	require_once(PATH_MEDIA."db.".str_replace("www.", "", $host).".".ROOT_EX);
}
if(file_exists(PATH_MEDIA."config.init.".ROOT_EX)) {
	include_once(PATH_MEDIA."config.init.".ROOT_EX);
}
if(file_exists(PATH_MEDIA."config.settings.".ROOT_EX)) {
	include_once(PATH_MEDIA."config.settings.".ROOT_EX);
}
if(defined("DEBUG_ACTIVATED") && file_exists(PATH_MEDIA."config.dev.".ROOT_EX)) {
	include_once(PATH_MEDIA."config.dev.".ROOT_EX);
}
$config = execEvent("after_load_config", $config);

if(file_exists(ROOT_PATH.ADMINCP_DIRECTORY.DS."paths.".ROOT_EX)) {
	include_once(ROOT_PATH.ADMINCP_DIRECTORY.DS."paths.".ROOT_EX);
} else if(file_exists(ROOT_PATH.ADMINCP_DIRECTORY.DS."paths.default.".ROOT_EX)) {
	include_once(ROOT_PATH.ADMINCP_DIRECTORY.DS."paths.default.".ROOT_EX);
}

if(!defined("PREFIX_DB")) {
	$file = "cardinal_";
	$file = execEvent("set_prefix_db", $file);
	define("PREFIX_DB", $file);
}
$api_key = cardinal::GenApiKey();
$api_key = execEvent("set_api_key", $api_key);
$config = array_merge($config, array(
	"api_key" => $api_key,
));

set_error_handler(array('cardinalError', 'handlePhpError'));
set_exception_handler(array('cardinalError', 'handleException'));
register_shutdown_function(array('cardinalError', 'handleFatalError'));