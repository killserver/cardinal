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

if(!defined("DEBUG_MODE_ONLY_DEBUG")) {
	define("DEBUG_MODE_ONLY_DEBUG", 1);
}
// Debug
if(!defined("DEBUG_MEMORY")) {
	define("DEBUG_MEMORY", 1);
}
if(!defined("DEBUG_TIME")) {
	define("DEBUG_TIME", 2);
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

if(!defined("VERSION")) {
	define("VERSION", "10.4");
}
if(!defined("VERSION_ADMIN_STYLE")) {
	define("VERSION_ADMIN_STYLE", "1.9");
}
if(!defined("INTVERSION")) {
	define("INTVERSION", "104007");
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

if(!defined("LANGUAGE_SUPPORT_SERVICE")) {
	define("LANGUAGE_SUPPORT_SERVICE", "https://raw.githubusercontent.com/killserver/ForCardinal/master/translateSupport.serialize");
}
if(!defined("ROUTE_GET_URL")) {
	define("ROUTE_GET_URL", "REQUEST_URI");
}

$config = array_merge($config, array(
	"api_key" => "1234567890",
	"hosting" => true,//true - hosting, false - vps/vds
	"ip_test_shab" => array(
		//"127.0.0.1",
	),
	"date_timezone" => 'Europe/Kiev',
	"cache" => array(
		"type" => CACHE_NONE,
		"server" => "localhost",
		"port" => 11211,
		"login" => "",
		"pass" => "",
		"path" => "/",
	),
	"skins" => array(
		"skins" => "main",
		"admincp" => "xenon",
		"test_shab" => "",
		"mobile" => "",
	),
//минификация
	"tpl_minifier" => true,
	"gzip" => "yes",
	"gzip_output" => true,
	
	"viewport" => "width=device-width, initial-scale=1, shrink-to-fit=no, viewport-fit=cover, minimal-ui",

	"default_http_local" => "/",
	"default_http_hostname" => "online-killer.pp.ua",
	"default_http_host" => HTTP::$protocol."://online-killer.pp.ua/",
	"charset" => "utf-8",
	"FullMenu" => true,
	"manifestCache" => false,
	"guest_level" => LEVEL_GUEST,
	"db" => array(
		"driver" => "db_mysqli",
	),

	"lang" => "ru",
	"logs" => ERROR_FILE,
	"git_install" => true,
	"git_beta" => true,
	"rewrite" => true,
	"gzip" => false,
	"mobyActive" => false,
	"default_http_mobyhost" => "",
	"new_method_uri" => true,
	"uses" => array(
		"Mobile Detect" => "2.8.28",
		"PHPMailer" => "6.0.2",
		"PEAR" => "1.10.5",
		"Archive Tar" => "1.1",
	),
	"htmlPrefix" => array(
		"og" => "http://ogp.me/ns",
	),
	"logoAdminMain" => "assets/xenon/images/logo.svg",
	"logoAdminMainWidth" => "110",
	"logoAdminMobile" => "assets/xenon/images/logo-collapsed.svg",
	"logoAdminMobileWidth" => "40",
	"defaultAdminSkin" => " ",
	"accessChangeSkin" => "true",
	"mainPageAdmin" => "?pages=main",
	"pluginsForEditor" => array(
		"advlist",
		"anchor",
		"autolink",
		"code",
		"contextmenu",
		"charmap",
		"fullscreen",
		"lists",
		"link",
		"localautosave",
		"media",
		"image",
		"imagetools",
		"paste",
		"responsivefilemanager",
		"visualblocks",
	),
));

?>