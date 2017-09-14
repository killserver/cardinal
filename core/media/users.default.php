<?php
if(!defined("IS_CORE")) {
	echo "403 ERROR";
	die();
}

$users = array_merge($users, array(
	"admin" => array(
		"username" => "admin",
		"pass" => User::create_pass("1q2we3r4t5y6u"),
		"admin_pass" => cardinal::create_pass("1q2we3r4t5y6u"),
		"level" => LEVEL_CREATOR,
		"avatar" => "http://img2.wikia.nocookie.net/__cb20130512094126/sword-art-online/pl/images/thumb/a/a4/Akihiko_Kayaba.png/500px-Akihiko_Kayaba.png",
	),
));