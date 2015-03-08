<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

final class config {

	private static $config = array();
	
	public static function StandAlone() {
	global $config;
		self::$config = $config;
	}

	public static function init() {
	global $config;
		self::$config = array();
		if(!cache::Exists("config")) {
			$configs = array();
			db::doquery("SELECT config_name, config_value FROM config", true);
			while($conf = db::fetch_array()) {
				if(strpos($conf['config_value'], ":-:")!==false) {
					$vals = array();
					if(strpos($conf['config_value'], ";-;")!==false) {
						$exp = explode(";-;", $conf['config_value']);
						for($i=0;$i<sizeof($exp);$i++) {
							$ex = explode(":-:", $exp[$i]);
							$vals[$ex[0]] = $ex[1];
						}
						$configs[$conf['config_name']] = $vals;
					} else {
						$exp = explode(":-:", $conf['config_value']);
						$vals[$exp[0]] = $exp[1];
						$configs[$conf['config_name']] = $vals;
					}
				} else {
					$configs[$conf['config_name']] = $conf['config_value'];
				}
			}
			self::$config = $configs;
			cache::Set("config", $configs);
			unset($configs);
		} else {
			self::$config = cache::Get("config");
		}
		self::$config = (sizeof(self::$config) > 0 ? array_merge($config, self::$config) : $config);
	}

	public static function All() {
		return self::$config;
	}
	
	public static function Exists($data, $sub=null, $subst = null) {
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

	public static function Select($data, $sub=null, $subst = null) {
//		var_dump(self::$config, ROOT_PATH, ROOT_EX);die();
		if(!empty($sub) && !empty($subst) && isset(self::$config[$data]) && isset(self::$config[$data][$sub]) && isset(self::$config[$data][$subst])) {
			return self::$config[$data][$sub][$subst];
		} else if(!empty($sub) && isset(self::$config[$data]) && isset(self::$config[$data][$sub])) {
			return self::$config[$data][$sub];
		} else if(isset(self::$config[$data])) {
			return self::$config[$data];
		} else {
			return false;
		}
	}

	public static function Update($name, $data=null) {
		db::doquery("UPDATE config SET config_value = \"".$data."\" WHERE config_name = \"".$name."\"");
		cache::Delete("config");
		if(!empty(self::$config[$data])) {
			return self::$config[$data];
		} else {
			return true;
		}
	}

	function __destruct() {
		unset($this);
	}

}

?>