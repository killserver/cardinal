<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

function cardinalAutoload($class) {
    if(stripos(ini_get('include_path'), $class)!==false && class_exists($class, false)) {
        return false;
    }
    if(file_exists(PATH_MODULES."autoload".DS.$class.".".ROOT_EX)) {
        include_once(PATH_MODULES."autoload".DS.$class.".".ROOT_EX);
    } elseif(file_exists(PATH_CLASS.$class.".".ROOT_EX)) {
        include_once(PATH_CLASS.$class.".".ROOT_EX);
    } elseif(file_exists(PATH_CLASS."system".DS.$class.".".ROOT_EX)) {
        include_once(PATH_CLASS."system".DS.$class.".".ROOT_EX);
    } elseif(file_exists(PATH_CLASS."system".DS."DBDrivers".DS.$class.".".ROOT_EX)) {
        include_once(PATH_CLASS."system".DS."DBDrivers".DS.$class.".".ROOT_EX);
    }
}
if(version_compare(PHP_VERSION, '5.1.2', '>=')) {
	if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
		spl_autoload_register('cardinalAutoload', true, true);
	} else {
		spl_autoload_register('cardinalAutoload');
	}
} else {
	function __autoload($class) {
		cardinalAutoload($class);
	}
}

if(!defined("WITHOUT_DB") && file_exists(PATH_MEDIA."config.".ROOT_EX) && file_exists(PATH_MEDIA."db.".ROOT_EX)) {
	$config = array();
	if(file_exists(PATH_MEDIA."config.client.".ROOT_EX)) {
		if(is_writable(PATH_MEDIA."config.client.".ROOT_EX)) {
			chmod(PATH_MEDIA."config.client.".ROOT_EX, 0664);
		}
		require_once(PATH_MEDIA."config.client.".ROOT_EX);
	}
	require_once(PATH_MEDIA."config.global.".ROOT_EX);
	require_once(PATH_MEDIA."config.".ROOT_EX);
	if(file_exists(PATH_MEDIA."config.install.".ROOT_EX)) {
		require_once(PATH_MEDIA."config.install.".ROOT_EX);
	}
	require_once(PATH_MEDIA."db.".ROOT_EX);
} else {
	if(!defined("WITHOUT_DB")) {
		define("INSTALLER", true);
	}
	$config = array("charset" => "utf-8");
	if(file_exists(PATH_MEDIA."config.client.".ROOT_EX)) {
		if(is_writable(PATH_MEDIA."config.client.".ROOT_EX)) {
			chmod(PATH_MEDIA."config.client.".ROOT_EX, 0664);
		}
		require_once(PATH_MEDIA."config.client.".ROOT_EX);
	}
	require_once(PATH_MEDIA."config.global.".ROOT_EX);
	if(defined("WITHOUT_DB")) {
		$host = (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "online-killer.pp.ua");
		$hostMD5 = substr(md5($host), 0, 6);
		define("COOK_USER", "username_".$hostMD5);
		define("COOK_PASS", "password_".$hostMD5);
		define("COOK_ADMIN_USER", "admin_username_".$hostMD5);
		define("COOK_ADMIN_PASS", "admin_password_".$hostMD5);
		if(file_exists(PATH_MEDIA."config.default.".ROOT_EX)) {
			require_once(PATH_MEDIA."config.default.".ROOT_EX);
			if(defined("VERSION")) {
				define("START_VERSION", VERSION);
			}
			if(isset($_SERVER['SCRIPT_NAME'])) {
				$link = str_replace(array("index.".ROOT_EX, "install.".ROOT_EX, ADMINCP_DIRECTORY."/"), "", $_SERVER['SCRIPT_NAME']);
			} else {
				$link = "/";
			}
			$config = array_merge($config, array(
				"default_http_local" => $link,
				"default_http_hostname" => $host,
				"default_http_host" => HTTP::$protocol."://".$host.$link,
			));
			unset($link);
		}
		if(file_exists(PATH_MEDIA."config.".ROOT_EX)) {
			require_once(PATH_MEDIA."config.".ROOT_EX);
		}
		if(file_exists(PATH_MEDIA."config.install.".ROOT_EX)) {
			require_once(PATH_MEDIA."config.install.".ROOT_EX);
		}
		if(file_exists(PATH_MEDIA."db.".ROOT_EX)) {
			require_once(PATH_MEDIA."db.".ROOT_EX);
		}
	}
	if(!defined("WITHOUT_DB") && !defined("IS_INSTALLER") && (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], "install")===false)) {
		if(isset($_SERVER['PHP_SELF'])) {
			$link = str_replace(array("index.".ROOT_EX, "install.".ROOT_EX, ADMINCP_DIRECTORY."/"), "", $_SERVER['PHP_SELF']);
		} else {
			$link = "/";
		}
		header("Location: ".(isset($_SERVER['HTTP_HOST']) ? "http://".$_SERVER['HTTP_HOST'] : "").$link."install.".ROOT_EX);
		unset($link);
		die();
	}
}


if(defined("WITHOUT_DB")) {
	if(!defined("PREFIX_DB")) {
		$file = "cardinal_";
		if(file_exists(PATH_MEDIA."prefix_db.lock") && is_readable(PATH_MEDIA."prefix_db.lock")) {
			$file = file_get_contents(PATH_MEDIA."prefix_db.lock");
		} elseif(is_writable(PATH_MEDIA."prefix_db.lock")) {
			$file = "cd".uniqid();
			file_put_contents(PATH_MEDIA."prefix_db.lock", $file);
		}
		define("PREFIX_DB", $file);
	}
	$api_key = cardinal::GenApiKey();
	$config = array_merge($config, array(
		"api_key" => $api_key,
	));
}

set_error_handler(array('cardinalError', 'handlePhpError'));
set_exception_handler(array('cardinalError', 'handleException'));
register_shutdown_function(array('cardinalError', 'handleFatalError'));