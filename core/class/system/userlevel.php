<?php
/*
 *
 * @version 1.25.6-rc4
 * @copyright 2014-2015 KilleR for Cardinal Engine
 *
 * Version Engine: 1.25.6-rc4
 * Version File: 1
 *
 * 1.3
 * delete $user and rebuild him on module system
 *
*/

/**
 * Class userlevel
 */
class userlevel {

	private static $cacheAll = array();
	/**
	 * Get all exists user levels in DB
	 * @return array All user levels in DB
     */
	final public static function all() {
		if(is_array(self::$cacheAll) && sizeof(self::$cacheAll)>0) {
			return self::$cacheAll;
		}
		if(defined("WITHOUT_DB")) {
			$userlevels = array();
			if(file_exists(PATH_MEDIA."userlevels.".ROOT_EX)) {
				include_once(PATH_MEDIA."userlevels.".ROOT_EX);
			} else if(file_exists(PATH_MEDIA."userlevels.default.".ROOT_EX)) {
				include_once(PATH_MEDIA."userlevels.default.".ROOT_EX);
			}
			self::$cacheAll = $userlevels;
			return $userlevels;
		}
		if(!cache::Exists("userlevels")) {
			$row = db::select_query("SELECT * FROM `".PREFIX_DB."userlevels` ORDER BY `id` ASC");
			cache::Set("userlevels", $row);
		} else {
			$row = cache::Get("userlevels");
		}
		self::$cacheAll = $row;
	return $row;
	}

	/**
	 * Check if exists access in section
	 * @param string $get Checking access
	 * @return bool Result access
     */
	final public static function get($get) {
		$all = self::all();
		$level = User::get('level');
		if(is_bool($level) || empty($level)) {
			$level = config::Select("guest_level");
		}
		if(isset($all[$level]) && isset($all[$level]["access_".$get]) && $all[$level]["access_".$get] == "yes") {
			return true;
		} elseif(!isset($all[$level]) || !isset($all[$level]["access_".$get]) || $all[$level]["access_".$get] == "no") {
			return false;
		} else {
			return false;
		}
	}

	/**
	 * Rebuild standard access level
	 * @param array $array All change level
	 * @return array Result rebuilding
     */
	final private static function define($array) {
		$def = array();
		for($i=0;$i<sizeof($array);$i++) {
			$array[$i]['id'] -= 1;
			$def[$array[$i]['alt_name']] = $array[$i];
		}
	return $def;
	}

	/**
	 * Get check if exists access in section for template
	 * @param string $get Checking access
	 * @param string $access Assess to section
	 * @return string Result access
     */
	final public static function check($get, $access = "") {
		$all = self::all();
		$all = self::define($all);
		$level = User::get('level');
		if(is_bool($level) || empty($level)) {
			$level = config::Select("guest_level");
		}
		if(isset($all[$get]) && (isset($all[$get]['id']) && $level == $all[$get]['id']) && (isset($all[$get]['access_'.$access]) && $all[$get]['access_'.$access] == "yes")) {
			return "true";
		} else {
			return "false";
		}
	}

	/**
	 * Set access for level on id and clear cache access
	 * @param int $id Id for change
	 * @param string $set Section access
	 * @param string|bool $data Value access
     */
	final public static function set($id, $set, $data) {
		if(defined("WITHOUT_DB")) {
			return false;
		}
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
		db::doquery("UPDATE `".PREFIX_DB."userlevels` SET `".$set."` = \"".$data."\" WHERE `id` = ".$id);
		cache::Delete("userlevels");
	}

	final public static function is() {
		$all = self::all();
		$user = User::get("level");
		$notFound = 0;
		$list = func_get_args();
		for($i=0;$i<sizeof($list);$i++) {
			if($user!=$list[$i]) {
				$notFound++;
			}
		}
		return $notFound != sizeof($list);
	}

}

?>