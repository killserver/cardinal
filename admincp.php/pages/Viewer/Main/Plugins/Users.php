<?php

class Main_Users extends Main {

	public function Main_Users() {
	global $users;
		if(defined("WITHOUT_DB")) {
			templates::assign_vars(array(
				"users" => sizeof($users),
			));
		} else {
			$vid = db::doquery("SELECT count(id) as id FROM `users` WHERE activ = \"yes\"");
			$users = $vid['id'];
			templates::assign_vars(array(
				"users" => $users,
			));
		}
	}

}

?>