<?php
/*
 *
 * @version 1.25.6-rc5
 * @copyright 2014-2015 KilleR for Cardinal Engine
 *
 * Version Engine: 1.25.6-rc5
 * Version File: 1
 *
 * 1.1
 * add logic routification
 *
*/
define("IS_CORE", true);
include_once("core.php");

if(!userlevel::get("site")) {
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
$page = Route::Get($page);


$manifest['now_page'] = $page;
$manifest['mod_page'][HTTP::getip()]['page'] = $page;
view_pages($page);
if(class_exists("page")) {
	$page = new page();
	unset($page);
}
if(defined("DEBUG")) {
	ini_set('display_errors',1);
	error_reporting(E_ALL);
}

$Timer = microtime()-$Timer;
GzipOut(templates::$gzip, templates::$gzip_activ);
HTTP::echos();
unset($templates);
unset($db);


?>