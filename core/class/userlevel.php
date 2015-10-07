<?php
/*
 *
 * @version 2015-09-30 13:30:44 1.25.6-rc3
 * @copyright 2014-2015 KilleR for Cardinal Engine
 *
 * Version Engine: 1.25.6-rc3
 * Version File: 1
 *
 * 1.3
 * delete $user and rebuild him on module system
 *
*/
class userlevel {

	public static function all() {
		if(!modules::init_cache()->exists("userlevels")) {
			$row = modules::init_db()->select_query("SELECT * FROM userlevels ORDER BY id ASC");
			modules::init_cache()->set("userlevels", $row);
		} else {
			$row = modules::init_cache()->get("userlevels");
		}//$row = modules::init_db()->select_query("SELECT * FROM userlevels ORDER BY id ASC");
	return $row;
	}

	public static function get($get) {
		$all = self::all();
		$level = modules::get_user('level');
		if(is_bool($level) || empty($level)) {
			$level = modules::get_config("guest_level");
		}
		if(isset($all[$level]["access_".$get]) && $all[$level]["access_".$get] == "yes") {
			return true;
		} elseif(!isset($all[$level]["access_".$get]) || $all[$level]["access_".$get] == "no") {
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

	public static function check($get, $access=null) {
		$all = self::all();
		$all = self::define($all);
		$level = modules::get_user('level');
		if(is_bool($level) || empty($level)) {
			$level = modules::get_config("guest_level");
		}
		if((isset($all[$get]['id']) && $level == $all[$get]['id']) && (isset($all[$get]['access_'.$access]) && $all[$get]['access_'.$access] == "yes")) {
			return "true";
		} else {
			return "false";
		}
	}

	public static function set($id, $set, $data) {
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