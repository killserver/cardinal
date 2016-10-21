<?php

class Main_Users extends Main {

	public function Main_Users() {
		$vid = db::doquery("SELECT COUNT(`id`) AS `id` FROM `users` WHERE `activ` LIKE \"yes\"");
		$users = $vid['id'];
		templates::assign_vars(array(
			"users" => $users,
		));
	}

}

?>