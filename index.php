<?php
define("IS_CORE", true);
include_once("core.php");

if(!userlevel::get("site")) {
	$templates->error("{L_error_level_full}", "{L_error_level}");
}

$server = $_SERVER['QUERY_STRING'];
//cut($server, nstrpos($server, "?")+1, nstrlen($server))
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


$manifest['mod_page'][getenv("REMOTE_ADDR")]['page'] = $page;
view_pages($page);
if(class_exists("page")) {
	new page();
}
if(defined("DEBUG")) {
	ini_set('display_errors',1);
	error_reporting(E_ALL);
}

$Timer = microtime()-$Timer;
GzipOut($templates->gzip);
unset($templates);
unset($db);


?>