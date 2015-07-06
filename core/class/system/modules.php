<?php
/*
*
* Version Engine: 1.25.5a7
* Version File: 3
*
* 3.1
* add support install system modules
*
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

//namespace modules;

final class modules {

	public static function get_config($get, $array=null) {
	global $config;
		if(!empty($array)) {
			if(isset($config[$get][$array])) {
				return $config[$get][$array];
			} else {
				return false;
			}
		} else {
			if(isset($config[$get])) {
				return $config[$get];
			} else {
				return false;
			}
		}
	}

	public static function get_lang($get, $array=null) {
	global $lang;
		if(!empty($array)) {
			if(isset($lang[$get][$array])) {
				return $lang[$get][$array];
			} else {
				return false;
			}
		} else {
			if(isset($lang[$get])) {
				return $lang[$get];
			} else {
				return false;
			}
		}
	}

	public static function init_templates() {
	global $templates;
		if(empty($templates)) {
			return new templates();
		} else {
			return $templates;
		}
	}

	public static function init_lang() {
		return new lang();
	}

	public static function init_bb() {
		return new bbcodes();
	}

	public static function init_db() {
	global $db;
		if(!$db) {
			$dbs = new db();
			return $dbs;
		} else {
			return $db;
		}
	}

	public static function init_cache() {
	global $cache;
		if(!$cache) {
			return new cache();
		} else {
			return $cache;
		}
	}

	private static function init_modules() {
		if(!db::connected()) {
			return array();
		}
		if(!self::init_cache()->exists("modules")) {
			self::init_db()->doquery("SELECT page, module, method, param, tpl FROM modules WHERE activ = \"yes\"", true);
			$modules = array();
			while($row = self::init_db()->fetch_assoc()) {
				$modules[$row['page']][] = $row;
			}
			self::init_cache()->set("modules", json_encode($modules));
		} else {
			$get = self::init_cache()->get("modules");
			$modules = json_decode($get, true);
		}
	return $modules;
	}

	public static function use_modules($page, $params=array()) {
		$modules = self::init_modules();
		$html = "";
		for($i=0;$i<sizeof($modules[$page]);$i++) {
			if(isset($modules[$page][$i]['module']) && class_exists($modules[$page][$i]['module'])) {
				$class = $modules[$page][$i]['module'];
				if(empty($modules[$page][$i]['method'])) {
					continue;
				}
				$method = $modules[$page][$i]['method'];
				$mod = new $class();
				$html .= $mod->$method($params);
				if(!empty($modules[$page][$i]['tpl'])) {
					$tpl = json_decode($modules[$page][$i]['tpl'], true);
					self::init_templates()->assign_vars($tpl);
				}
			}
		}
		unset($tpl, $modules);
	return $html;
	}

	public static function change_db($page, $db) {
		$modules = self::init_modules();
		if(isset($modules[$page])) {
			for($i=0;$i<sizeof($modules[$page]);$i++) {
				if(isset($modules[$page][$i]['module']) && class_exists($modules[$page][$i]['module'])) {
					$class = $modules[$page][$i]['module'];
					$mod = new $class();
					if(method_exists($mod, "change_db")) {
						$db = $mod->change_db($db);
					}
					unset($mod);
				}
			}
		}
	return $db;
	}

	public static function select_db($page, $db) {
		$modules = self::init_modules();
		for($i=0;$i<sizeof($modules[$page]);$i++) {
			if(isset($modules[$page][$i]['module']) && class_exists($modules[$page][$i]['module'])) {
				$class = $modules[$page][$i]['module'];
				$mod = new $class();
				if(method_exists($mod, "select_db")) {
					$db = $mod->select_db($db);
				}
				unset($mod);
			}
		}
	return $db;
	}

	public static function get_user($get) {
	global $user;
		$assess = array('id', 'username', 'alt_name', 'level', 'email', 'time_reg', 'last_activ', 'activ', 'avatar');
		if(in_array($get, $assess)) {
			if(isset($user[$get])) {
				return $user[$get];
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public static function manifest_log($select, $set) {
	global $manifest;
		$manifest[$select][] = $set;
	return $manifest;
	}

	public static function manifest_set($select, $set) {
	global $manifest;
		if(is_array($select)&&sizeof($select)==3) {
			$manifest[$select[0]][$select[1]][$select[2]] = $set;
		} elseif(is_array($select)&&sizeof($select)==2) {
			$manifest[$select[0]][$select[1]] = $set;
		} elseif(is_array($select)&&sizeof($select)==1) {
			$manifest[$select[0]] = $set;
		} else {
			$manifest[$select] = $set;
		}
	return $manifest;
	}

	public static function manifest_get($get) {
	global $manifest;
		if(is_array($get)) {
			if(sizeof($get)==3&&isset($manifest[$get[0]][$get[1]][$get[2]])) {
				return $manifest[$get[0]][$get[1]][$get[2]];
			} elseif(sizeof($get)==2&&isset($manifest[$get[0]][$get[1]])) {
				return $manifest[$get[0]][$get[1]];
			} elseif(sizeof($get)==1&&isset($manifest[$get[0]])) {
				return $manifest[$get[0]];
			} else {
				return false;
			}
		} else {
			if(isset($manifest[$get])) {
				return $manifest[$get];
			} else {
				return false;
			}
		}
	}

}

?>