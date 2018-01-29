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

$config = array_merge($config, array(
	"api_key" => "1234567890",
	"logs" => ERROR_FILE,
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
	
	"viewport" => "width=device-width, initial-scale=1, shrink-to-fit=no, minimal-ui",

	"default_http_local" => "/",
	"default_http_hostname" => "online-killer.pp.ua",
	"default_http_host" => HTTP::$protocol."://online-killer.pp.ua/",
	"charset" => "utf-8",

	"FullMenu" => true,
	"manifestCache" => false,
	"ParsePHP" => false,
	"rewrite" => true,
	"guest_level" => LEVEL_GUEST,
	"db" => array(
		"driver" => "db_mysqli",
	),
));

?>