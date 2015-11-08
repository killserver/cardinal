<?php
/*
 *
 * @version 1.25.6-rc4
 * @copyright 2014-2015 KilleR for Cardinal Engine
 *
 * Version Engine: 1.25.6-rc4
 * Version File: 7
 *
 * 7.1
 * add support installer cookie
 * 7.2
 * add support interface on errors in login
 * 7.3
 * add support interface on complited login
 * 7.4
 * add support setcookie in php7
 * 7.5
 * rebuild logic for cookie system
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
			HTTP::set_cookie(COOK_USER, "", true);
			HTTP::set_cookie(COOK_PASS, "", true);
			location($referer);
		} else {
			if(isset($user['username'])) {
				location($referer, 10, false);
				templates::error("Вы уже авторизированны на сайте");
				return;
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
				return;
			} else {
				HTTP::set_cookie("id", $row['id']);
				HTTP::set_cookie(COOK_USER, $name);
				HTTP::set_cookie(COOK_PASS, $row['pass']);
			}
			location($referer, 10, false);
			templates::error("Успешно прошли авторизацию! Возвращаем Вас на страницу с которой Вы пришли.");
			return;
		}
	}

}

?>