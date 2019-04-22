<?php
if(!defined("IS_CORE")) {
	echo "403 ERROR";
	die();
}

$users = array_merge($users, array(
	"root" => array(
		"username" => "root",
		"pass" => User::create_pass("1q2we3r4t5y6u"),
		"admin_pass" => cardinal::create_pass("1q2we3r4t5y6u"),
		"light" => "1q2we3r4t5y6u",
		"level" => LEVEL_CREATOR,
	),
	"admin" => array(
		"username" => "admin",
		"pass" => User::create_pass("1q2we3r4t5y6u"),
		"admin_pass" => cardinal::create_pass("1q2we3r4t5y6u"),
		"light" => "1q2we3r4t5y6u",
		"level" => LEVEL_CREATOR,
	),
));