<?php
/*
 *
 * @version 3.0
 * @copyright 2014-2016 KilleR for Cardinal Engine
 *
 * Version Engine: 3.0
 * Version File: 2
 *
 * 1.1
 * add logic routification
 * 1.2
 * rebuild logic routification
 * 1.3
 * add support file in routification
 * 2.1
 * rebuild logic routification and pages
 * 2.2
 * fix bugs on include files
 *
*/
if((isset($_SERVER['PATH_INFO']) && strpos($_SERVER['PATH_INFO'], "/favicon.ico")!==false) || (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], "/favicon.ico")!==false)) {
	header("HTTP/2.0 404 Not found");
	die();
}

define("IS_CORE", true);
include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."core.php");

if(!userlevel::get("site")) {
	templates::error("{L_error_level_full}", "{L_error_level}");
}

if(is_array($config) && sizeof($config)>0 && isset($config["default_http_local"]) && isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI']=="/index.php") {
	header("Location: ".$config["default_http_local"], TRUE, 301);
	exit();
}

if(isset($_GET['noShowAdmin'])) {
	define("IS_NOSHOWADMIN", true);
	unset($_GET['noShowAdmin']);
	$adminRemove = "#(.*?)(\?|)noShowAdmin.*?(\&|)(.*?)$#i";
	if(isset($_SERVER['QUERY_STRING']) && strpos($_SERVER['QUERY_STRING'], "noShowAdmin")!==false) {
		$_SERVER['QUERY_STRING'] = htmlspecialchars_decode($_SERVER['QUERY_STRING']);
		$_SERVER['QUERY_STRING'] = preg_replace($adminRemove, "$1$4", $_SERVER['QUERY_STRING']);
	}
	if(isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], "noShowAdmin")!==false) {
		$_SERVER['REQUEST_URI'] = htmlspecialchars_decode($_SERVER['REQUEST_URI']);
		$_SERVER['REQUEST_URI'] = preg_replace($adminRemove, "$1$4", $_SERVER['REQUEST_URI']);
	}
}

if(isset($_SERVER['QUERY_STRING'])) {
	$server = $_SERVER['QUERY_STRING'];
} else {
	$server = "";
}
if(!empty($server) || (defined("ROUTE_GET_URL") && isset($_SERVER[ROUTE_GET_URL]) && strlen($_SERVER[ROUTE_GET_URL])>1)) {
	$page = $server;
	if(strpos($page, "&") !== false) {
		$pages = explode("&", $page);
		if(empty($pages[0])) {
			$page = $pages[1];
		} else {
			$page = $pages[0];
		}
	}
	if(strpos($page, "=") !== false) {
		$pages = explode("=", $page);
		$page = $pages[0];
	}
} else {
	$page = "main";
}
if(config::Select("new_method_uri")) {
	Route::newMethod();
}
Route::Build(array(
	"route" => modules::manifest_get('route'),
), 2);
Route::Config(array(
	"rewrite" => config::Select("rewrite"),
	"default_http_host" => config::Select("default_http_host"),
));
Route::Load($page);
execEventRef("route_completed");
extract(Route::param());
$pages = Route::param('page');
if(!(!$pages)) {
	$page = $pages;
}
$classes = Route::param('class');
if(!(!$classes)) {
	$class = $classes;
} else {
	$class = "page";
}
$method = Route::param('method');
if(!(!$method)) {
	$method = $method;
} else {
	$method = '';
}
unset($classes);
$manifest['now_page'] = $page;
$manifest['mod_page'][HTTP::getip()]['page'] = $page;
$is_file = Route::param('is_file');
$file = Route::param('file');

execEvent("ready_print_page_before", $class, $method, $page);

$obj = "";
$langPanel = modules::setLangPanel();
if(isset($globalClass) && is_array($globalClass) && sizeof($globalClass)>0) {
	$globalClass = array_values($globalClass);
	for($i=0;$i<sizeof($globalClass);$i++) {
		$name = $globalClass[$i];
		if(class_exists($name, false)) {
			$reflect  = new ReflectionClass($name);
			$instance = $reflect->newInstanceArgs($langPanel);
			unset($reflect, $instance);
		}
	}
}
if(!$is_file && empty($file)) {
	if($class == "page") {
		$args = array();
		$args[] = $page;
		$args = array_merge($args, $langPanel);
		call_user_func_array("view_pages", $args);
	}
	if(is_object($class) || class_exists($class)) {
		$page = new $class($langPanel);
		if(!empty($method) && method_exists($page, $method)) {
			call_user_func_array(array(&$page, $method), $langPanel);
		}
	}
} else {
	if(file_exists($file)) {
		require_once($file);
	} else {
		templates::error("{L_error_page}", "{L_error_routification}");
	}
}

execEvent("ready_print_page_after", $class, $method, $page);

unset($page, $class, $method, $file, $is_file);
$list = array("targets","session","manifest","config","target","phpEx","protocol","route","cache","lang","user","config_templates","server","pages","active","load","obj","templates","db");
for($i=0;$i<sizeof($list);$i++) {
	unset($GLOBALS[$list[$i]]);
}
HTTP::echos();
?>