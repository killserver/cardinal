<?php

class Main_Users extends Main {

	public function Main_Users() {
		$vid = db::doquery("SELECT count(id) as id FROM `users` WHERE activ = \"yes\"");
		$users = $vid['id'];
		templates::assign_vars(array(
			"users" => $users,
		));
	}

}

?>