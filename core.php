<?php
/*
 *
 * @version 3.0
 * @copyright 2014-2016 KilleR for Cardinal Engine
 * @author KilleR
 *
 * Version Engine: 3.0
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
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
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


if(!defined("MEMORY_GET")) {
	define("MEMORY_GET", memory_get_usage());
}
if(!defined("DS")) {
	define("DS", DIRECTORY_SEPARATOR);
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

$lang = array();
$db = new db();
$cache = new cache();
$cnf = new config();
$cnf->init();
$config = $cnf->all();
unset($cnf);
if(isset($config['db_version'])) {
	updater::update(DB_VERSION, $config['db_version']);
} else {
	updater::update(DB_VERSION, "");
}
$langs = new lang();
$lang = $langs->init_lang();
unset($langs);
defines::add("CRON_TIME", config::Select("cardinal_time"));
defines::init();
new cardinal();
if(function_exists("mb_internal_encoding") && mb_internal_encoding($config['charset'])) {
	mb_internal_encoding($config['charset']);
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

if(!defined("INSTALLER")) {
	if(!defined("IS_CRON_FILE") && isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], $config['default_http_hostname'])===false) {
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: http://".$config['default_http_hostname'].$_SERVER['REQUEST_URI']);
	die();
	}

	$user = array();
	if((isset($_COOKIE[COOK_USER]) and !empty($_COOKIE[COOK_USER])) && ((isset($_COOKIE[COOK_PASS]) and !empty($_COOKIE[COOK_PASS])) || (isset($_COOKIE[COOK_ADMIN_PASS]) and !empty($_COOKIE[COOK_ADMIN_PASS])))) {
		if(isset($_COOKIE[COOK_ADMIN_USER]) && defined("IS_ADMIN")) {
			$username = saves($_COOKIE[COOK_ADMIN_USER]);
		} else {
			$username = saves($_COOKIE[COOK_USER]);
		}
		if(isset($_COOKIE[COOK_ADMIN_PASS]) && defined("IS_ADMIN")) {
			$where = "`admin_pass`";
			$password = saves($_COOKIE[COOK_ADMIN_PASS]);
		} else {
			$where = "`pass`";
			$password = saves($_COOKIE[COOK_PASS]);
		}
		if(!cache::Exists("user_".$username)) {
			db::doquery("SELECT * FROM users WHERE `username` = \"".$username."\" AND ".$where." = \"".$password."\"", true);
			if(db::num_rows()==0) {
				cache::Delete("user_".$username);
				HTTP::set_cookie(COOK_USER, null, true);
				HTTP::set_cookie(COOK_PASS, null, true);
			} else {
				$user = db::fetch_array();
				cache::Set("user_".$username, $user);
				db::doquery("UPDATE `users` SET `last_activ` = UNIX_TIMESTAMP(), `last_ip` = \"".HTTP::getip()."\" WHERE `id` = ".$user['id']);
				define("IS_AUTH", true);
			}
		} else {
			$password = $admin_password = "";
			if(isset($_COOKIE[COOK_PASS])) {
				$password = saves($_COOKIE[COOK_PASS]);
			}
			if(isset($_COOKIE[COOK_ADMIN_PASS])) {
				$admin_password = saves($_COOKIE[COOK_ADMIN_PASS]);
			}
			$user = cache::Get("user_".$username);
			if($user['pass']!=$password && $user['admin_pass']!=$admin_password) {
				cache::Delete("user_".$username);
				HTTP::set_cookie(COOK_USER, null, true);
				HTTP::set_cookie(COOK_PASS, null, true);
			}
		}
	} else {
		$user['level'] = 0;
	}
}

$templates = new templates($config_templates);
templates::SetConfig($config_templates);
header('Content-Type: text/html; charset='.config::Select('charset'));
header('X-UA-Compatible: IE=edge,chrome=1');
header('Cache-Control: max-age');
header("Cardinal: ".cardinal::SaveCardinal(VERSION));
header_remove('x-powered-by');
?>