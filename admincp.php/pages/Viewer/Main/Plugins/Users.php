<?php

class Main_Users extends Main {

	public function Main_Users() {
	global $users;
		if(defined("WITHOUT_DB")) {
			templates::assign_var("isUsers", "0");
		} else {
			templates::assign_var("isUsers", "1");
			$vid = db::doquery("SELECT count(id) as id FROM `users` WHERE activ = \"yes\"");
			$users = $vid['id'];
			templates::assign_vars(array(
				"users" => $users,
			));
		}
	}

}

?>