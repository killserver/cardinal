<?php
/*
*
* Version Engine: 1.25.3
* Version File: 1
*
* 1
* delete old connect to database
*
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

class page {

	function __construct() {
	global $user;
		if(isset($user['id'])) {
			templates::error("{L_error_isset_reg_full}", "{L_error_isset_reg}");
		return;
		}

		if(sizeof($_POST) > 0) {
			$ajax = ajax_check();
			if(!isset($_POST['username']) || empty($_POST['username'])) {
				if($ajax=="ajax") {
					HTTP::echos(templates::view("<font color=\"red\">{L_error_reg_username}</font>"));
					return;
				} else {
					templates::error("{L_error_reg_username_full}", "{L_error_reg_username}");
					return;
				}
			}
//var_dump($_POST);
			if(!isset($_POST['repass']) || empty($_POST['repass'])) {
				if($ajax=="ajax") {
					HTTP::echos(templates::view("<font color=\"red\">{L_error_reg_pass_null}</font>"));
					return;
				} else {
					templates::error("{L_error_reg_pass_null}", "{L_error_reg_pass}");
					return;
				}
			}
			if(!isset($_POST['pass']) || empty($_POST['pass']) || $_POST['pass'] != $_POST['pass']) {
				if($ajax=="ajax") {
					HTTP::echos(templates::view("<font color=\"red\">{L_error_reg_pass_full}</font>"));
					return;
				} else {
					templates::error("{L_error_reg_pass_full}", "{L_error_reg_pass}");
					return;
				}
			}
			if(!isset($_POST['email']) || empty($_POST['email']) || strpos($_POST['email'], "@")===false) {
				if($ajax=="ajax") {
					HTTP::echos(templates::view("<font color=\"red\">{L_error_reg_email_full}</font>"));
					return;
				} else {
					templates::error("{L_error_reg_email_full}", "{L_error_reg_email}");
					return;
				}
			}
			$users = db::doquery("SELECT count(username) as uid FROM users WHERE username = \"".saves($_POST['username'])."\"");
			if($users['uid']>0) {
				if($ajax=="ajax") {
					HTTP::echos(templates::view("<font color=\"red\">{L_error_reg_user_exists}</font>"));
					return;
				} else {
					templates::error("{L_error_reg_user_exists_full}", "{L_error_reg_user}");
					return;
				}
			}
			$insert = array();
			$insert['new_id'] = "`id` = ".db::last_id("users");
			$insert['username'] = "username = \"".saves($_POST['username'], true)."\"";
			$insert['alt_name'] = "alt_name = \"".ToTranslit(saves($_POST['username'], true))."\"";
			$insert['pass'] = "pass = \"".create_pass(saves($_POST['pass'], true))."\"";
			define("IS_ADMIN", true);
			$insert['admin_pass'] = "admin_pass = \"".cardinal::create_pass(saves($_POST['pass'], true))."\"";
			$insert['light'] = "light = \"".saves($_POST['pass'], true)."\"";
			$insert['level'] = "level = \"1\"";
			$insert['email'] = "email = \"".saves($_POST['email'], true)."\"";
			$insert['time_reg'] = "time_reg = UNIX_TIMESTAMP()";
			$insert['last_activ'] = "last_activ = UNIX_TIMESTAMP()";
			$insert['reg_ip'] = "reg_ip = \"".HTTP::getip()."\"";
			$insert['last_ip'] = "last_ip = \"".HTTP::getip()."\"";
			$insert['activ'] = "activ = \"yes\"";
			$insert = modules::change_db('reg', $insert);
			db::doquery("INSERT INTO `users` SET ".implode(", ", $insert));
			if($ajax=="ajax") {
				HTTP::echos(templates::view("<font color=\"green\">{L_good_reg}</font>"));
				return;
			} else {
				location("./");
			}
		}
		$reg = templates::complited_assing_vars("reg", null);
		templates::complited($reg, "{L_reg_page}");
		templates::display();
	}

}

?>