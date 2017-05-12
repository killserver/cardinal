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

class config {

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
	
	final private static function initWithoutDB($action = "read", $name = "", $val = "", $valS = "", $valTh = "") {
	global $configWDB;
		if(!isset($configWDB) || !is_array($configWDB)) {
			$configWDB = array();
		}
		$dir = defined("ROOT_PATH") ? ROOT_PATH."core".DS."cache".DS."system".DS : dirname(__FILE__).DIRECTORY_SEPARATOR;
		$filePATH = $dir."configWithoutDB.txt";
		if($action == "read" && !empty($name) && file_exists($filePATH) && is_readable($filePATH)) {
			$file = file_get_contents($filePATH);
			$configWDB = unserialize($file);
		} elseif($action=="edit") {
			if(!(!empty($name) && ((file_exists($filePATH) && is_readable($filePATH)) || is_writable($dir)))) {
				return false;
			}
			if(file_exists($filePATH) && is_readable($filePATH)) {
				$file = file_get_contents($filePATH);
				$configWDB = unserialize($file);
			}
			if(!empty($valTh)) {
				$configWDB[$name][$val][$valS] = $valTh;
			} elseif(!empty($valS)) {
				$configWDB[$name][$val] = $valS;
			} else {
				$configWDB[$name] = $val;
			}
			if(file_exists($filePATH)) {
				unlink($filePATH);
			}
			if(is_writable($dir)) {
				file_put_contents($filePATH, serialize($configWDB));
			}
			return true;
		} elseif($action=="delete") {
			if(!(!empty($name) && ((file_exists($filePATH) && is_readable($filePATH)) || is_writable($dir)))) {
				return false;
			}
			if((!isset($configWDB[$name]) || !isset($configWDB[$name][$val])) && file_exists($filePATH) && is_readable($filePATH)) {
				$file = file_get_contents($filePATH);
				$configWDB = unserialize($file);
			}
			$update = false;
			if(!empty($val) && !empty($valS) && isset($configWDB[$name][$val])) {
				unset($configWDB[$name][$val][$valS]);
				$update = true;
			} elseif(!empty($val) && isset($configWDB[$name][$val])) {
				unset($configWDB[$name][$val]);
				$update = true;
			} elseif(isset($configWDB[$name])) {
				unset($configWDB[$name]);
				$update = true;
			}
			if($update) {
				if(file_exists($filePATH)) {
					unlink($filePATH);
				}
				if(is_writable($dir)) {
					file_put_contents($filePATH, serialize($configWDB));
				}
			}
			return true;
		}
		return $configWDB;
	}

	final public static function init() {
	global $config;
		if(defined("WITHOUT_DB") || !class_exists("db") || !method_exists("db", "connected") || !db::connected()) {
			$cfg = self::initWithoutDB();
			self::$config = array_merge($config, $cfg);
			return self::$config;
		}
		self::$config = array();
		if(!cache::Exists("config")) {
			$configs = array();
			db::doquery("SELECT `config_name`, `config_value` FROM `config`", true);
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
			if(!is_array(self::$config[$list[0]])) {
				self::$config[$list[0]] = array();
			}
			self::$config[$list[0]][$list[1]] = $list[2];
			return true;
		} else {
			return false;
		}
	}
	
	final public static function Exists() {
		if(func_num_args() == 0 || func_num_args() > 2) {
			return false;
		}
		$list = func_get_args();
		if(isset($list[1]) && !empty($list[1]) && isset($list[2]) && !empty($list[2]) && isset(self::$config[$list[0]]) && isset(self::$config[$list[0]][$list[1]]) && isset(self::$config[$list[0]][$list[1]][$list[2]])) {
			return true;
		} else if(isset($list[1]) && !empty($list[1]) && isset(self::$config[$list[0]]) && isset(self::$config[$list[0]][$list[1]])) {
			return true;
		} else if(isset(self::$config[$list[0]])) {
			return true;
		} else {
			return false;
		}
	}
	
	final public static function SetDefault($def) {
		self::$default = $def;
	}

	final public static function Select() {
		if(func_num_args() == 0 || func_num_args() > 2) {
			return false;
		}
		$list = func_get_args();
		if(isset($list[1]) && !empty($list[1]) && isset($list[2]) && !empty($list[2]) && isset(self::$config[$list[0]]) && isset(self::$config[$list[0]][$list[1]]) && isset(self::$config[$list[0]][$list[1]][$list[2]])) {
			return self::$config[$list[0]][$list[1]][$list[2]];
		} else if(isset($list[1]) && !empty($list[1]) && isset(self::$config[$list[0]]) && isset(self::$config[$list[0]][$list[1]])) {
			return self::$config[$list[0]][$list[1]];
		} else if(isset(self::$config[$list[0]])) {
			return self::$config[$list[0]];
		} else {
			return self::$default;
		}
	}

	final public static function Del() {
		if(func_num_args() == 0 || func_num_args() > 2) {
			return false;
		}
		$list = func_get_args();
		if(isset($list[1]) && !empty($list[1]) && isset($list[2]) && !empty($list[2]) && isset(self::$config[$list[0]]) && isset(self::$config[$list[0]][$list[1]]) && isset(self::$config[$list[0]][$list[1]][$list[2]])) {
			unset(self::$config[$list[0]][$list[1]][$list[2]]);
			self::initWithoutDB("delete", $list[0], $list[1], $list[2]);
			return true;
		} else if(isset($list[1]) && !empty($list[1]) && isset(self::$config[$list[0]]) && isset(self::$config[$list[0]][$list[1]])) {
			unset(self::$config[$list[0]][$list[1]]);
			self::initWithoutDB("delete", $list[0], $list[1]);
			return true;
		} else if(isset(self::$config[$list[0]])) {
			unset(self::$config[$list[0]]);
			self::initWithoutDB("delete", $list[0]);
			return true;
		} else {
			return self::$default;
		}
	}

	final public static function Update($name, $data = "") {
		if(defined("WITHOUT_DB") || !class_exists("db") || !method_exists("db", "connected") || !db::connected()) {
			return self::initWithoutDB("edit", $name, $data);
		}
		db::doquery("REPLACE INTO `config` SET `config_value` = \"".$data."\", `config_name` = \"".$name."\"");
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