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

require_once(ROOT_PATH."core".DS."loadConfig.".ROOT_EX);

function require_dir($dir = "", $modules = "", $force = false) { include_dir($dir, $modules, $force); }

function include_dir($dir = "", $modules = "", $force = false) {
	if(empty($dir)) {
		$dir = PATH_FUNCTIONS;
	}
	if(empty($modules)) {
		$modules = ".".ROOT_EX;
		$inc = false;
	} else {
		$inc = true;
	}
	$useNew = false;
	if(is_dir($dir)) {
		if($dh = dir($dir)) {
			while(($file = $dh->read()) !== false) {
				$strip_path = str_replace(ROOT_PATH, "", PATH_MODULES);
				if($file != "index.html" && $file != "index.".ROOT_EX && $file != "." && $file != "..") {
					if($inc && is_file($dir.DS.$file) && strpos($file, $modules) === false) {
						continue;
					}
					if($inc && !modules::load_modules($strip_path.$file, $modules)) {
						if(!$force) {
							continue;
						}
					}
					if(is_dir($dir.DS.$file)) {
						$useNew = true;
					}
					if($useNew) {
						$class = str_replace($modules, "", $file);
						require_once($dir.$class.DS."init.".ROOT_EX);
					} else {
						require_once($dir.$file);
					}
					if($inc) {
						$class = str_replace($modules, "", $file);
						if(class_exists($class)) {
							modules::initialize($class);
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
if(file_exists(PATH_MEDIA."config.route.global.".ROOT_EX)) {
	require_once(PATH_MEDIA."config.route.global.".ROOT_EX);
}
if(file_exists(PATH_MEDIA."config.route.".ROOT_EX)) {
	require_once(PATH_MEDIA."config.route.".ROOT_EX);
}
include_dir(PATH_MODULES, ".class.".ROOT_EX);
include_dir();
if(file_exists(ROOT_PATH.".env")) {
	loadConfig(".env");
}

if(file_exists(ROOT_PATH.".htaccess") && defined("DEVELOPER_MODE")) {
	$file = file_get_contents(ROOT_PATH.".htaccess");
	if(strpos($file, "# Add htaccess")===false) {
		if(@chmod(ROOT_PATH.".htaccess", 0777)) {
			$file = "# Add htaccess\n<IfModule pagespeed_module>\n\tModPagespeed off\n</IfModule>\n\n".$file;
			file_put_contents(ROOT_PATH.".htaccess", $file);
		}
		@chmod(ROOT_PATH.".htaccess", 0644);
	}
} else if(file_exists(ROOT_PATH.".htaccess") && !defined("DEVELOPER_MODE")) {
	$file = file_get_contents(ROOT_PATH.".htaccess");
	if(strpos($file, "# Add htaccess")!==false) {
		if(@chmod(ROOT_PATH.".htaccess", 0777)) {
			$fLen = strlen("# Add htaccess\n<IfModule pagespeed_module>\n\tModPagespeed on\n</IfModule>\n\n");
			$file = substr($file, $fLen);
			file_put_contents(ROOT_PATH.".htaccess", $file);
		}
		@chmod(ROOT_PATH.".htaccess", 0644);
	}
}
$error = error_get_last();
if(is_array($error) && in_array($error['type'], array(E_PARSE, E_ERROR, E_USER_ERROR))) {
	// Clean the output buffer
	ob_get_level() && ob_clean();

	// Fake an exception for nice debugging
	cardinalError::handleException(new ErrorException($error['message'], $error['type'], 0, $error['file'], $error['line']));

	// Shutdown now to avoid a "death loop"
	exit(1);
}
?>