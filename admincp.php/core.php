<?php
if(!defined("IS_ADMIN")) {
die();
}
define("IS_CORE", true);
//define("IS_ADMINPANEL", true);
include_once(dirname(__FILE__)."/../core.php");
$defined = array("Cardinal" => "Cardin");

function ReadPlugins($dir, $page, $include=true) {
	$dirs = read_dir($dir, ".".ROOT_EX);
	for($i=0;$i<sizeof($dirs);$i++) {
		include_once($dir.$dirs[$i]);
		if($include) {
			$view = $page."_".str_replace(".".ROOT_EX, "", $dirs[$i]);
			new $view();
		}
	}
}
$in_page = "Main";
templates::dir_skins("admincp.php/temp/".config::Select('skins','admincp'));
templates::set_skins("");


spl_autoload_register(function($class) {
global $in_page;
	if(strpos($class, "/")===false&&strpos($class, "\\")===false&&file_exists(ROOT_PATH."admincp.php".DS."pages".DS."Viewer".DS.$class.DS.$class.".class.".ROOT_EX)) {
		include_once(ROOT_PATH."admincp.php".DS."pages".DS."Viewer".DS.$class.DS.$class.".class.".ROOT_EX);
	} else if(strpos($class, "_")===false) {
		$in_page = "Errors";
		include_once(ROOT_PATH."admincp.php".DS."pages".DS."Viewer".DS."Errors".DS."Errors.class.".ROOT_EX);
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
	$active = false;
	$load = true;
	$obj = "";
	if(config::Select("activeCache")) {
		$par = $_GET;
		array_walk($par, function(&$v, $k) { $v = ($k."-".$v); });
		$url = implode("=", $par);
		$md5 = md5($url);
		if(!file_exists(ROOT_PATH."core".DS."cache".DS."page".DS."admin_".$md5.".txt")) {
			$active = true;
		} else {
			$load = false;
		}
	}
	if($load) {
		if(method_exists(''.$view, 'start')) {
			$view::start();
			defines::init();
		}
		new $view();
	} else {
		include(ROOT_PATH."core".DS."cache".DS."page".DS."admin_".$md5.".txt");
	}
	if($active) {
		$obj = ob_get_contents();
		ob_end_clean();
		file_put_contents(ROOT_PATH."core".DS."cache".DS."page".DS."admin_".$md5.".txt", removeBOM($obj));
		HTTP::echos($obj);
	}
}

?>