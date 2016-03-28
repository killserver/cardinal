<?php
/*
 *
 * @version 1.25.6-rc6
 * @copyright 2014-2015 KilleR for Cardinal Engine
 *
 * Version Engine: 1.25.6
 * Version File: 8
 *
 * 8.1
 * add support XXX category
 * 8.2
 * add support skins for admincp
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

define("BLOCK", 4*1024);
define("API_URL", "http://online-killer.pp.ua/api.php");

if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']!='off') {
	$protocol = "https";
} else if(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO']=='https') {
	$protocol = "https";
} else {
	$protocol = "http";
}

$config = array(
	"api_key" => "1234567890",
	"logs" => ERROR_FILE,
	"hosting" => true,//true - hosting, false - vps/vds
	"ip_test_shab" => array(
		"127.0.0.1",
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
	"link" => array(
		"reg" => "/?reg",
		"lost" => "/?lost",
		"login" => "/?login",
		"logout" => "/?login&out",
		"add" => "/?add",
		"recover" => "/?recover",
	),
//минификаци¤
	"tpl_minifier" => true,
	"gzip" => "yes",
	"gzip_output" => true,
	"js_min" => true,
	
	"viewport" => "width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=1",

	"default_http_local" => "/",
	"default_http_hostname" => "online-killer.pp.ua",
	"default_http_host" => $protocol."://online-killer.pp.ua/",
	'lang' => "ru",
	"charset" => "utf-8",

	"ParsePHP" => true,
	"rewrite" => true,
	"guest_level" => "0",
	"db" => array(
		"driver" => "mysql",
	),
);

?>