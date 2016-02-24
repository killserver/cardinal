<?php
/*
 *
 * @version 3.0
 * @copyright 2014-2016 KilleR for Cardinal Engine
 *
 * Version Engine: 3.0
 * Version File: 3
 *
 * 3.2
 * add checker install system
 * 3.3
 * add support include config file after installing
 * 3.4
 * add support drivers for databases
 * 3.5
 * add support basic and user setting for routification
 * 3.6
 * add support PEAR installed on server
 * 3.7
 * add support special symbol delimer for Windows OS
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

$config = array();
if(file_exists(ROOT_PATH."core".DS."media".DS."config.".ROOT_EX) && file_exists(ROOT_PATH."core".DS."media".DS."db.".ROOT_EX)) {
	require_once(ROOT_PATH."core".DS."media".DS."config.global.".ROOT_EX);
	require_once(ROOT_PATH."core".DS."media".DS."config.".ROOT_EX);
	if(file_exists(ROOT_PATH."core".DS."media".DS."config.install.".ROOT_EX)) {
		require_once(ROOT_PATH."core".DS."media".DS."config.install.".ROOT_EX);
	}
	require_once(ROOT_PATH."core".DS."media".DS."db.".ROOT_EX);
} else {
	define("INSTALLER", true);
	$config = array("charset" => "utf-8");
	require_once(ROOT_PATH."core".DS."media".DS."config.global.".ROOT_EX);
	if(isset($_SERVER["REQUEST_URI"]) && strpos($_SERVER["REQUEST_URI"], "install")===false) {
		header("Location: install.php");
	}
}

spl_autoload_register(function($class) {
	if(stripos(ini_get('include_path'), $class)!==false) {
		return false;
	}
	if(file_exists(ROOT_PATH."core".DS."class".DS.$class.".".ROOT_EX)) {
		include_once(ROOT_PATH."core".DS."class".DS.$class.".".ROOT_EX);
	} elseif(file_exists(ROOT_PATH."core".DS."class".DS."system".DS.$class.".".ROOT_EX)) {
		include_once(ROOT_PATH."core".DS."class".DS."system".DS.$class.".".ROOT_EX);
	} elseif(file_exists(ROOT_PATH."core".DS."modules".DS."autoload".DS.$class.".".ROOT_EX)) {
		include_once(ROOT_PATH."core".DS."modules".DS."autoload".DS.$class.".".ROOT_EX);
	} elseif(file_exists(ROOT_PATH."core".DS."class".DS."system".DS."drivers".DS.$class.".".ROOT_EX)) {
		include_once(ROOT_PATH."core".DS."class".DS."system".DS."drivers".DS.$class.".".ROOT_EX);
	}
});

//ToDo: delete this function?
/*function FriendlyErrorType($type) {
	if($type==E_ERROR) { // 1 // 
		return 'E_ERROR'; 
	} else if($type==E_WARNING) { // 2 // 
		return 'E_WARNING'; 
	} else if($type==E_PARSE) { // 4 // 
		return 'E_PARSE'; 
	} else if($type==E_NOTICE) { // 8 // 
		return 'E_NOTICE'; 
	} else if($type==E_CORE_ERROR) { // 16 // 
		return 'E_CORE_ERROR'; 
	} else if($type==E_CORE_WARNING) { // 32 // 
		return 'E_CORE_WARNING'; 
	} else if($type==E_COMPILE_ERROR) { // 64 // 
		return 'E_COMPILE_ERROR'; 
	} else if($type==E_COMPILE_WARNING) { // 128 // 
		return 'E_COMPILE_WARNING'; 
	} else if($type==E_USER_ERROR) { // 256 // 
		return 'E_USER_ERROR'; 
	} else if($type==E_USER_WARNING) { // 512 // 
		return 'E_USER_WARNING'; 
	} else if($type==E_USER_NOTICE) { // 1024 // 
		return 'E_USER_NOTICE'; 
	} else if($type==E_STRICT) { // 2048 // 
		return 'E_STRICT'; 
	} else if($type==E_RECOVERABLE_ERROR) { // 4096 // 
		return 'E_RECOVERABLE_ERROR'; 
	} else if($type==E_DEPRECATED) { // 8192 // 
		return 'E_DEPRECATED';
	} else if($type==E_USER_DEPRECATED) { // 16384 //
		return 'E_USER_DEPRECATED';
	} else {
		return "";
	}
}*/

set_error_handler(array('Error', 'handlePhpError'));
set_exception_handler(array('Error', 'handleException'));
register_shutdown_function(array('Error', 'handleFatalError'));

function require_dir($dir = null, $modules = null, $mod = false) {include_dir($dir, $modules, $mod);}

function include_dir($dir = null, $modules = null, $mod = false) {
	if(empty($dir)) {
		$dir = ROOT_PATH."core".DS."functions".DS;
	}
	if(empty($modules)) {
		$modules = ".".ROOT_EX;
		$inc = false;
	} else {
		$inc = true;
	}
	if(is_dir($dir)) {
		if($dh = dir($dir)) {
			while(($file = $dh->read()) !== false) {
				if($file != "index.".ROOT_EX && $file != "." && $file != ".." && strpos($file, $modules) !== false) {
					require_once($dir.$file);
					if($inc) {
						$class = str_replace($modules, "", $file);
						$classes = new $class();
						unset($classes);
					}
				}
			}
		$dh->close();
		}
	}
}
if(file_exists(ROOT_PATH."core".DS."media".DS."config.route.global.php")) {
	require_once(ROOT_PATH."core".DS."media".DS."config.route.global.php");
}
if(file_exists(ROOT_PATH."core".DS."media".DS."config.route.php")) {
	require_once(ROOT_PATH."core".DS."media".DS."config.route.php");
}
include_dir(ROOT_PATH."core".DS."modules".DS, ".class.".ROOT_EX, true);
include_dir();

?>