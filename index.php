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
if(strpos($_SERVER['PATH_INFO'], "/favicon.ico")!==false || strpos($_SERVER['REQUEST_URI'], "/favicon.ico")!==false) {
	header("HTTP/2.0 404 Not found");
	die();
}

define("IS_CORE", true);
include_once("core.php");

if(!defined("INSTALLER") && !userlevel::get("site")) {
	templates::error("{L_error_level_full}", "{L_error_level}");
}
if(is_array($config) && sizeof($config)>0 && isset($config["default_http_local"]) && isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI']=="/index.php") {
	header("Location: ".$config["default_http_local"], TRUE, 301);
	exit();
}

if(isset($_GET['noShowAdmin'])) {
	unset($_GET['noShowAdmin']);
	if(strpos($_SERVER['QUERY_STRING'], "noShowAdmin")!==false) {
		$_SERVER['QUERY_STRING'] = htmlspecialchars_decode($_SERVER['QUERY_STRING']);
		$_SERVER['QUERY_STRING'] = preg_replace("#noShowAdmin=(.+?)(\&|)#", "", $_SERVER['QUERY_STRING']);
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
Route::Build(array(
	"route" => modules::manifest_get('route'),
), 2);
Route::Config(array(
	"rewrite" => config::Select("rewrite"),
	"default_http_host" => config::Select("default_http_host"),
));
Route::Load($page);
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

$active = false;
$load = true;
$obj = "";
if(config::Select("activeCache")) {
    function cacheWalk(&$v, $k) { $v = ($k."-".$v); }
	$par = Route::param();
	array_walk($par, "cacheWalk");
	$url = implode("=", $par);
	$md5 = md5($url);
	$par = $_GET;
	array_walk($par, "cacheWalk");
	$url = implode("=", $par);
	$md5 = md5($md5.$url);
	if(!file_exists(PATH_CACHE_PAGE.$md5.".txt")) {
		$active = true;
	} else {
		$load = false;
	}
}
$langPanel = modules::setLangPanel();
if($load) {
	if(!$is_file && empty($file)) {
		if($class == "page") {
			view_pages($page);
		}
		if(class_exists($class)) {
			$page = new $class();
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
} else {
	include(PATH_CACHE_PAGE.$md5.".txt");
}
if($active) {
	$obj = ob_get_contents();
	ob_end_clean();
	file_put_contents(PATH_CACHE_PAGE.$md5.".txt", removeBOM($obj));
	HTTP::echos($obj);
}
unset($page, $class, $method, $file, $is_file);
$time = microtime();
if(strpos($time, " ")!==false) {
	$time = explode(" ", $time);
	$time = current($time);
}
$Timer = $time-$Timer;
$list = array("targets","session","manifest","Timer","config","target","phpEx","protocol","route","cache","lang","user","config_templates","server","pages","active","load","obj","templates","db");
for($i=0;$i<sizeof($list);$i++) {
	unset($GLOBALS[$list[$i]]);
}
HTTP::echos();
?>