<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

$config = array_merge($config, array(
	"db" => array(
		"host" => "localhost",
		"port" => "3306",
		"user" => "",
		"pass" => "",
		"db" => "",
		"charset" => "utf8",
	),
));

?>