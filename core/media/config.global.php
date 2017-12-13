<?php
/*
 *
 * @version 3.3
 * @copyright 2014-2016 KilleR for Cardinal Engine
 *
 * Version Engine: 3.3
 * Version File: 1
 *
 * 1.1
 * add revision
 * 1.2
 * add support moder and admin
 * 1.3
 * add support upload and debug
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

// Cache
if(!defined("CACHE_MEMCACHE")) {
	define("CACHE_MEMCACHE", 1);
}
if(!defined("CACHE_MEMCACHED")) {
	define("CACHE_MEMCACHED", 2);
}
if(!defined("CACHE_FILE")) {
	define("CACHE_FILE", 3);
}
if(!defined("CACHE_FTP")) {
	define("CACHE_FTP", 4);
}
if(!defined("CACHE_XCACHE")) {
	define("CACHE_XCACHE", 5);
}
if(!defined("CACHE_REDIS")) {
	define("CACHE_REDIS", 6);
}
if(!defined("CACHE_APC")) {
	define("CACHE_APC", 6);
}
if(!defined("CACHE_WINCACHE")) {
	define("CACHE_WINCACHE", 7);
}
if(!defined("CACHE_NONE")) {
	define("CACHE_NONE", 0);
}

// Error
if(!defined("ERROR_FILE")) {
	define("ERROR_FILE", 0);
}
if(!defined("ERROR_DB")) {
	define("ERROR_DB", 1);
}

// Debug
if(!defined("DEBUG_MEMORY")) {
	define("DEBUG_MEMORY", 1);
}
if(!defined("DEBUG_TIME")) {
	define("DEBUG_TIME", 2);
}
if(!defined("DEBUG_FILES")) {
	define("DEBUG_FILES", 3);
}
if(!defined("DEBUG_INCLUDE")) {
	define("DEBUG_INCLUDE", 4);
}
if(!defined("DEBUG_DB")) {
	define("DEBUG_DB", 5);
}
if(!defined("DEBUG_TEMPLATE")) {
	define("DEBUG_TEMPLATE", 6);
}
if(!defined("DEBUG_FILE")) {
	define("DEBUG_FILE", 12);
}
if(!defined("DEBUG_CORE")) {
	define("DEBUG_CORE", 24);
}
if(!defined("DEBUG_DBTEMP")) {
	define("DEBUG_DBTEMP", 30);
}
if(!defined("DEBUG_ALL")) {
	define("DEBUG_ALL", 720);
}

// Upload
if(!defined("UPLOAD_CORE")) {
	define("UPLOAD_CORE", 10);
}
if(!defined("UPLOAD_FTP")) {
	define("UPLOAD_FTP", 20);
}
if(!defined("UPLOAD_MYSQL")) {
	define("UPLOAD_MYSQL", 30);
}
if(!defined("UPLOAD_DROPBOX")) {
	define("UPLOAD_DROPBOX", 40);
}
if(!defined("UPLOAD_CF")) {
	define("UPLOAD_CF", 200);
}
if(!defined("UPLOAD_CFM")) {
	define("UPLOAD_CFM", 6000);
}
if(!defined("UPLOAD_CM")) {
	define("UPLOAD_CM", 12000);
}
if(!defined("UPLOAD_CMD")) {
	define("UPLOAD_CMD", 12000);
}
if(!defined("UPLOAD_ALL")) {
	define("UPLOAD_ALL", 240000);
}

if(!defined("VERSION")) {
	define("VERSION", "7.7");
}
if(!defined("VERSION_ADMIN_STYLE")) {
	define("VERSION_ADMIN_STYLE", "1.5");
}
if(!defined("INTVERSION")) {
	define("INTVERSION", "70745");
}
if(!defined("DB_VERSION")) {
	define("DB_VERSION", "6.5");
}
if(!defined("LEVEL_CREATOR")) {
	define("LEVEL_CREATOR", 5);
}
if(!defined("LEVEL_CUSTOMER")) {
	define("LEVEL_CUSTOMER", 4);
}
if(!defined("LEVEL_ADMIN")) {
	define("LEVEL_ADMIN", 3);
}
if(!defined("LEVEL_MODER")) {
	define("LEVEL_MODER", 2);
}
if(!defined("LEVEL_USER")) {
	define("LEVEL_USER", 1);
}
if(!defined("LEVEL_GUEST")) {
	define("LEVEL_GUEST", 0);
}
if(!defined("S_TIME_VIEW")) {
	define("S_TIME_VIEW", "d-m-Y H:i:s");
}

// Shop
if(!defined("SERVER_MODULES")) {
	define("SERVER_MODULES", "http://shop.killer.pp.ua/");
}
if(!defined("LANGUAGE_SUPPORT_SERVICE")) {
	define("LANGUAGE_SUPPORT_SERVICE", "https://raw.githubusercontent.com/killserver/ForCardinal/master/translateSupport.serialize");
}
if(!defined("ROUTE_GET_URL")) {
	define("ROUTE_GET_URL", "PATH_INFO");
}
if(!defined("ADMINCP_DIRECTORY")) {
	define("ADMINCP_DIRECTORY", "admincp.php");
}

$config = array_merge($config, array(
	"lang" => "ru",
	"git_install" => true,
	"git_beta" => true,
	"rewrite" => true,
	"gzip" => false,
	"activeCache" => false,
	"mobyActive" => false,
	"default_http_mobyhost" => "",
	"uses" => array(
		"Mobile Detect" => "2.8.28",
		"PHPMailer" => "6.0.2",
		"PEAR" => "1.10.5",
		"Archive Tar" => "1.1",
	),
	"logoAdminMain" => "assets/xenon/images/logo@2x.png",
	"logoAdminMobile" => "assets/xenon/images/logo-collapsed@2x.png",
	"defaultAdminSkin" => " ",
	"accessChangeSkin" => "true",
));

?>