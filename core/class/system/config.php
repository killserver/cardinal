<?php
/*
 *
 * @version 2015-09-30 13:30:44 1.25.6-rc3
 * @copyright 2014-2015 KilleR for Cardinal Engine
 *
 * Version Engine: 1.25.6-rc3
 * Version File: 2
 *
 * 2.2
 * add checker connection to db
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

final class config {

	private static $config = array();
	private static $default = false;
	
	final public static function StandAlone() {
	global $config;
		self::$config = $config;
	}
	
	final public static function ReadConfig($name, $val) {
		$configs = array();
		if(strpos($val, ":-:")!==false) {
			$vals = array();
			if(strpos($val, ";-;")!==false) {
				$exp = explode(";-;", $val);
				for($i=0;$i<sizeof($exp);$i++) {
					$ex = explode(":-:", $exp[$i]);
					$vals[$ex[0]] = $ex[1];
				}
				$configs[$name] = $vals;
			} else {
				$exp = explode(":-:", $val);
				$vals[$exp[0]] = $exp[1];
				$configs[$name] = $vals;
			}
		} else {
			$configs[$name] = $val;
		}
		return $configs;
	}

	final public static function init() {
	global $config;
		if(!db::connected()) {
			self::$config = $config;
			return $config;
		}
		self::$config = array();
		if(!cache::Exists("config")) {
			$configs = array();
			db::doquery("SELECT config_name, config_value FROM config", true);
			while($conf = db::fetch_array()) {
				$ret = self::ReadConfig($conf['config_name'], $conf['config_value']);
				$configs = array_merge($configs, $ret);
			}
			self::$config = $configs;
			cache::Set("config", $configs);
			unset($configs);
		} else {
			self::$config = cache::Get("config");
		}
		self::$config = (sizeof(self::$config) > 0 ? array_merge($config, self::$config) : $config);
	}

	final public static function All() {
		return self::$config;
	}
	
	final public static function Set() {
		if(func_num_args()<2) {
			return false;
		}
		$list = func_get_args();
		if(sizeof($list)==2) {
			self::$config[$list[0]] = $list[1];
			return true;
		} else if(sizeof($list)==3) {
			self::$config[$list[0]][$list[1]] = $list[2];
			return true;
		} else {
			return false;
		}
	}
	
	final public static function Exists($data, $sub = "", $subst = "") {
		if(!empty($sub) && !empty($subst) && isset(self::$config[$data]) && isset(self::$config[$data][$sub]) && isset(self::$config[$data][$subst])) {
			return true;
		} else if(!empty($sub) && isset(self::$config[$data]) && isset(self::$config[$data][$sub])) {
			return true;
		} else if(isset(self::$config[$data])) {
			return true;
		} else {
			return false;
		}
	}
	
	final public static function SetDefault($def) {
		self::$default = $def;
	}

	final public static function Select($data, $sub = "", $subst = "") {
		if(!empty($sub) && !empty($subst) && isset(self::$config[$data]) && isset(self::$config[$data][$sub]) && isset(self::$config[$data][$subst])) {
			return self::$config[$data][$sub][$subst];
		} else if(!empty($sub) && isset(self::$config[$data]) && isset(self::$config[$data][$sub])) {
			return self::$config[$data][$sub];
		} else if(isset(self::$config[$data])) {
			return self::$config[$data];
		} else {
			return self::$default;
		}
	}

	final public static function Update($name, $data = "") {
		db::doquery("REPLACE INTO `config` SET `config_value` = \"".$data."\" WHERE `config_name` = \"".$name."\"");
		cache::Delete("config");
		if(!empty(self::$config[$data])) {
			return self::$config[$data];
		} else {
			return true;
		}
	}
	
	final public function __get($val) {
		if(!empty($val) && isset(self::$config[$val])) {
			return self::$config[$val];
		} else {
			return false;
		}
	}
	
	final public function __set($name, $val) {
		if(!empty($name) && !empty($val)) {
			self::$config[$name] = $val;
			return true;
		} else {
			return false;
		}
	}

	function __destruct() {
		unset($this);
	}

}

?>