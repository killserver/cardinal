<?php
if(!defined("IS_CORE")) {
	echo "403 ERROR";
	die();
}

$userlevels = array_replace($userlevels, array(
	"3" => array(
		"access_admin" => "yes",
		"access_site" => "yes",
	),
	"2" => array(
		"access_admin" => "yes",
		"access_site" => "yes",
	),
	"1" => array(
		"access_admin" => "no",
		"access_site" => "yes",
	),
	"0" => array(
		"access_admin" => "no",
		"access_site" => "yes",
	),
));