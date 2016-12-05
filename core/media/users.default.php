<?php
if(!defined("IS_CORE")) {
	echo "403 ERROR";
	die();
}

$users = array_merge($users, array(
	"admin" => array(
		"username" => "admin",
		"pass" => create_pass("1q2we3r4t5y6u"),
		"admin_pass" => cardinal::create_pass("1q2we3r4t5y6u"),
		"level" => LEVEL_ADMIN,
	),
));