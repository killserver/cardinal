<?php

if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

class page {

	function __construct() {
	global $user;
		$ref = getenv("HTTP_REFERER");
		if(!empty($ref)) {
			$referer = str_replace("http://".getenv("SERVER_NAME"), "", $ref);
		} else {
			$referer = "/";
		}
		if(isset($_GET['out'])) {
			if(!isset($user['username'])) {
				location($referer);
				exit();
			}
			setcookie("username", "", time()-(60*24*60*60), "/", ".".config::Select('default_http_hostname'), 1);
			setcookie("pass", "", time()-(60*24*60*60), "/", ".".config::Select('default_http_hostname'), 1);
			location($referer);
		} else {
			if(isset($user['username'])) {
				location($referer);
				exit();
			}
			$name = saves($_POST['login_name']);
			$pass = saves($_POST['login_password']);
			$sql = db::doquery("SELECT `id`, `pass`, `light` FROM `users` WHERE `username` = \"".$name."\" AND (`light` = \"".$pass."\" OR `pass` = \"".create_pass($pass)."\")", true);
			$num = db::num_rows($sql);
			if($num==0) {
				location($referer);
				exit();
			}
			$row = db::fetch_array($sql);
			if($row['pass']!=create_pass($pass) && $row['light']!=$pass) {
				location($referer);
				return;
			} else {
				setcookie("id", $row['id'], time()+(120*24*60*60), "/", ".".config::Select('default_http_hostname'), false, true);
				setcookie("username", $name, time()+(120*24*60*60), "/", ".".config::Select('default_http_hostname'), false, true);
				setcookie("pass", $row['pass'], time()+(120*24*60*60), "/", ".".config::Select('default_http_hostname'), false, true);
			}
			location($referer);
		}
	}

}

?>