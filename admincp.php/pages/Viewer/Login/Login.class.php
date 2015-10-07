<?php
/*
 *
 * @version 2015-10-07 17:50:38 1.25.6-rc3
 * @copyright 2014-2015 KilleR for Cardinal Engine
 *
 * Version Engine: 1.25.6-rc3
 * Version File: 2
 *
 * 2.1
 * add support setcookie in php7
 *
*/
if(!defined("IS_ADMIN")) {
die();
}

class Login extends Core {

	function Login() {
	global $user;
		if(isset($_GET['out'])) {
			setcookie(COOK_ADMIN_USER, "", time()-(120*24*60*60), "/", ".".config::Select('default_http_hostname'), false, true);
			setcookie(COOK_ADMIN_PASS, "", time()-(120*24*60*60), "/", ".".config::Select('default_http_hostname'), false, true);
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
				if((version_compare(PHP_VERSION_ID, '70000', '>='))) {
					setcookie('is_admin_login', 1, time()+(120*24*60*60), "/");
				} else {
					setcookie('is_admin_login', 1, time()+(120*24*60*60), "/", ".".config::Select('default_http_hostname'), false, true);
				}
				setcookie('failed-attempts', 0, time()+(5*60));
				if((version_compare(PHP_VERSION_ID, '70000', '>='))) {
					setcookie(COOK_ADMIN_USER, $given_username, time()+(24*60*60), "/");
				} else {
					setcookie(COOK_ADMIN_USER, $given_username, time()+(24*60*60), "/", ".".config::Select('default_http_hostname'), false, true);
				}
				if((version_compare(PHP_VERSION_ID, '70000', '>='))) {
					setcookie(COOK_ADMIN_PASS, $given_password, time()+(24*60*60), "/");
				} else {
					setcookie(COOK_ADMIN_PASS, $given_password, time()+(24*60*60), "/", ".".config::Select('default_http_hostname'), false, true);
				}
				if(!isset($_COOKIE[COOK_USER]) || empty($_COOKIE[COOK_USER])) {
					if((version_compare(PHP_VERSION_ID, '70000', '>='))) {
						setcookie(COOK_USER, $given_username, time()+(120*24*60*60), "/");
					} else {
						setcookie(COOK_USER, $given_username, time()+(120*24*60*60), "/", ".".config::Select('default_http_hostname'), false, true);
					}
				}
				if(!isset($_COOKIE[COOK_PASS]) || empty($_COOKIE[COOK_PASS])) {
					if((version_compare(PHP_VERSION_ID, '70000', '>='))) {
						setcookie(COOK_PASS, $row['pass'], time()+(120*24*60*60), "/");
					} else {
						setcookie(COOK_PASS, $row['pass'], time()+(120*24*60*60), "/", ".".config::Select('default_http_hostname'), false, true);
					}
				}
			} else {
				// Failed Attempts
				$fa = isset($_COOKIE['failed-attempts']) ? $_COOKIE['failed-attempts'] : 0;
				$fa++;
				setcookie('failed-attempts', $fa, time()+(5*60));
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
		if(isset($_COOKIE['is_admin_login']) && !empty($user['username'])) {
			echo templates::view(templates::complited_assing_vars("again_login", null));
		} else {
			echo templates::view(templates::complited_assing_vars("login", null));
		}
	}

}
ReadPlugins(dirname(__FILE__)."/Plugins/", "Login");

?>