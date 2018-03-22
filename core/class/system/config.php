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

class config implements ArrayAccess {

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
		$dir = defined("ROOT_PATH") ? PATH_CACHE_USERDATA : dirname(__FILE__).DIRECTORY_SEPARATOR;
		$filePATH = $dir."configWithoutDB.txt";
		if($action == "read" && file_exists($filePATH) && is_readable($filePATH)) {
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
				if(!isset($configWDB[$name])) {
					$configWDB[$name] = array();
				}
				if(!isset($configWDB[$name][$val])) {
					$configWDB[$name][$val] = array();
				}
				$configWDB[$name][$val][$valS] = $valTh;
			} elseif(!empty($valS)) {
				if(!isset($configWDB[$name])) {
					$configWDB[$name] = array();
				}
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
			if(!empty($val) && !empty($valS) && isset($configWDB[$name]) && isset($configWDB[$name][$val]) && isset($configWDB[$name][$valS])) {
				unset($configWDB[$name][$val][$valS]);
				$update = true;
			} elseif(!empty($val) && isset($configWDB[$name]) && isset($configWDB[$name][$val])) {
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
			db::doquery("SELECT `config_name`, `config_value` FROM {{config}}", true);
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
		if(sizeof($list)==1 && strpos($list[0], ".")!==false) {
			$list = explode(".", $list[0]);
		}
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
		if(sizeof($list)==1 && strpos($list[0], ".")!==false) {
			$list = explode(".", $list[0]);
		}
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
		if(sizeof($list)==1 && strpos($list[0], ".")!==false) {
			$list = explode(".", $list[0]);
		}
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
		if(sizeof($list)==1 && strpos($list[0], ".")!==false) {
			$list = explode(".", $list[0]);
		}
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
			/*if(strpos($data, ".")!==false) {
				$exp = explode(".", $data);
				if(sizeof($exp)==3) {
					return self::initWithoutDB("edit", $name, $exp[0], $exp[1], $exp[2]);
				} else if(sizeof($exp)==2) {
					return self::initWithoutDB("edit", $name, $exp[0], $exp[1]);
				} else {
					return self::initWithoutDB("edit", $name, $exp[0]);
				}
			} else {*/
				return self::initWithoutDB("edit", $name, $data);
			/*}*/
		}
		db::doquery("REPLACE INTO {{config}} SET `config_value` = \"".$data."\", `config_name` = \"".$name."\"");
		cache::Delete("config");
		if(!empty(self::$config[$data])) {
			return self::$config[$data];
		} else {
			return true;
		}
	}
	
	final public function __get($val) {
		if(strpos($val, ".")!==false) {
			$val = explode(".", $val);
		}
		if(is_array($val) && sizeof($val)==3 && isset(self::$config[$val[0]]) && isset(self::$config[$val[0]][$val[1]]) && isset(self::$config[$val[0]][$val[1]][$val[2]])) {
			return self::$config[$val[0]][$val[1]][$val[2]];
		} else if(is_array($val) && sizeof($val)==2 && isset(self::$config[$val[0]]) && isset(self::$config[$val[0]][$val[1]])) {
			return self::$config[$val[0]][$val[1]];
		} else if(is_array($val) && sizeof($val)==1 && isset(self::$config[$val[0]])) {
			return self::$config[$val[0]];
		} else if(!empty($val) && isset(self::$config[$val])) {
			return self::$config[$val];
		} else {
			return false;
		}
	}
	
	final public function __set($name, $val) {
		if(strpos($name, ".")!==false) {
			$name = explode(".", $name);
		}
		if(is_array($name) && sizeof($name)==3 && !empty($val)) {
			if(!isset(self::$config[$name[0]])) {
				self::$config[$name[0]] = array();
			}
			if(!isset(self::$config[$name[1]])) {
				self::$config[$name[0]][$name[1]] = array();
			}
			self::$config[$name[0]][$name[1]][$name[2]] = $val;
			return true;
		} else if(is_array($name) && sizeof($name)==2 && !empty($val)) {
			if(!isset(self::$config[$name[0]])) {
				self::$config[$name[0]] = array();
			}
			self::$config[$name[0]][$name[1]] = $val;
			return true;
		} else if(is_array($name) && sizeof($name)==1 && !empty($val)) {
			self::$config[$name[0]] = $val;
			return true;
		} else if(!empty($name) && !empty($val)) {
			self::$config[$name] = $val;
			return true;
		} else {
			return false;
		}
	}
	
	public function offsetSet($offset, $value) {
		if(strpos($offset, ".")!==false) {
			$offset = explode(".", $offset);
		}
		if(is_null($offset)) {
			self::$config[] = $value;
		} else {
			if(is_array($offset) && sizeof($offset)==3 && !empty($value)) {
				if(!isset(self::$config[$offset[0]])) {
					self::$config[$offset[0]] = array();
				}
				if(!isset(self::$config[$offset[0]][$offset[1]])) {
					self::$config[$offset[0]][$offset[1]] = array();
				}
				self::$config[$offset[0]][$offset[1]][$offset[2]] = $value;
			} else if(is_array($offset) && sizeof($offset)==2 && !empty($value)) {
				if(!isset(self::$config[$offset[0]])) {
					self::$config[$offset[0]] = array();
				}
				self::$config[$offset[0]][$offset[1]] = $value;
			} else if(is_array($offset) && sizeof($offset)==1 && !empty($value)) {
				self::$config[$offset[0]] = $value;
			} else if(is_string($offset)) {
				self::$config[$offset] = $value;
			}
		}
    }
	
	public function offsetExists($offset) {
		if(strpos($offset, ".")!==false) {
			$offset = explode(".", $offset);
		}
		if(is_array($offset) && sizeof($offset)==3 && !empty($offset[0]) && isset(self::$config[$offset[0]]) && !empty($offset[1]) && isset(self::$config[$offset[0]][$offset[1]]) && !empty($offset[2]) && isset(self::$config[$offset[0]][$offset[1]][$offset[2]])) {
			return true;
		} else if(is_array($offset) && sizeof($offset)==2 && !empty($offset[0]) && isset(self::$config[$offset[0]]) && !empty($offset[1]) && isset(self::$config[$offset[0]][$offset[1]])) {
			return true;
		} else if(is_array($offset) && sizeof($offset)==1 && !empty($offset[0]) && isset(self::$config[$offset[0]])) {
			return true;
		} else if(is_string($offset) && !empty($offset)) {
			return isset(self::$config[$offset]);
		} else {
			return false;
		}
	}
	
	public function offsetUnset($offset) {
		if(strpos($offset, ".")!==false) {
			$offset = explode(".", $offset);
		}
		if(is_array($offset) && sizeof($offset)==3 && !empty($offset[0]) && isset(self::$config[$offset[0]]) && !empty($offset[1]) && isset(self::$config[$offset[0]][$offset[1]]) && !empty($offset[2]) && isset(self::$config[$offset[0]][$offset[1]][$offset[2]])) {
			unset(self::$config[$offset[0]][$offset[1]][$offset[2]]);
		} else if(is_array($offset) && sizeof($offset)==2 && !empty($offset[0]) && isset(self::$config[$offset[0]]) && !empty($offset[1]) && isset(self::$config[$offset[0]][$offset[1]])) {
			unset(self::$config[$offset[0]][$offset[1]]);
		} else if(is_array($offset) && sizeof($offset)==1 && !empty($offset[0]) && isset(self::$config[$offset[0]])) {
			unset(self::$config[$offset[0]]);
		} else if(is_string($offset) && isset(self::$config[$offset])) {
			unset(self::$config[$offset]);
		}
	}
	
	public function offsetGet($offset) {
		if(strpos($offset, ".")!==false) {
			$offset = explode(".", $offset);
		}
		if(is_array($offset) && sizeof($offset)==3 && !empty($offset[0]) && isset(self::$config[$offset[0]]) && !empty($offset[1]) && isset(self::$config[$offset[0]][$offset[1]]) && !empty($offset[2]) && isset(self::$config[$offset[0]][$offset[1]][$offset[2]])) {
			return self::$config[$offset[0]][$offset[1]][$offset[2]];
		} else if(is_array($offset) && sizeof($offset)==2 && !empty($offset[0]) && isset(self::$config[$offset[0]]) && !empty($offset[1]) && isset(self::$config[$offset[0]][$offset[1]])) {
			return self::$config[$offset[0]][$offset[1]];
		} else if(is_array($offset) && sizeof($offset)==1 && !empty($offset[0]) && isset(self::$config[$offset[0]])) {
			return self::$config[$offset[0]];
		} else if(is_string($offset) && !empty($offset) && isset(self::$config[$offset])) {
			return self::$config[$offset];
		} else {
			return null;
		}
	}

}

?>