<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die;
}

define("VERSION", "1.15.3");
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
	"sleep_parser" => true, //будет-ли парсер "спать" при большущей скорости?
	"sleep_max" => 10*1024, //при каких максимальных скоростях будет "спать" парсер
	"charset" => "utf-8",

	'sea_facer' => "no", //yes/no
	'cutworld' => "15",
	'site_ignor' => "",
	"tlimit" => 30,
	"word_filter" => "",

	"vk_access" => array(),
	"vk_id" => "",


	"pogoda_v" => 3, //версия просмотра погоды в админке

	"ads_tube" => "0",
);

?>