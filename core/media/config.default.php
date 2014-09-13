<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die;
}

define("VERSION", "1.19.1");
define("BLOCK", 15*1024);
define("LEVEL_MODER", 2);
define("LEVEL_USER", 1);
define("LEVEL_GUEST", 0);


$config = array(
	"cache" => array(
		"type" => 3,
		"server" => "localhost",
		"port" => 11211,
	),
	"skins" => array(
		"skins" => "Style",
		"mobile" => "mobile",
	),
	"link" => array(
		"reg" => "/?reg",
		"lost" => "/?lost",
		"login" => "/?login",
		"logout" => "/?login&out",
		"add" => "/?add",
	),
//минификация
	"tpl_minifier" => false,
	"gzip" => "yes",
	"gzip_output" => true,
	"js_min" => true,

	"default_http_hostname" => "online-killer.com",
	"default_http_host" => "http://online-killer.com/",
	'lang' => "ru",
	"charset" => "utf-8",
);

?>