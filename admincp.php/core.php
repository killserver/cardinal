<?php
if(!defined("IS_ADMIN")) {
die();
}
define("IS_CORE", true);
//define("IS_ADMINPANEL", true);
include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."core.php");

$defined = array("Cardinal" => "Cardin");

function ReadPlugins($dir, $page, $include=true) {
	$dirs = read_dir($dir, ".".ROOT_EX);
	for($i=0;$i<sizeof($dirs);$i++) {
		if(strpos($dirs[$i],"index.".ROOT_EX)!==false || strpos($dirs[$i],"index.html")!==false) {
			continue;
		}
		include_once($dir.$dirs[$i]);
		if($include) {
			$view = $page."_".str_replace(".".ROOT_EX, "", $dirs[$i]);
			new $view();
		}
	}
}
$in_page = "Main";
$skin = config::Select('skins','admincp');
$skin = (!is_string($skin) ? "xenon" : $skin);
config::Set("skins", "admincp", $skin);
templates::dir_skins(str_replace(ROOT_PATH, "", ADMIN_SKINS.$skin));
templates::set_skins("");

function accessOnDefault($class) {
	if(defined("CHECK_MOD_ADMIN") || defined("DEVELOPER_MODE")) {
		return true;
	}
	$classCheck = strtolowers($class);
	if(in_array($class, array("Core", "Errors"))) {
		return true;
	}
	if(!in_array($class, array("Archer", "Login", "Main")) && userlevel::get($classCheck)===false) {
		return false;
	}
	return true;
}
function cardinalAutoloadAdmin($class) {
    global $in_page;
    if(strpos($class, "/")===false&&strpos($class, "\\")===false&&!class_exists($class,false)&&accessOnDefault($class)!==false&&file_exists(ADMIN_VIEWER.$class.DS.$class.".class.".ROOT_EX)) {
        include_once(ADMIN_VIEWER.$class.DS.$class.".class.".ROOT_EX);
    } else if(strpos($class, "_")===false) {
        $in_page = "Errors";
        include_once(ADMIN_VIEWER."Errors".DS."Errors.class.".ROOT_EX);
        new Errors();
    }
}
if(version_compare(PHP_VERSION, '5.1.2', '>=')) {
	if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
		spl_autoload_register('cardinalAutoloadAdmin', true, false);
	} else {
		spl_autoload_register('cardinalAutoloadAdmin');
	}
} else {
	function __autoload($class) {
		cardinalAutoloadAdmin($class);
	}
}
if(isset($_GET['pages']) && $_GET['pages'] != "Core") {
	$view = htmlspecialchars(strip_tags($_GET['pages']));
	$view = nucfirst($view);
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
        if(!function_exists("cacheWalk")) {
            function cacheWalk(&$v, $k) {
                $v = ($k . "-" . $v);
            }
        }
		$par = $_GET;
		array_walk($par, "cacheWalk");
		$url = implode("=", $par);
		$md5 = md5($url);
		if(!file_exists(PATH_CACHE_PAGE."admin_".$md5.".txt")) {
			$active = true;
		} else {
			$load = false;
		}
	}
	if($load) {
		if(method_exists(''.$view, 'start')) {
			call_user_func(array(&$view, "start"));
		}
		new $view();
	} else {
		include(PATH_CACHE_PAGE."admin_".$md5.".txt");
	}
	if($active) {
		$obj = ob_get_contents();
		ob_end_clean();
		file_put_contents(PATH_CACHE_PAGE."admin_".$md5.".txt", removeBOM($obj));
		HTTP::echos($obj);
	}
}

?>