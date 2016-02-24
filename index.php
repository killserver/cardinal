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
define("IS_CORE", true);
include_once("core.php");
if(!defined("INSTALLER") && !userlevel::get("site")) {
	templates::error("{L_error_level_full}", "{L_error_level}");
}

$server = $_SERVER['QUERY_STRING'];
if(!empty($server)) {
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
if(!$is_file && empty($file)) {
	view_pages($page);
	if(class_exists($class)) {
		$page = new $class();
		if(!empty($method) && method_exists($page, $method)) {
			$page->$method();
		}
	}
} else {
	if(file_exists($file)) {
		require_once($file);
	} else {
		templates::error("{L_error_page}", "{L_error_routification}");
	}
}
unset($page, $class, $method, $file, $is_file);
if(defined("DEBUG")) {
	ini_set('display_errors',1);
	error_reporting(E_ALL);
}
$Timer = microtime()-$Timer;
if(defined("DEBUG_ACTIVATED")) {
	Error::Debug(null, true);
}
GzipOut(templates::$gzip, templates::$gzip_activ);
HTTP::echos();
unset($templates);
unset($db);

?>