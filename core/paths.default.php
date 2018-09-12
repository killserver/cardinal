<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}


if(!defined("PATH_CLASS")) {
	define("PATH_CLASS", ROOT_PATH."core".DS."class".DS);
}
if(!defined("PATH_SYSTEM")) {
	define("PATH_SYSTEM", ROOT_PATH."core".DS."class".DS."system".DS);
}
if(!defined("PATH_DB_DRIVERS")) {
	define("PATH_DB_DRIVERS", ROOT_PATH."core".DS."class".DS."system".DS."DBDrivers".DS);
}
if(!defined("PATH_CACHE")) {
	define("PATH_CACHE", ROOT_PATH."core".DS."cache".DS);
}
if(!defined("PATH_CACHE_PAGE")) {
	define("PATH_CACHE_PAGE", ROOT_PATH."core".DS."cache".DS."page".DS);
}
if(!defined("PATH_CACHE_SYSTEM")) {
	define("PATH_CACHE_SYSTEM", ROOT_PATH.'core'.DS.'cache'.DS.'system'.DS);
}
if(!defined("PATH_LOGS")) {
	define("PATH_LOGS", ROOT_PATH.'core'.DS.'cache'.DS.'system'.DS);
}
if(!defined("PATH_CACHE_LANGS")) {
	define("PATH_CACHE_LANGS", ROOT_PATH.'core'.DS.'cache'.DS.'lang'.DS);
}
if(!defined("PATH_CACHE_SESSION")) {
	define("PATH_CACHE_SESSION", ROOT_PATH."core".DS."cache".DS."session".DS);
}
if(!defined("PATH_CACHE_USERDATA")) {
	define("PATH_CACHE_USERDATA", ROOT_PATH."application".DS."cache".DS);
}
if(!defined("PATH_FUNCTIONS")) {
	define("PATH_FUNCTIONS", ROOT_PATH."core".DS."functions".DS);
}
if(!defined("PATH_MEDIA")) {
	define("PATH_MEDIA", ROOT_PATH."core".DS."media".DS);
}
if(!defined("PATH_LANGS")) {
	define("PATH_LANGS", ROOT_PATH."core".DS."lang".DS);
}
if(!defined("PATH_MODULES")) {
	define("PATH_MODULES", ROOT_PATH."application".DS."modules".DS);
}
if(!defined("PATH_GLOBAL")) {
	define("PATH_GLOBAL", ROOT_PATH."application".DS."global".DS);
}
if(!defined("PATH_AUTOLOADS")) {
	define("PATH_AUTOLOADS", ROOT_PATH."application".DS."autoload".DS);
}
if(!defined("PATH_LOAD_LIBRARY")) {
	define("PATH_LOAD_LIBRARY", ROOT_PATH."application".DS."library".DS);
}
if(!defined("PATH_LOADED_CONTENT")) {
	define("PATH_LOADED_CONTENT", ROOT_PATH."application".DS);
}
if(!defined("PATH_MODELS")) {
	define("PATH_MODELS", ROOT_PATH."application".DS."models".DS);
}
if(!defined("PATH_CRON_FILES")) {
	define("PATH_CRON_FILES", ROOT_PATH."application".DS."cron".DS);
}
if(!defined("PATH_PAGES")) {
	define("PATH_PAGES", ROOT_PATH."core".DS."pages".DS);
}
if(!defined("PATH_SKINS")) {
	define("PATH_SKINS", ROOT_PATH."skins".DS);
}
if(!defined("PATH_UPLOADS")) {
	define("PATH_UPLOADS", ROOT_PATH."uploads".DS);
}
if(!defined("PATH_MANIFEST")) {
	define("PATH_MANIFEST", ROOT_PATH."uploads".DS."manifest".DS);
}
if(!defined("ADMINCP_DIRECTORY")) {
	define("ADMINCP_DIRECTORY", "admincp.php");
}

?>