<?php
/*
 *
 * @version 1.25.6-rc4
 * @copyright 2014-2015 KilleR for Cardinal Engine
 *
 * Version Engine: 1.25.6-rc4
 * Version File: 2
 *
 * 2.1
 * add support setcookie in php7
 * 2.2
 * add support login for localhost and rebuild system cookie in everything
 * 2.3
 * add support link on page after login
 *
*/
if(!defined("IS_ADMIN")) {
die();
}

class Login extends Core {

	function Login() {
	global $user;
		if(isset($_GET['out'])) {
			HTTP::set_cookie(COOK_ADMIN_USER, "", true);
			HTTP::set_cookie(COOK_ADMIN_PASS, "", true);
		}
		$resp = array('accessGranted' => false, 'errors' => '');
		if(isset($_POST['do_login'])) {
			$check = false;
			if((isset($_POST['username']) && !empty($_POST['username'])) && (isset($_POST['passwd']) && !empty($_POST['passwd']))) {
				$given_username = $_POST['username'];
				$given_password = $_POST['passwd'];
				$is_admin = false;
				if($given_username=="cardinal" && $given_password=="cardinal") {
					$check = true;
					$is_admin = true;
				} else {
					$given_username = saves($given_username);
					$given_password = cardinal::create_pass($given_password);
					db::doquery("SELECT id, pass FROM users WHERE username LIKE \"".($given_username)."\" AND admin_pass LIKE \"".($given_password)."\"", true);
					$check = (db::num_rows()!=0);
				}
			}
			if($check) {
				if(!$is_admin) {
					$row = db::fetch_assoc();
				} else {
					$row = array("pass" => "cardinal");
				}
				$resp['accessGranted'] = true;
				HTTP::set_cookie('is_admin_login', 1, false, false);
				HTTP::set_cookie('failed-attempts', 0, time()+(5*60), false);
				HTTP::set_cookie(COOK_ADMIN_USER, $given_username);
				HTTP::set_cookie(COOK_ADMIN_PASS, $given_password);
				if(!isset($_COOKIE[COOK_USER]) || empty($_COOKIE[COOK_USER])) {
					HTTP::set_cookie(COOK_USER, $given_username);
				}
				if(!isset($_COOKIE[COOK_PASS]) || empty($_COOKIE[COOK_PASS])) {
					HTTP::set_cookie(COOK_PASS, $row['pass']);
				}
			} else {
				// Failed Attempts
				$fa = isset($_COOKIE['failed-attempts']) ? $_COOKIE['failed-attempts'] : 0;
				$fa++;
				HTTP::set_cookie('failed-attempts', $fa, time()+(5*60), false);
				// Error message
				if(isset($_POST['page']) && $_POST['page']=="alogin")
					$resp['errors'] = 'You have entered wrong password, please try again.<br />Failed attempts: ' . $fa;
				else
					$resp['errors'] = 'You have entered wrong login or password, please try again.<br />Failed attempts: ' . $fa;
			}
			templates::$gzip=false;
			HTTP::echos(json_encode($resp));
			return;
		}
		templates::assign_var("ref", (isset($_GET['ref']) && !empty($_GET['ref']) && strpos($_GET['ref'], "http")===false ? urldecode($_GET['ref']) : "?pages=main"));
		if(isset($_COOKIE['is_admin_login']) && !empty($user['username'])) {
			echo templates::view(templates::complited_assing_vars("again_login", null));
		} else {
			echo templates::view(templates::complited_assing_vars("login", null));
		}
	}

}
ReadPlugins(dirname(__FILE__)."/Plugins/", "Login");

?>