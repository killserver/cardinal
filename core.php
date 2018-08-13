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
if(!defined("DEFINED_CLASSES")) {
	define("DEFINED_CLASSES", implode(",", get_declared_classes()));
}

if(!function_exists('microtime_float')) {
	function microtime_float() {
		list($usec, $sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
	}
}

@ini_set("max_execution_time", 0);
if(strpos(CLOSE_FUNCTION, "set_time_limit")===false) {
	set_time_limit(0);
}
if(version_compare(PHP_VERSION, '5.3', '<')) {
	@ini_set('zend.ze1_compatibility_mode', 0);
	@set_magic_quotes_runtime(0);
	@ini_set('magic_quotes_gpc', 0);
	@ini_set('magic_quotes_sybase', 0);
	@ini_set('magic_quotes_runtime', 0);
}
@ini_set('mbstring.func_overload', 4);
@ini_set('upload_max_filesize', '200M');
@ini_set('post_max_size', '200M');
@ini_set('max_file_uploads', '80');
@ini_set('scream.enabled', false);
@ini_set('gd.jpeg_ignore_warning', 1);

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
	"define" => array(), //used in templates, method "define"
	"lang" => array(), //in class lang
	"const" => array(), //is define for modules
	"params" => array(), //is use in call module and get/send parameters
	"dependency_modules" => array(), //dependency logic modules and need update his
	"gzip" => false,
	"session_destroy" => false,
	"jscss" => array(),
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

$targets = array('PHP_SELF', 'HTTP_USER_AGENT', 'HTTP_REFERER', 'QUERY_STRING', 'REQUEST_URI', 'PATH_INFO');
foreach($targets as $target) {
	if(isset($_SERVER[$target]) && substr($_SERVER[$target], -5)=="debug") {
		$_SERVER[$target] = substr($_SERVER[$target], 0, -5);
	}
}
if(defined("DEBUG") || isset($_GET['debug']) || isset($_COOKIE['cardinal_debug'])) {
	@ini_set('display_errors', 1);
	@ini_set('html_errors', true);
	@ini_set('error_reporting', E_ALL);
	error_reporting(E_ALL);
	if(!defined("DEBUG_ACTIVATED")) {
		define("DEBUG_ACTIVATED", true);
	}
	if(!defined("DEBUG_DB")) {
		define("DEBUG_DB", true);
	}
} else {
	error_reporting(E_ALL ^ E_WARNING ^ E_DEPRECATED ^ E_NOTICE);
	@ini_set('error_reporting', E_ALL ^ E_WARNING ^ E_DEPRECATED ^ E_NOTICE);
	@ini_set('display_errors', true);
	@ini_set('html_errors', false);
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
		define("DS_DB", DS);
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
if(empty($phpEx)) {
    $phpEx = "php";
}
if(!defined("ROOT_EX") && strpos($phpEx, '/') === false) {
	define("ROOT_EX", $phpEx);
}
if(file_exists(ROOT_PATH."core".DS."modules".DS)) {
	if(file_exists(ROOT_PATH."core".DS."modules".DS."loader.default.".ROOT_EX)) {
		$file = file_get_contents(ROOT_PATH."core".DS."modules".DS."loader.default.".ROOT_EX);
		$file = str_replace('"core\\', '"application".DS."', $file);
		$file = str_replace('"core/', '"application".DS."', $file);
		$file = str_replace('"core".DS', '"application".DS', $file);
		file_put_contents(ROOT_PATH."core".DS."modules".DS."loader.default.".ROOT_EX, $file);
	}
	if(file_exists(ROOT_PATH."core".DS."modules".DS."loader.".ROOT_EX)) {
		$file = file_get_contents(ROOT_PATH."core".DS."modules".DS."loader.".ROOT_EX);
		$file = str_replace('"core\\', '"application".DS."', $file);
		$file = str_replace('"core/', '"application".DS."', $file);
		$file = str_replace('"core".DS', '"application".DS', $file);
		file_put_contents(ROOT_PATH."core".DS."modules".DS."loader.".ROOT_EX, $file);
	}
	if(!file_exists(ROOT_PATH."core".DS."cache".DS."system".DS."application.lock")) {
		function rrmdirModules($dir) {
			if(is_dir($dir)) {
				$files = @scandir($dir);
				foreach($files as $file) {
					if($file != "." && $file != "..") {
						if(is_file($dir.DS.$file)) {
							@unlink($dir.DS.$file);
						} else if(is_dir($dir.DS.$file.DS)) {
							rrmdirModules($dir.DS.$file.DS);
							@rmdir($dir.DS.$file.DS);
						}
					}
				}
				if($dir != "." && $dir != "..") {
					@unlink($dir);
				}
			}
		}
		function rcopyModules($src, $dst) {
			if(is_dir($src)) {
				@mkdir($dst, 0777);
				$files = @scandir($src);
				foreach($files as $file) {
					if($file != "." && $file != "..") {
						rcopyModules($src.DS.$file, $dst.DS.$file);
					}
				}
			} else if(file_exists($src)) {
				@copy($src, $dst);
			}
		}
		rcopyModules(ROOT_PATH."core".DS."modules", ROOT_PATH."application");
		rrmdirModules(ROOT_PATH."core".DS."modules");
		@mkdir(ROOT_PATH."core".DS."modules");
		if(!file_exists(ROOT_PATH."application".DS."modules".DS)) {
			if(!file_exists(ROOT_PATH."application".DS."modules".DS)) {
				@mkdir(ROOT_PATH."application".DS."modules".DS, 0777, true);
			}
			$files = @scandir(ROOT_PATH."application".DS);
			foreach($files as $file) {
				if(is_file(ROOT_PATH."application".DS.$file)) {
					@copy(ROOT_PATH."application".DS.$file, ROOT_PATH."application".DS."modules".DS.$file);
					@unlink(ROOT_PATH."application".DS.$file);
				}
			}
		}
		@file_put_contents(ROOT_PATH."core".DS."cache".DS."system".DS."application.lock", "");
	}
	if(!defined("PATH_MODULES")) {
		define("PATH_MODULES", ROOT_PATH."application".DS."modules".DS);
	}
	if(!defined("PATH_AUTOLOADS")) {
		define("PATH_AUTOLOADS", ROOT_PATH."application".DS."autoload".DS);
	}
	if(!defined("PATH_HOOKS")) {
		define("PATH_HOOKS", ROOT_PATH."application".DS."hooks".DS);
	}
	if(!defined("PATH_LOAD_LIBRARY")) {
		define("PATH_LOAD_LIBRARY", ROOT_PATH."application".DS."library".DS);
	}
	if(!defined("PATH_MODELS")) {
		define("PATH_MODELS", ROOT_PATH."application".DS."models".DS);
	}
	if(!defined("PATH_CRON_FILES")) {
		define("PATH_CRON_FILES", ROOT_PATH."application".DS."cron".DS);
	}
}
if(file_exists(ROOT_PATH."core".DS."paths.".ROOT_EX)) {
	include_once(ROOT_PATH."core".DS."paths.".ROOT_EX);
} else if(file_exists(ROOT_PATH."core".DS."paths.default.".ROOT_EX)) {
	include_once(ROOT_PATH."core".DS."paths.default.".ROOT_EX);
}

if(file_exists(PATH_MEDIA."definition.php")) {
	include_once(PATH_MEDIA."definition.php");
}

if(!defined("DOING_AJAX") && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
	define("DOING_AJAX", true);
} elseif(!defined("DOING_AJAX") && getenv('HTTP_X_REQUESTED_WITH') && getenv('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest') {
	define("DOING_AJAX", true);
}

if(!defined("DEVELOPER_MODE") && file_exists(PATH_MEDIA."develop.lock")) {
	define("DEVELOPER_MODE", true);
	define("ERROR_VIEW", true);
	if(!defined("DEBUG_DB")) {
		define("DEBUG_DB", true);
	}
}
if(!defined("WITHOUT_DB") && file_exists(PATH_MEDIA."isFrame.lock")) {
	define("WITHOUT_DB", true);
}
if(!defined("CHECK_MOD_ADMIN") && file_exists(PATH_MEDIA."oldPrinciple.lock")) {
	define("CHECK_MOD_ADMIN", false);
}
if(!defined("ENABLED_SUPPORTS") && file_exists(PATH_MEDIA."enabledSupports.lock")) {
	define("ENABLED_SUPPORTS", true);
}
if(!defined("ERROR_VIEW") && file_exists(PATH_MEDIA."error.lock")) {
	define("ERROR_VIEW", true);
}
if(!defined("PERMISSION_PHP") && file_exists(PATH_MEDIA."phpINtmp.lock")) {
	define("PERMISSION_PHP", true);
}

$Timer = microtime_float();

if(!defined("SYSTEM_TIME_START")) {
	define("SYSTEM_TIME_START", time());
}
if(!defined("SYSTEM_TIME_START_FLOAT")) {
	define("SYSTEM_TIME_START_FLOAT", microtime_float());
}

require_once(ROOT_PATH."core".DS."functions.".ROOT_EX);

execEvent("init_core");

HTTP::setSaveMime(PATH_CACHE_SYSTEM."mimeList.json");
Validate::$host = config::Select("default_http_host");

if(!isset($lang) || !is_Array($lang)) {
	$lang = array();
}
$host = (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "online-killer.pp.ua");
if(file_exists(PATH_MEDIA."db.".ROOT_EX) || file_exists(PATH_MEDIA."db.".$host.".".ROOT_EX)) {
	$db = new db();
	$db = execEvent("init_db", $db);
}
$cache = new cache();
$cache = execEvent("init_cache", $cache);
$cnf = new config();
$cnf->init();
$config = $cnf->all();
unset($cnf);
$config = execEvent("init_config", $config);
$langInit = new lang();
$lang = $langInit->init_lang();
$lang = execEvent("init_lang", $lang);
$GLOBALS['_1225392420_']=Array(base64_decode('ZGVmaW5' .'lZA' .'=='),base64_decode('ZGVmaW5l'),base64_decode('aXNfd3JpdG' .'FibGU='),base64_decode('Zml' .'sZ' .'V9leG' .'lzdHM='),base64_decode('Z' .'m' .'ls' .'ZW' .'10aW1l'),base64_decode('dGltZQ=='),base64_decode('Z' .'GVm' .'aW' .'5l')); function _925531152($i){$a=Array('V0lUSE9VVF9EQg==','Q1JPTl9USU1F','Y2FyZGluYWxfdGltZQ==','Y3Jvbi50eHQ=','Y3Jvbi50eHQ=','Q1JPTl9USU1F');return base64_decode($a[$i]);} if(!$GLOBALS['_1225392420_'][0](_925531152(0))){$GLOBALS['_1225392420_'][1](_925531152(1),config::Select(_925531152(2)));new cardinal();}elseif($GLOBALS['_1225392420_'][2](PATH_CACHE)){if($GLOBALS['_1225392420_'][3](PATH_CACHE ._925531152(3))){$_0=$GLOBALS['_1225392420_'][4](PATH_CACHE ._925531152(4));}else{$_0=$GLOBALS['_1225392420_'][5]();}$GLOBALS['_1225392420_'][6](_925531152(5),$_0);new cardinal();}
$config['charset'] = execEvent("set_charset", $config['charset']);
if(function_exists("mb_internal_encoding") && mb_internal_encoding($config['charset'])) {
	mb_internal_encoding($config['charset']);
}
if(function_exists("mb_regex_encoding") && mb_regex_encoding($config['charset'])) {
	mb_regex_encoding($config['charset']);
}
if(function_exists("mb_http_output") && mb_http_output($config['charset'])) {
	mb_http_output($config['charset']);
}
if(function_exists("mb_http_input") && mb_http_input($config['charset'])) {
	mb_http_input($config['charset']);
}
if(strpos($config['charset'], "UTF")!==false && function_exists('mb_language')) {
	mb_language('uni');
}
if(PHP_VERSION_ID>50600) {
	ini_set("default_charset", $config['charset']);
} else {
	ini_set("iconv.internal_encoding", $config['charset']);
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

$sessionOnline = false;
$session = false;
cardinal::StartSession();
execEvent("init_session");
if(isset($sessionOnline) && is_writeable(PATH_CACHE_USERDATA)) {
	clearstatcache();
	$SessionDir = session_save_path();
	$Timeout = 3 * 60;
	$Timeout = execEvent("timeout_session_online", $Timeout);
	$usersOnline = 0;
	$online = 0;
	if($Handler = @scandir($SessionDir)) {
		for($i=0;$i<sizeof($Handler);$i++) {
			if($Handler[$i]=="index.html"||$Handler[$i]=="index.".ROOT_EX) {
				continue;
			}
			if(time()-@filemtime($SessionDir.$Handler[$i])<$Timeout) {
				$usersOnline++;
			}
		}
	}
	$usersOnline = execEvent("get_session_online", $usersOnline);
	if(file_exists(PATH_CACHE_USERDATA."userOnline.txt")) {
		$online = file_get_contents(PATH_CACHE_USERDATA."userOnline.txt");
	}
	$online = max($online, $usersOnline);
	$online = execEvent("max_session_online", $online);
	@file_put_contents(PATH_CACHE_USERDATA."userOnline.txt", $online);
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
		header("Location: ".HTTP::$protocol."://".$config['default_http_hostname'].$_SERVER['REQUEST_URI']);
	die();
	}
	User::PathUsers(PATH_CACHE_USERDATA);
	User::load();
}

$templates = new templates($config_templates);
templates::SetConfig($config_templates);
$templates = execEvent("init_templates", $templates);
header('Content-Type: text/html; charset='.config::Select('charset'));
header('X-UA-Compatible: IE=edge');
header("X-Content-Type-Options: SAMEORIGIN");
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");
header('Cache-Control: max-age');
header("Cardinal: ".cardinal::SaveCardinal(VERSION));
@ini_set("pcre.backtrack_limit", 120000000);
@ini_set("pcre.recursion_limit", 120000000);
if(function_exists("header_remove")) {
	header_remove('x-powered-by');
} else {
	header('X-Powered-By:');
}
execEvent("set_headers");
function shutdownCardinal() {
	execEvent("shutdownCardinal");
}
if(defined("DEBUG_ACTIVATED")) {
	Debug::activation(720, true);
}
addEvent("shutdownCardinal", "GzipOut", "", 999999999);
register_shutdown_function("shutdownCardinal");
execEvent("core_ready");
?>