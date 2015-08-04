<?php
/*
*
* Version Engine: 1.25.5b1
* Version File: 7
*
* 7.1
* add support installer cookie
* 7.2
* add support interface on errors in login
*
*/
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
			setcookie(COOK_USER, "", time()-(60*24*60*60), "/", ".".config::Select('default_http_hostname'), 1);
			setcookie(COOK_PASS, "", time()-(60*24*60*60), "/", ".".config::Select('default_http_hostname'), 1);
			location($referer);
		} else {
			if(isset($user['username'])) {
				location($referer, 10, false);
				templates::error("Вы уже авторизированны на сайте");
				exit();
			}
			$name = saves($_POST['login_name']);
			$pass = saves($_POST['login_password']);
			$sql = db::doquery("SELECT `id`, `pass`, `light` FROM `users` WHERE `username` = \"".$name."\" AND (`light` = \"".$pass."\" OR `pass` = \"".create_pass($pass)."\")", true);
			$num = db::num_rows($sql);
			if($num==0) {
				location($referer, 10, false);
				templates::error("Данный пользователь не найден на сайте, либо Вы ввели неверные данные для входа");
				exit();
			}
			$row = db::fetch_array($sql);
			if($row['pass']!=create_pass($pass) && $row['light']!=$pass) {
				location($referer, 10, false);
				templates::error("Не верный пароль для авторизации");
				exit();
				return;
			} else {
				setcookie("id", $row['id'], time()+(120*24*60*60), "/", ".".config::Select('default_http_hostname'), false, true);
				setcookie(COOK_USER, $name, time()+(120*24*60*60), "/", ".".config::Select('default_http_hostname'), false, true);
				setcookie(COOK_PASS, $row['pass'], time()+(120*24*60*60), "/", ".".config::Select('default_http_hostname'), false, true);
			}
			location($referer);
		}
	}

}

?>