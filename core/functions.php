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

if(file_exists(ROOT_PATH."core".DS."media".DS."config.".ROOT_EX) && file_exists(ROOT_PATH."core".DS."media".DS."db.".ROOT_EX)) {
	$config = array();
	if(file_exists(ROOT_PATH."core".DS."media".DS."config.client.php")) {
		chmod(ROOT_PATH."core".DS."media".DS."config.client.php", 0664);
		require_once(ROOT_PATH."core".DS."media".DS."config.client.php");
	}
	require_once(ROOT_PATH."core".DS."media".DS."config.global.".ROOT_EX);
	require_once(ROOT_PATH."core".DS."media".DS."config.".ROOT_EX);
	if(file_exists(ROOT_PATH."core".DS."media".DS."config.install.".ROOT_EX)) {
		require_once(ROOT_PATH."core".DS."media".DS."config.install.".ROOT_EX);
	}
	require_once(ROOT_PATH."core".DS."media".DS."db.".ROOT_EX);
} else {
	define("INSTALLER", true);
	$config = array("charset" => "utf-8");
	if(file_exists(ROOT_PATH."core".DS."media".DS."config.client.php")) {
		chmod(ROOT_PATH."core".DS."media".DS."config.client.php", 0664);
		require_once(ROOT_PATH."core".DS."media".DS."config.client.php");
	}
	require_once(ROOT_PATH."core".DS."media".DS."config.global.".ROOT_EX);
	if(!defined("IS_INSTALLER") && (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], "install")===false)) {
		if(isset($_SERVER['PHP_SELF'])) {
			$link = str_replace(array("index.php", "admincp.php/"), "", $_SERVER['PHP_SELF']);
		} else {
			$link = "/";
		}
		header("Location: ".(isset($_SERVER['HTTP_HOST']) ? "http://".$_SERVER['HTTP_HOST'] : "").$link."install.php");
		unset($link);
		die();
	}
}

spl_autoload_register(function($class) {
	if(stripos(ini_get('include_path'), $class)!==false && class_exists($class)) {
		return false;
	}
	if(file_exists(ROOT_PATH."core".DS."modules".DS."autoload".DS.$class.".".ROOT_EX)) {
		include_once(ROOT_PATH."core".DS."modules".DS."autoload".DS.$class.".".ROOT_EX);
	} elseif(file_exists(ROOT_PATH."core".DS."class".DS.$class.".".ROOT_EX)) {
		include_once(ROOT_PATH."core".DS."class".DS.$class.".".ROOT_EX);
	} elseif(file_exists(ROOT_PATH."core".DS."class".DS."system".DS.$class.".".ROOT_EX)) {
		include_once(ROOT_PATH."core".DS."class".DS."system".DS.$class.".".ROOT_EX);
	} elseif(file_exists(ROOT_PATH."core".DS."class".DS."system".DS."drivers".DS.$class.".".ROOT_EX)) {
		include_once(ROOT_PATH."core".DS."class".DS."system".DS."drivers".DS.$class.".".ROOT_EX);
	}
});

set_error_handler(array('Error', 'handlePhpError'));
set_exception_handler(array('Error', 'handleException'));
register_shutdown_function(array('Error', 'handleFatalError'));

function require_dir($dir = "", $modules = "", $mod = false) {include_dir($dir, $modules, $mod);}

function include_dir($dir = "", $modules = "", $mod = false) {
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
				if($file != "index.".ROOT_EX && $file != "." && $file != ".." && strpos($file, $modules) !== false && ($inc ? modules::load_modules("core".DS."modules".DS.$file, $modules) : true)) {
					require_once($dir.$file);
					if($inc) {
						$class = str_replace($modules, "", $file);
						if(class_exists($class)) {
							$classes = new $class();
							unset($classes);
						}
					}
				}
			}
		$dh->close();
		}
	}
}
if(file_exists(ROOT_PATH."core".DS."media".DS."config.route.global.".ROOT_EX)) {
	require_once(ROOT_PATH."core".DS."media".DS."config.route.global.".ROOT_EX);
}
if(file_exists(ROOT_PATH."core".DS."media".DS."config.route.".ROOT_EX)) {
	require_once(ROOT_PATH."core".DS."media".DS."config.route.".ROOT_EX);
}
include_dir(ROOT_PATH."core".DS."modules".DS, ".class.".ROOT_EX, true);
include_dir();
?>