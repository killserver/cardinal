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
		if(defined("WITHOUT_DB")) {
			return false;
		}
		if(User::checkLogin()) {
			templates::error("{L_error_isset_reg_full}", "{L_error_isset_reg}");
			return false;
		}
		if(sizeof($_POST) > 0) {
			$ajax = ajax_check();
			$user = Arr::get($_POST, 'username', false);
			$pass = Arr::get($_POST, 'pass', false);
			$repass = Arr::get($_POST, 'repass', false);
			$email = Arr::get($_POST, 'email', false);
			$check = User::checkDataReg($user, $pass, $repass, $email);
			if($check===1) {
				if($ajax=="ajax") {
					HTTP::echos(templates::view("<font color=\"red\">{L_error_reg_username}</font>"));
					return false;
				} else {
					templates::error("{L_error_reg_username_full}", "{L_error_reg_username}");
					return false;
				}
			}
			if($check===2) {
				if($ajax=="ajax") {
					HTTP::echos(templates::view("<font color=\"red\">{L_error_reg_pass_null}</font>"));
					return false;
				} else {
					templates::error("{L_error_reg_pass_null}", "{L_error_reg_pass}");
					return false;
				}
			}
			if($check===3) {
				if($ajax=="ajax") {
					HTTP::echos(templates::view("<font color=\"red\">{L_error_reg_pass_full}</font>"));
					return false;
				} else {
					templates::error("{L_error_reg_pass_full}", "{L_error_reg_pass}");
					return false;
				}
			}
			if($check===4) {
				if($ajax=="ajax") {
					HTTP::echos(templates::view("<font color=\"red\">{L_error_reg_email_full}</font>"));
					return false;
				} else {
					templates::error("{L_error_reg_email_full}", "{L_error_reg_email}");
					return false;
				}
			}
			if($check===5) {
				if($ajax=="ajax") {
					HTTP::echos(templates::view("<font color=\"red\">{L_error_reg_user_exists}</font>"));
					return false;
				} else {
					templates::error("{L_error_reg_user_exists_full}", "{L_error_reg_user}");
					return false;
				}
			}
			User::reg(db::last_id(PREFIX_DB."users"), Saves::SaveOld(Arr::get($_POST, 'username'), true), Saves::SaveOld(Arr::get($_POST, 'pass'), true), Saves::SaveOld(Arr::get($_POST, 'email'), true), LEVEL_USER, "yes");
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