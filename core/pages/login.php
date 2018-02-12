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
		if(defined("WITHOUT_DB")) {
			return false;
		}
		$ref = HTTP::getServer("HTTP_REFERER");
		if(!empty($ref)) {
			$referer = str_replace("http://".HTTP::getServer("SERVER_NAME"), "", $ref);
		} else {
			$referer = "{C_default_http_local}";
		}
		if(isset($_GET['out'])) {
			if(!User::checkLogin()) {
				HTTP::Location(templates::view($referer), 0, true, 302);
				return false;
			}
			User::logout();
			HTTP::Location(templates::view($referer), 0, true, 302);
		} else {
			if(User::checkLogin()) {
				HTTP::Location(templates::view($referer), 3, false, 302);
				templates::error("{L_login[authorized]}");
				return false;
			}
			$name = Saves::SaveOld(Arr::get($_POST, 'login_name'));
			$pass = Saves::SaveOld(Arr::get($_POST, 'login_password'));
			$login = User::login($name, $pass);
			if($login===1) {
				HTTP::Location(templates::view($referer), 3, false, 302);
				templates::error("{L_login[notFound]}");
				return false;
			} else if($login===2) {
				HTTP::Location(templates::view($referer), 3, false, 302);
				templates::error("{L_login[notCorrect]}");
				return false;
			}
			HTTP::Location(templates::view($referer), 3, false, 302);
			templates::error("{L_login[correct]}");
			return false;
		}
	}

}

?>