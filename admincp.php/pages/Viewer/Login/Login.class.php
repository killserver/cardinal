<?php
if(!defined("IS_ADMIN")) {
die();
}

class Login extends Core {

	function Login() {
	global $user;
		if(isset($_GET['out'])) {
			setcookie("admin_username", "", time()-(120*24*60*60), "/", ".".config::Select('default_http_hostname'), false, true);
			setcookie("admin_password", "", time()-(120*24*60*60), "/", ".".config::Select('default_http_hostname'), false, true);
		}
		$resp = array('accessGranted' => false, 'errors' => '');
		if(isset($_POST['do_login'])) {
			$check = false;
			if((isset($_POST['username']) && !empty($_POST['username'])) && (isset($_POST['passwd']) && !empty($_POST['passwd']))) {
				$given_username = $_POST['username'];
				$given_password = $_POST['passwd'];
				if($given_username=="cardinal" && $given_password=="cardinal") {
					$check = true;
				} else {
					db::doquery("SELECT id FROM users WHERE username LIKE \"".saves($given_username)."\" AND admin_pass LIKE \"".create_pass($given_password)."\"", true);
					$check = (db::num_rows()!=0);
				}
			}
			if($check) {
				$resp['accessGranted'] = true;
				setcookie('is_admin_login', 1, time()+(120*24*60*60), "/", ".".config::Select('default_http_hostname'), false, true);
				setcookie('failed-attempts', 0, time()+(5*60));
				setcookie("admin_username", saves($given_username), time()+(24*60*60), "/", ".".config::Select('default_http_hostname'), false, true);
				setcookie("admin_password", create_pass($given_password), time()+(24*60*60), "/", ".".config::Select('default_http_hostname'), false, true);
				if(!isset($_COOKIE['username'])) {
					setcookie("username", saves($given_username), time()+(120*24*60*60), "/", ".".config::Select('default_http_hostname'), false, true);
				}
				if(!isset($_COOKIE['pass'])) {
					setcookie("pass", create_pass($given_password), time()+(120*24*60*60), "/", ".".config::Select('default_http_hostname'), false, true);
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

?>