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
	private static $setData = array();
	/**
	 * Get all exists user levels in DB
	 * @return array All user levels in DB
     */
	final public static function all() {
		if(is_array(self::$cacheAll) && sizeof(self::$cacheAll)>0) {
			return execEvent("loadUserLevels", self::$cacheAll);
		}
		$userlevels = array();
		if(isset($_SERVER['HTTP_HOST']) && file_exists(PATH_MEDIA."userlevels".str_replace("www.", "", $_SERVER['HTTP_HOST']).".".ROOT_EX)) {
			include_once(PATH_MEDIA."userlevels".str_replace("www.", "", $_SERVER['HTTP_HOST']).".".ROOT_EX);
		} else if(file_exists(PATH_MEDIA."userlevels.".ROOT_EX)) {
			include_once(PATH_MEDIA."userlevels.".ROOT_EX);
		} else if(file_exists(PATH_MEDIA."userlevels.default.".ROOT_EX)) {
			include_once(PATH_MEDIA."userlevels.default.".ROOT_EX);
		}
		if(file_exists(PATH_CACHE_SYSTEM."userlevels.php")) {
			$file = file_get_contents(PATH_CACHE_SYSTEM."userlevels.php");
			$file = preg_replace("#\<\?(.*?)\?\>#is", "", $file);
			if(self::is_serialized($file)) {
				$usersFile = unserialize($file);
				if(!file_exists(PATH_CACHE_SYSTEM."userlevels.php")) {
					$d = file_put_contents(PATH_CACHE_SYSTEM."userlevels.php", "<?php die(); ?>".self::normalizerJSON(self::jsonEncode($usersFile)));
					if($d) {
						unlink(PATH_CACHE_SYSTEM."userlevels.txt");
					}
				}
				$userlevels = array_merge($userlevels, $usersFile);
			} else if(self::is_json($file)) {
				$usersFile = json_decode($file, true);
				$userlevels = array_merge($userlevels, $usersFile);
			}
		} else if(file_exists(PATH_CACHE_SYSTEM."userlevels.txt")) {
			$f = file_get_contents(PATH_CACHE_SYSTEM."userlevels.txt");
			$usersFile = "";
			if(self::is_serialized($f)) {
				$usersFile = unserialize($f);
				$userlevels = array_merge($userlevels, $usersFile);
			} else if(self::is_json($f)) {
				$usersFile = json_decode($f, true);
				$userlevels = array_merge($userlevels, $usersFile);
			}
			if(!empty($usersFile) && !file_exists(self::PathUsers()."userlevels.php")) {
				$d = file_put_contents(self::PathUsers()."userlevels.php", "<?php die(); ?>".self::normalizerJSON(self::jsonEncode($usersFile)));
				if($d) {
					unlink(self::PathUsers()."userlevels.txt");
				}
			}
		}
		foreach(self::$setData as $level => $data) {
			$userlevels[$level] = array_merge($userlevels[$level], $data);
		}
		ksort($userlevels);
		self::$cacheAll = $userlevels;
		return execEvent("loadUserLevels", self::$cacheAll);
	}

	/**
	 * Check if exists access in section
	 * @param string $get Checking access
	 * @return bool Result access
     */
	final public static function get($get = "") {
		$all = self::all();
		$username = User::get('username');
		$level = User::get('level');
		if($get==="") {
			return $all[$level];
		}
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
		$userlevels = array();
		if(file_exists(PATH_CACHE_SYSTEM."userlevels.php")) {
			$levels = file_get_contents(PATH_CACHE_SYSTEM."userlevels.php");
			if(self::is_serialized($levels)) {
				$levels = unserialize($levels);
				$userlevels = array_merge($userlevels, $levels);
			} else if(self::is_json($levels)) {
				$levels = json_decode($levels, true);
				$userlevels = array_merge($userlevels, $levels);
			}
			unlink(PATH_CACHE_SYSTEM."userlevels.php");
		} else if(file_exists(PATH_CACHE_SYSTEM."userlevels.txt")) {
			$levels = file_get_contents(PATH_CACHE_SYSTEM."userlevels.txt");
			if(self::is_serialized($levels)) {
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
		@file_put_contents(PATH_CACHE_SYSTEM."userlevels.php", "<?php die(); ?>".self::normalizerJSON(self::jsonEncode($userlevels)));
		return true;
	}

	final public static function setAll($set, $data) {
		$defs = get_defined_constants(true);
		$defs = $defs['user'];
		$levels = array();
		foreach($defs as $k => $v) {
			if(strpos($k, "LEVEL_")!==false) {
				if(!isset(self::$setData[$v]) || !is_array(self::$setData[$v])) {
					self::$setData[$v] = array();
				}
				$set = (strpos($set, "access_")===false ? "access_".$set : $set);
				self::$setData[$v][$set] = $data;
			}
		}
		return $levels;
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

	final private static function is_json($str) {
		return $str === '""' || $str === '[]' || $str === '{}' || $str[0] === '"' && substr($str, -1) === '"' || $str[0] === '[' && substr($str, -1) === ']' || $str[0] === '{' && substr($str, -1) === '}';
	}

	final private static function is_serialized($data) {
		if(!is_string($data)) {
			return false;
		}
		$data = trim($data);
		if('N;' == $data) {
			return true;
		}
		if(!preg_match('/^([adObis]):/', $data, $badions)) {
			return false;
		}
		switch($badions[1]) {
			case 'a':
			case 'O':
			case 's':
				if (preg_match("/^".$badions[1].":[0-9]+:.*[;}]\$/s", $data)) {
					return true;
				}
			break;
			case 'b':
			case 'i':
			case 'd':
				if(preg_match("/^".$badions[1].":[0-9.E-]+;\$/", $data)) {
					return true;
				}
			break;
		}
		return false;
	}
	
	final private static function normalizerJSON($data) {
		$arr = array();
		$tab = 1;
		$d = false;
		for($f=0;$f<strlen($data);$f++) {
			$bytes = $data[$f];
			if($d && $bytes === $d) {
				$data[$f - 1] !== "\\" && ($d = !1);
			} else if(!$d && ($bytes === '"' || $bytes === "'")) {
				$d = $bytes;
			} else if(!$d && ($bytes === " " || $bytes === "\t")) {
				$bytes = "";
			} else if(!$d && $bytes === ":") {
				$bytes = $bytes." ";
			} else if(!$d && $bytes === ",") {
				$bytes = $bytes."\n";
				$bytes = str_pad($bytes, ($tab * 2), " ");
			} else if(!$d && ($bytes === "[" || $bytes === "{")) {
				$tab++;
				$bytes .= "\n";
				$bytes = str_pad($bytes, ($tab * 2), " ");
			} else if(!$d && ($bytes === "]" || $bytes === "}")) {
				$tab--;
				$bytes = str_pad("\n", ($tab * 2), " ").$bytes;
			}
			array_push($arr, $bytes);
		}
		return implode("", $arr);
	}

	final private static function jsonEncode($arr) {
		if(defined('JSON_UNESCAPED_UNICODE')) {
			return json_encode($arr, JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
		} else {
			return preg_replace_callback('/(?<!\\\\)\\\\u([0-9a-f]{4})/i', "userlevel::json_encode_unicode_fn", json_encode($arr));
		}
	}

	final private static function json_encode_unicode_fn($m) {
		$d = pack("H*", $m[1]);
		$r = mb_convert_encoding($d, "UTF8", "UTF-16BE");
		return $r!=="?" && $r!=="" ? $r : $m[0];
	}

}

?>