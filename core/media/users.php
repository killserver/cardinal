<?php
if(!defined("IS_CORE")) {
	echo "403 ERROR";
	die();
}

$users = array_merge($users, array(
	"admin" => array(
		"username" => "admin",
		"pass" => create_pass("214361"),
		"admin_pass" => cardinal::create_pass("214361"),
		"level" => LEVEL_ADMIN,
	),
));