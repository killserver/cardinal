<?php

class Main_Users extends Main {

	public function __construct() {
	global $users;
		templates::assign_var("isUsers", "0");
		$userCount = sizeof($users);
		if(isset($db) && !is_bool($db) && method_exists($db, "connected") && $db->connected() && method_exists($db, "getTable") && $db->getTable("users")) {
			$vid = db::doquery("SELECT count(`id`) AS `id` FROM `".PREFIX_DB."users` WHERE `activ` LIKE \"yes\"");
			$userCount += (isset($vid['id']) && $vid['id']>0 ? $vid['id'] : 0);
		}
		if($userCount>1) {
			templates::assign_var("isUsers", "1");
			templates::assign_vars(array(
				"users" => $userCount,
			));
		}
	}

}

?>