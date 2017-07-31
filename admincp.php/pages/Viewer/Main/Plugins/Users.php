<?php

class Main_Users extends Main {

	public function __construct() {
	global $users;
		templates::assign_var("isUsers", "1");
		if(!userlevel::get("users")) {
			return false;
		}
		$userCount = sizeof($users);
		if(isset($db) && !is_bool($db) && method_exists($db, "connected") && $db->connected() && method_exists($db, "getTable") && $db->getTable("users")) {
			$vid = db::doquery("SELECT count(`id`) AS `id` FROM `".PREFIX_DB."users` WHERE `activ` LIKE \"yes\"");
			$userCount += (isset($vid['id']) && $vid['id']>0 ? $vid['id'] : 0);
		}
		if(defined("WITHOUT_DB") && $userCount>2) {
			templates::assign_var("isUsers", "0");
			templates::assign_vars(array(
				"users" => $userCount,
			));
		} elseif(!defined("WITHOUT_DB")) {
			templates::assign_var("isUsers", "0");
			templates::assign_vars(array(
				"users" => $userCount,
			));
		}
	}

}

?>