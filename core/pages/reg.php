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
		if(defined("WITHOUT_DB")) {
			return false;
		}
		if(isset($user['id'])) {
			templates::error("{L_error_isset_reg_full}", "{L_error_isset_reg}");
		return;
		}

		if(sizeof($_POST) > 0) {
			$ajax = ajax_check();
			if(Arr::get($_POST, 'username', false)) {
				if($ajax=="ajax") {
					HTTP::echos(templates::view("<font color=\"red\">{L_error_reg_username}</font>"));
					return false;
				} else {
					templates::error("{L_error_reg_username_full}", "{L_error_reg_username}");
					return false;
				}
			}
			if(Arr::get($_POST, 'repass', false)) {
				if($ajax=="ajax") {
					HTTP::echos(templates::view("<font color=\"red\">{L_error_reg_pass_null}</font>"));
					return false;
				} else {
					templates::error("{L_error_reg_pass_null}", "{L_error_reg_pass}");
					return false;
				}
			}
			if(Arr::get($_POST, 'pass', false) || !Validate::equals(Arr::get($_POST, 'pass', false), Arr::get($_POST, 'repass', false))) {
				if($ajax=="ajax") {
					HTTP::echos(templates::view("<font color=\"red\">{L_error_reg_pass_full}</font>"));
					return false;
				} else {
					templates::error("{L_error_reg_pass_full}", "{L_error_reg_pass}");
					return false;
				}
			}
			if(Arr::get($_POST, 'email', false) || Validate::email(Arr::get($_POST, 'email'))) {
				if($ajax=="ajax") {
					HTTP::echos(templates::view("<font color=\"red\">{L_error_reg_email_full}</font>"));
					return false;
				} else {
					templates::error("{L_error_reg_email_full}", "{L_error_reg_email}");
					return false;
				}
			}
			$users = db::doquery("SELECT COUNT(`username`) AS `uid` FROM `users` WHERE `username` LIKE \"".Saves::SaveOld(Arr::get($_POST, 'username', false))."\"");
			if($users['uid'] > 0) {
				if($ajax=="ajax") {
					HTTP::echos(templates::view("<font color=\"red\">{L_error_reg_user_exists}</font>"));
					return false;
				} else {
					templates::error("{L_error_reg_user_exists_full}", "{L_error_reg_user}");
					return false;
				}
			}
			$insert = array();
			$insert['new_id'] = "`id` = ".db::last_id("users");
			$insert['username'] = "`username` = \"".Saves::SaveOld(Arr::get($_POST, 'username'), true)."\"";
			$insert['alt_name'] = "`alt_name` = \"".ToTranslit(Saves::SaveOld(Arr::get($_POST, 'username'), true))."\"";
			$insert['pass'] = "`pass` = \"".create_pass(Saves::SaveOld(Arr::get($_POST, 'pass'), true))."\"";
			define("IS_ADMIN", true);
			$insert['admin_pass'] = "`admin_pass` = \"".cardinal::create_pass(Saves::SaveOld(Arr::get($_POST, 'pass'), true))."\"";
			$insert['light'] = "`light` = \"".Saves::SaveOld(Arr::get($_POST, 'pass'), true)."\"";
			$insert['level'] = "`level` = \"1\"";
			$insert['email'] = "`email` = \"".Saves::SaveOld(Arr::get($_POST, 'email'), true)."\"";
			$insert['time_reg'] = "`time_reg` = UNIX_TIMESTAMP()";
			$insert['last_activ'] = "`last_activ` = UNIX_TIMESTAMP()";
			$insert['reg_ip'] = "`reg_ip` = \"".HTTP::getip()."\"";
			$insert['last_ip'] = "`last_ip` = \"".HTTP::getip()."\"";
			$insert['activ'] = "`activ` = \"yes\"";
			$insert = modules::change_db('reg', $insert);
			db::doquery("INSERT INTO `users` SET ".implode(", ", $insert));
			if($ajax=="ajax") {
				HTTP::echos(templates::view("<font color=\"green\">{L_good_reg}</font>"));
				return false;
			} else {
				location("{C_default_http_local}");
			}
		}
		$reg = templates::complited_assing_vars("reg", null);
		templates::complited($reg, "{L_reg_page}");
		templates::display();
	}

}

?>