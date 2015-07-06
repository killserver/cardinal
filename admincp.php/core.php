<?php
if(!defined("IS_ADMIN")) {
die();
}
define("IS_CORE", true);
//define("IS_ADMINPANEL", true);
include_once(dirname(__FILE__)."/../core.php");
$defined = array("Cardinal" => "Cardin");

function ReadPlugins($dir, $page, $include=true) {
	$dirs = read_dir($dir);
	for($i=0;$i<sizeof($dirs);$i++) {
		include_once($dir.$dirs[$i]);
		if($include) {
			$view = $page."_".str_replace(".php", "", $dirs[$i]);
			new $view();
		}
	}
}
$in_page = "Main";
templates::dir_skins("admincp.php/temp");

spl_autoload_register(function($class) {
global $in_page;
	if(strpos($class, "/")===false&&strpos($class, "\\")===false&&file_exists(ROOT_PATH."admincp.php/pages/Viewer/".$class."/".$class.".class.php")) {
		include_once(ROOT_PATH."admincp.php/pages/Viewer/".$class."/".$class.".class.php");
	} else if(strpos($class, "_")===false) {
		$in_page = "Errors";
		include_once(ROOT_PATH."admincp.php/pages/Viewer/Errors/Errors.class.php");
		new Errors();
	}
});
if(isset($_GET['pages'])) {
	$view = htmlspecialchars(strip_tags($_GET['pages']));
	$view = ucfirst($view);
} else {
	$view = "Main";
}
if(in_array($view, array_keys($defined))) {
	$view = $defined[$view];
}
$in_page = $view;
if(class_exists($view)) {
	new $view();
}

?>