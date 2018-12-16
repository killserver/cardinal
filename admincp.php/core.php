<?php
if(!defined("IS_ADMIN")) {
	die();
}
define("IS_CORE", true);
define("IS_ADMINCP", true);
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
    if(strpos($class, "/")===false && strpos($class, "\\")===false && !class_exists($class,false) && accessOnDefault($class)!==false) {
    	if(file_exists(ADMIN_VIEWER.$class.DS.$class.".class.".ROOT_EX)) {
	        include_once(ADMIN_VIEWER.$class.DS.$class.".class.".ROOT_EX);
	    } else if(file_exists(ADMIN_VIEWER.$class.".class.".ROOT_EX)) {
	    	include_once(ADMIN_VIEWER.$class.".class.".ROOT_EX);
	    }
    } else if(strpos($class, "_")===false) {
        $in_page = "Errors";
        include_once(ADMIN_VIEWER."Errors".DS."Errors.class.".ROOT_EX);
    }
}
if(version_compare(PHP_VERSION, '5.1.2', '>=')) {
	if(version_compare(PHP_VERSION, '5.3.0', '>=')) {
		spl_autoload_register('cardinalAutoloadAdmin', true, false);
	} else {
		spl_autoload_register('cardinalAutoloadAdmin');
	}
} else {
	function __autoload($class) {
		cardinalAutoloadAdmin($class);
	}
}

$in_page = "Main";
execEvent("adminRoute");
Route::setError(0);
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
Route::Load($in_page);
$langs = $lang; $tmp = $templates; $dbs = $db;
extract(Route::param());
$lang = $langs; $templates = $tmp; $db = $dbs;
unset($langs, $tmp, $dbs);
$skin = config::Select('skins','admincp');
$skin = (!is_string($skin) ? "xenon" : $skin);
config::Set("skins", "admincp", $skin);
templates::dir_skins(str_replace(ROOT_PATH, "", ADMIN_SKINS.$skin));
templates::set_skins("");
$resp = Route::param("response", false);
if($resp!==false) {
	$view = "Errors";
} else if(($class = Route::param("class"))!==false) {
	$view = $class;
} else if(($in_page = Route::param("in_page"))!==false) {
	$view = $in_page;
} else if(isset($_GET['pages']) && $_GET['pages'] != "Core") {
	$view = htmlspecialchars(strip_tags($_GET['pages']));
	$view = nucfirst($view);
} else {
	$view = "Main";
}
$view = execEvent("admin_page", $view);

if(in_array($view, array_keys($defined))) {
	$view = $defined[$view];
}
$in_page = $view;
if(class_exists($view)) {
	if(!defined("ADMIN_PAGE_NOW")) {
		define("ADMIN_PAGE_NOW", $view);
	}
	execEvent("admin_ready");
	if(method_exists(''.$view, 'start')) {
		call_user_func(array(&$view, "start"));
	}
	$call = new $view();
	if(isset($method) && method_exists($call, $method)) {
		call_user_func_array(array($call, $method), array());
	}
} else {
	$in_page = "Errors";
	$in_page = execEvent("admin_page_notfound", $in_page);
    include_once(ADMIN_VIEWER.$in_page.DS.$in_page.".class.".ROOT_EX);
    new $in_page();
}

?>