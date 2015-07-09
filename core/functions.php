<?php
/*
*
* Version Engine: 1.25.5a6
* Version File: 3
*
* 3.2
* add checker install system
*
* 3.3
* add support include config file after installing
*
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

$config = array();
if(file_exists(ROOT_PATH."core/media/config.".ROOT_EX) && file_exists(ROOT_PATH."core/media/db.".ROOT_EX)) {
	require_once(ROOT_PATH."core/media/config.global.".ROOT_EX);
	require_once(ROOT_PATH."core/media/config.".ROOT_EX);
	if(file_exists(ROOT_PATH."core/media/config.install.".ROOT_EX)) {
		require_once(ROOT_PATH."core/media/config.install.".ROOT_EX);
	}
	require_once(ROOT_PATH."core/media/db.".ROOT_EX);
} else {
	define("INSTALLER", true);
	$config = array("charset" => "utf-8");
	require_once(ROOT_PATH."core/media/config.global.".ROOT_EX);
	if(!defined("IS_INSTALLER")) {
		header("Location: install.php");
	}
}

spl_autoload_register(function($class) {
	if(file_exists(ROOT_PATH."core/class/".$class.".".ROOT_EX)) {
		include_once(ROOT_PATH."core/class/".$class.".".ROOT_EX);
	} elseif(file_exists(ROOT_PATH."core/class/system/".$class.".".ROOT_EX)) {
		include_once(ROOT_PATH."core/class/system/".$class.".".ROOT_EX);
	} elseif(!defined("IS_ADMINPANEL") && file_exists(ROOT_PATH."core/modules/autoload/".$class.".".ROOT_EX)) {
		include_once(ROOT_PATH."core/modules/autoload/".$class.".".ROOT_EX);
	}
});

//ToDo: delete this function?
function FriendlyErrorType($type) {
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
}

set_error_handler(array('Error', 'handlePhpError'));
set_exception_handler(array('Error', 'handleException'));
register_shutdown_function(array('Error', 'handleFatalError'));

function require_dir($dir = null, $modules = null) {include_dir($dir, $modules);}

function include_dir($dir = null, $modules = null) {
	if(empty($dir)) {
		$dir = ROOT_PATH."core/functions/";
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
if(!defined("IS_ADMINPANEL")) {
	include_dir(ROOT_PATH."core/modules/", ".class.".ROOT_EX);
}
include_dir();

?>