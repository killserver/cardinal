<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

function cardinalAutoload($class) {
    if(stripos(ini_get('include_path'), $class)!==false && class_exists($class, false)) {
        return false;
    }
    if(file_exists(PATH_AUTOLOADS.$class.".".ROOT_EX)) {
        include_once(PATH_AUTOLOADS.$class.".".ROOT_EX);
    } elseif(file_exists(PATH_CLASS.$class.".".ROOT_EX)) {
        include_once(PATH_CLASS.$class.".".ROOT_EX);
    } elseif(file_exists(PATH_SYSTEM.$class.".".ROOT_EX)) {
        include_once(PATH_SYSTEM.$class.".".ROOT_EX);
    } elseif(file_exists(PATH_DB_DRIVERS.$class.".".ROOT_EX)) {
        include_once(PATH_DB_DRIVERS.$class.".".ROOT_EX);
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

function addEvent() {
	return call_user_func_array("cardinalEvent::addListener", func_get_args());
}

function removeEvent() {
	return call_user_func_array("cardinalEvent::removeListener", func_get_args());
}

function execEvent() {
	return call_user_func_array("cardinalEvent::execute", func_get_args());
}

$host = (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "online-killer.pp.ua");
$config = array(
	"charset" => "utf-8",
);
$config = execEvent("before_load_config", $config);
if(file_exists(PATH_MEDIA."config.client.".ROOT_EX)) {
	if(is_writable(PATH_MEDIA."config.client.".ROOT_EX)) {
		chmod(PATH_MEDIA."config.client.".ROOT_EX, 0664);
	}
	require_once(PATH_MEDIA."config.client.".ROOT_EX);
}
if(!defined("WITHOUT_DB") && file_exists(PATH_MEDIA."config.".ROOT_EX) && file_exists(PATH_MEDIA."db.".ROOT_EX)) {
	require_once(PATH_MEDIA."config.global.".ROOT_EX);
	require_once(PATH_MEDIA."config.".ROOT_EX);
	if(file_exists(PATH_MEDIA."config.install.".ROOT_EX)) {
		require_once(PATH_MEDIA."config.install.".ROOT_EX);
	}
	if(file_exists(PATH_MEDIA."config.".str_replace("www.", "", $host).".".ROOT_EX)) {
		require_once(PATH_MEDIA."config.".str_replace("www.", "", $host).".".ROOT_EX);
	}
	require_once(PATH_MEDIA."db.".ROOT_EX);
	if(file_exists(PATH_MEDIA."db.".str_replace("www.", "", $host).".".ROOT_EX)) {
		require_once(PATH_MEDIA."db.".str_replace("www.", "", $host).".".ROOT_EX);
	}
} else {
	if(!defined("WITHOUT_DB")) {
		define("INSTALLER", true);
	}
	require_once(PATH_MEDIA."config.global.".ROOT_EX);
	$protocol = "http";
	if(
		   (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
		|| (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && !empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
		|| (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on')
		|| (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
		|| (isset($_SERVER['HTTP_X_FORWARDED_PORT']) && $_SERVER['HTTP_X_FORWARDED_PORT'] == 443)
		|| (isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https')
		|| (isset($_SERVER['CF_VISITOR']) && $_SERVER['CF_VISITOR'] == '{"scheme":"https"}')
		|| (isset($_SERVER['HTTP_CF_VISITOR']) && $_SERVER['HTTP_CF_VISITOR'] == '{"scheme":"https"}')
	) {
		$protocol = "https";
	}
	if(defined("WITHOUT_DB")) {
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
				"default_http_host" => $protocol."://".$host.$link,
			));
			unset($link);
		}
		if(file_exists(PATH_MEDIA."config.".ROOT_EX)) {
			require_once(PATH_MEDIA."config.".ROOT_EX);
		}
		if(file_exists(PATH_MEDIA."config.install.".ROOT_EX)) {
			require_once(PATH_MEDIA."config.install.".ROOT_EX);
		}
		if(file_exists(PATH_MEDIA."config.".str_replace("www.", "", $host).".".ROOT_EX)) {
			require_once(PATH_MEDIA."config.".str_replace("www.", "", $host).".".ROOT_EX);
		}
		if(file_exists(PATH_MEDIA."db.".ROOT_EX)) {
			require_once(PATH_MEDIA."db.".ROOT_EX);
		}
		if(file_exists(PATH_MEDIA."db.".str_replace("www.", "", $host).".".ROOT_EX)) {
			require_once(PATH_MEDIA."db.".str_replace("www.", "", $host).".".ROOT_EX);
		}
	}
	if(!defined("WITHOUT_DB") && !defined("IS_INSTALLER") && (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], "install")===false)) {
		if(isset($_SERVER['PHP_SELF'])) {
			$link = str_replace(array("index.".ROOT_EX, "install.".ROOT_EX, ADMINCP_DIRECTORY."/"), "", $_SERVER['PHP_SELF']);
		} else {
			$link = "/";
		}
		header("Location: ".(isset($_SERVER['HTTP_HOST']) ? $protocol."://".$_SERVER['HTTP_HOST'] : "").$link."install.".ROOT_EX);
		unset($link);
		die();
	}
}

if(file_exists(PATH_MEDIA."config.init.".ROOT_EX)) {
	include_once(PATH_MEDIA."config.init.".ROOT_EX);
}
if(file_exists(PATH_MEDIA."config.settings.".ROOT_EX)) {
	include_once(PATH_MEDIA."config.settings.".ROOT_EX);
}
if(defined("DEBUG_ACTIVATED") && file_exists(PATH_MEDIA."config.dev.".ROOT_EX)) {
	include_once(PATH_MEDIA."config.dev.".ROOT_EX);
}
$config = execEvent("after_load_config", $config);

if(file_exists(ROOT_PATH.ADMINCP_DIRECTORY.DS."paths.".ROOT_EX)) {
	include_once(ROOT_PATH.ADMINCP_DIRECTORY.DS."paths.".ROOT_EX);
} else if(file_exists(ROOT_PATH.ADMINCP_DIRECTORY.DS."paths.default.".ROOT_EX)) {
	include_once(ROOT_PATH.ADMINCP_DIRECTORY.DS."paths.default.".ROOT_EX);
}

if(defined("WITHOUT_DB")) {
	if(!defined("PREFIX_DB")) {
		$file = "cardinal_";
		if(file_exists(PATH_MEDIA."prefix_db.lock") && is_readable(PATH_MEDIA."prefix_db.lock")) {
			$file = file_get_contents(PATH_MEDIA."prefix_db.lock");
		} elseif(is_writable(PATH_MEDIA."prefix_db.lock")) {
			$file = "cd".uniqid();
			@file_put_contents(PATH_MEDIA."prefix_db.lock", $file);
		}
		$file = execEvent("set_prefix_db", $file);
		define("PREFIX_DB", $file);
	}
	$api_key = cardinal::GenApiKey();
	$api_key = execEvent("set_api_key", $api_key);
	$config = array_merge($config, array(
		"api_key" => $api_key,
	));
}

set_error_handler(array('cardinalError', 'handlePhpError'));
set_exception_handler(array('cardinalError', 'handleException'));
register_shutdown_function(array('cardinalError', 'handleFatalError'));