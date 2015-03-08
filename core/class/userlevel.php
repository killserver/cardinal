<?php

class userlevel {

	static function all() {
		if(!modules::init_cache()->exists("userlevels")) {
			$row = modules::init_db()->select_query("SELECT * FROM userlevels ORDER BY id ASC");
			modules::init_cache()->set("userlevels", $row);
		} else {
			$row = modules::init_cache()->get("userlevels");
		}//$row = modules::init_db()->select_query("SELECT * FROM userlevels ORDER BY id ASC");
	return $row;
	}

	static function get($get) {
	global $user;
		$all = self::all();
		if(!isset($user['level'])) {
			$user['level'] = modules::get_config("guest_level");
		}
		if(isset($all[$user['level']]["access_".$get]) && $all[$user['level']]["access_".$get] == "yes") {
			return true;
		} elseif(!isset($all[$user['level']]["access_".$get]) || $all[$user['level']]["access_".$get] == "no") {
			return false;
		} else {
			return false;
		}
	}

	private static function define($array) {
		$def = array();
		for($i=0;$i<sizeof($array);$i++) {
			$array[$i]['id'] -= 1;
			$def[$array[$i]['alt_name']] = $array[$i];
		}
	return $def;
	}

	static function check($get, $access=null) {
	global $user;
		$all = self::all();
		$all = self::define($all);
		if(!isset($user['level'])) {
			$user['level'] = modules::get_config("guest_level");
		}
		if((isset($all[$get]['id']) && $user['level'] == $all[$get]['id']) && (isset($all[$get]['access_'.$access]) && $all[$get]['access_'.$access] == "yes")) {
			return "true";
		} else {
			return "false";
		}
	}

	static function set($id, $set, $data) {
		if(is_bool($data)) {
			if($data) {
				$data = "yes";
			} else {
				$data = "no";
			}
		}
		if(isset($data) && !empty($data)) {
			$data = "yes";
		} elseif(!isset($data) || empty($data)) {
			$data = "no";
		}
		modules::init_db()->doquery("UPDATE userlevels SET `".$set."` = ".$data." WHERE id = ".$id);
		modules::init_cache()->delete("userlevels");
	}

}

?>