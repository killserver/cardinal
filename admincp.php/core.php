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
templates::dir_skins(ADMINCP_DIRECTORY."/temp/".$skin);
templates::set_skins("");

function cardinalAutoloadAdmin($class) {
    global $in_page;
    if(strpos($class, "/")===false&&strpos($class, "\\")===false&&file_exists(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."Viewer".DS.$class.DS.$class.".class.".ROOT_EX)) {
        include_once(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."Viewer".DS.$class.DS.$class.".class.".ROOT_EX);
    } else if(strpos($class, "_")===false) {
        $in_page = "Errors";
        include_once(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."Viewer".DS."Errors".DS."Errors.class.".ROOT_EX);
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
        if(!function_exists("cacheWalk")) {
            function cacheWalk(&$v, $k) {
                $v = ($k . "-" . $v);
            }
        }
		$par = $_GET;
		array_walk($par, "cacheWalk");
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
			call_user_func(array(&$view, "start"));
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