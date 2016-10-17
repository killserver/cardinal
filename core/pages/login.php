<?php
/*
 *
 * @version 1.25.7-a4
 * @copyright 2014-2015 KilleR for Cardinal Engine
 *
 * Version Engine: 1.25.7-a4
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
 * 7.6
 * fix timeout redirect
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
			$referer = "{C_default_http_local}";
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
				location($referer, 3, false);
				templates::error("{L_login[authorized]}");
				return;
			}
			$name = Saves::SaveOld(Arr::get($_POST, 'login_name'));
			$pass = Saves::SaveOld(Arr::get($_POST, 'login_password'));
			$sql = db::doquery("SELECT `id`, `pass`, `light` FROM `users` WHERE `username` = \"".$name."\" AND (`light` = \"".$pass."\" OR `pass` = \"".create_pass($pass)."\")", true);
			$num = db::num_rows($sql);
			if($num == 0) {
				location($referer, 3, false);
				templates::error("{L_login[notFound]}");
				exit();
			}
			$row = db::fetch_array($sql);
			if($row['pass'] != create_pass($pass) && $row['light'] != $pass) {
				location($referer, 3, false);
				templates::error("{L_login[notCorrect]}");
				return;
			} else {
				HTTP::set_cookie("id", $row['id']);
				HTTP::set_cookie(COOK_USER, $name);
				HTTP::set_cookie(COOK_PASS, $row['pass']);
			}
			location($referer, 3, false);
			templates::error("{L_login[correct]}");
			return false;
		}
	}

}

?>