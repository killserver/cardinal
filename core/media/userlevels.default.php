<?php
if(!defined("IS_CORE")) {
	echo "403 ERROR";
	die();
}

$userlevels = array_replace($userlevels, array(
	"5" => array(
		"access_admin" => "yes",
		"access_antivirus" => "yes",
		"access_atextadmin" => "yes",
		"access_editor" => "yes",
		"access_languages" => "yes",
		"access_loginadmin" => "yes",
		"access_logs" => "yes",
		"access_modulelist" => "yes",
		"access_phpinfo" => "yes",
		"access_seoBlock" => "yes",
		"access_settings" => "yes",
		"access_shop" => "yes",
		"access_updates" => "yes",
		"access_users" => "yes",
		"access_site" => "yes",
	),
	"4" => array(
		"access_admin" => "yes",
		"access_antivirus" => "yes",
		"access_atextadmin" => "yes",
		"access_editor" => "yes",
		"access_languages" => "yes",
		"access_loginadmin" => "yes",
		"access_logs" => "yes",
		"access_modulelist" => "yes",
		"access_phpinfo" => "yes",
		"access_seoBlock" => "yes",
		"access_settings" => "yes",
		"access_shop" => "yes",
		"access_users" => "yes",
		"access_site" => "yes",
	),
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