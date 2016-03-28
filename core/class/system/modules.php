<?php
/*
 *
 * @version 3.0
 * @copyright 2014-2016 KilleR for Cardinal Engine
 *
 * Version Engine: 3.0
 * Version File: 3
 *
 * 3.1
 * add support install system modules
 * 3.2
 * fix errors in installer
 * 3.3
 * change log data
 * 3.4
 * fix bugs and construct method for installation modules
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

//namespace modules;

final class modules {
	
	private static $load_modules = false;

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
	
	public static function load_modules($file) {
		if(defined("IS_INSTALLER") || !self::init_db()->connected()) {
			return array();
		}
		if(is_bool(self::$load_modules)) {
			$cache = self::init_cache();
			if(!$cache->exists("load_modules")) {
				$db = self::init_db();
				$db->doquery("SELECT `file` FROM `modules` WHERE `activ` = \"yes\" AND `file` LIKE \"core%\"", true);
				self::$load_modules = array();
				while($row = $db->fetch_assoc()) {
					self::$load_modules[$row['file']] = true;
				}
				$cache->set("load_modules", self::$load_modules);
			} else {
				self::$load_modules = $cache->get("load_modules");
			}
		}
		if(isset(self::$load_modules[$file])) {
			return true;
		} else {
			return false;
		}
	}

	private static function init_modules() {
		if(defined("IS_INSTALLER") || !self::init_db()->connected()) {
			return array();
		}
		$cache = self::init_cache();
		if(!$cache->exists("modules")) {
//INSERT INTO `modules` SET activ = "yes", page = "reg", module = "reg_email"
			self::init_db()->doquery("SELECT page, module, method, param, tpl FROM modules WHERE activ = \"yes\"", true);
			$modules = array();
			while($row = self::init_db()->fetch_assoc()) {
				$modules[$row['page']][] = $row;
			}
			$cache->set("modules", json_encode($modules));
		} else {
			$get = $cache->get("modules");
			$modules = json_decode($get, true);
		}
	return $modules;
	}
	
	private static function parsePrimary($files) {
		$pr = -1;
		for($i=0;$i<sizeof($files);$i++) {
			if(isset($files[$i]) && isset($files[$i]->file) && isset($files[$i]->file->attributes()->primary)) {
				$pr = $i;
				break;
			}
		}
		return $pr;
	}

	private static function ReadRoot(&$arr, $dir = "") {
		if(empty($dir)) {
			$dir = ROOT_PATH;
			$view_dir = "";
		} else {
			$view_dir = str_replace(ROOT_PATH, "", $dir);
		}
		$dis = dir($dir);
		while(($file = $dis->read()) !== false) {
			if($file=="." || $file==".." || empty($file)) {
				continue;
			}
			if(is_dir($dir.$file)) {
				self::ReadRoot($arr, $dir.$file.DS);
			} else if(is_file($dir.$file)) {
				$arr[$view_dir . $file] = $view_dir . $file;
			}

		}
		return $arr;
	}

	private static function SearchOnArray($file, $array = array()) {
		$array = array_values($array);
		$file = (string) $file;
		for($i=0;$i<sizeof($array);$i++) {
			if(strpos($array[$i], $file) !== false) {
				return $array[$i];
			}
		}
		return "";
	}

	public static function Install($module) {
		if(!file_exists(ROOT_PATH."core".DS."modules".DS."xml".DS.$module.".xml")) {
			return false;
		}
		$db = self::init_db();
		$files_root = array();
		self::ReadRoot($files_root);
		try {
			$xml = simplexml_load_string(file_get_contents(ROOT_PATH . "core" . DS . "modules" . DS . "xml" . DS . $module . ".xml"));
		} catch(Exception $ex) {
			return false;
		}
		$sql = "";
		$parsePrimary = -1;
		if(isset($xml->files)) {
			$parsePrimary = self::parsePrimary($xml->files);
		}
		if(isset($xml->sql) && isset($xml->install) && sizeof($xml->install)>0 && isset($xml->uninstall) && sizeof($xml->uninstall)>0 && isset($xml->files) && $parsePrimary>=0 && isset($xml->files[$parsePrimary]->file) && isset($xml->files[$parsePrimary]->file->attributes()->path)) {
			$ch = $xml->sql->children();
			for($num=0;$num<sizeof($ch);$num++) {
				switch($ch[$num]->getname()) {
					case "create":
						if(!isset($ch[$num]->attributes()->table) || empty($ch[$num]->attributes()->table) || empty($ch[$num])) {
							continue;
						}
						$sqli = trim($ch[$num]);
						if(empty($sqli)) {
							continue;
						}
						if(isset($ch[$num]->attributes()->force)) {
							$sql .= "DROP TABLE IF EXISTS `".$ch[$num]->attributes()->table."`;";
						}
						$sql .= "CREATE TABLE IF NOT EXISTS `".$ch[$num]->attributes()->table."` (".$sqli.") ENGINE=";
						if(isset($ch[$num]->attributes()->engine) && !empty($ch[$num]->attributes()->engine)) {
							$sql .= $ch[$num]->attributes()->engine;
						} else {
							$sql .= "MyISAM";
						}
						$sql .= " DEFAULT CHARSET=".self::get_config("db", "charset").";";
					break;
					case "alter":
						if(!isset($ch[$num]->attributes()->table) || empty($ch[$num]->attributes()->table)) {
							continue;
						}
						$sqli = trim($ch[$num]);
						if(empty($sqli)) {
							continue;
						}
						$sql .= "ALTER TABLE `".$ch[$num]->attributes()->table."` ".$sqli.";";
					break;
					case "delete":
						if(!isset($ch[$num]->attributes()->table) || empty($ch[$num]->attributes()->table)) {
							continue;
						}
						$mod = "AND";
						if(isset($ch[$num]->attributes()->mod) && !empty($ch[$num]->attributes()->mod)) {
							$mod = $ch[$num]->attributes()->mod;
						}
						$where = array();
						if(sizeof($ch[$num]->where)>0) {
							for ($in = 0; $in < sizeof($ch[$num]->where); $in++) {
								$where[] = $ch[$num]->where[$in];
							}
						}
						if(sizeof($where)>0) {
							$sql .= "DELETE FROM `".$ch[$num]->attributes()->table."` WHERE ".(sizeof($where)>0 ? implode(" ".$mod." ", $where) : "1").";";
						}
					break;
					case "drop":
						if(!isset($ch[$num]->attributes()->table) || empty($ch[$num]->attributes()->table)) {
							continue;
						}
						$sql .= "DROP TABLE IF EXISTS `".$ch[$num]->attributes()->table."`;";
					break;
					case "insert":
						if(!isset($ch[$num]->attributes()->table) || empty($ch[$num]->attributes()->table) || empty($ch[$num])) {
							continue;
						}
						$sqli = trim($ch[$num]);
						if(empty($sqli)) {
							continue;
						}
						$mod = "";
						if(isset($ch[$num]->attributes()->force)) {
							$mod = " IGNORE";
						}
						$sql .= "INSERT".$mod." INTO `".$ch[$num]->attributes()->table."` SET ".$sqli.";";
					break;
				}
			}
			for($i=0;$i<sizeof($xml->install);$i++) {
				if(!isset($xml->install[$i]) || !isset($xml->info->attributes()->module)) {
					continue;
				}
				if($parsePrimary<0) {
					$parsePrimary = 0;
				}
				$param = array();
				$param['activ'] = "yes";
				$param['file'] = self::SearchOnArray($xml->files[$i]->file->attributes()->path, $files_root);
				$param['module'] = $xml->info->attributes()->module;
				if(isset($xml->install[$i]->attributes()->page)) {
					$param['page'] = $xml->install[$i]->attributes()->page;
				}
				if(isset($xml->install[$i]->method)) {
					$param['method'] = $xml->install[$i]->method;
				}
				if(isset($xml->install[$i]->param)) {
					$param['param'] = $xml->install[$i]->param;
				}
				if(isset($xml->install[$i]->tpl)) {
					$param['tpl'] = $xml->install[$i]->tpl;
				}
				$sql .= "INSERT INTO `modules` SET ".implode(", ", array_map(function($k, $v) { return "`".$k."` = \"".$v."\""; }, array_keys($param), array_values($param))).";";
			}
			// register all files for module
			for($i=0;$i<sizeof($xml->files->file);$i++) {
				$sql .= "INSERT INTO `modules` SET `file` = \"".self::SearchOnArray($xml->files->file[$i]->attributes()->path, $files_root)."\", `module` = \"".$xml->info->attributes()->module."\";";
			}
		}
		if(empty($sql)) {
			return false;
		}
		if(strpos($sql, ";")!==false) {
			$exp = explode(";", $sql);
			unset($sql);
			if(sizeof($exp)>1) {
				for($i=0;$i<sizeof($exp);$i++) {
					if(empty($exp[$i])) {
						continue;
					}
					$db->query($exp[$i]);
				}
			} else {
				$db->query(implode(";", $exp));
			}
		} else {
			$db->query($sql);
		}
		return true;
	}
	
	private static function UnInstallFile($module) {
		$db = self::init_db();
		$db->doquery("SELECT `file` FROM `modules` WHERE `module` LIKE \"%".$module."%\"", true);
		if($db->num_rows()==0) {
			return false;
		}
		while($files = $db->fetch_assoc()) {
			if(file_exists(ROOT_PATH.$files['file'])) {
				unlink(ROOT_PATH.$files['file']);
			}
		}
		if(file_exists(ROOT_PATH . "core" . DS . "modules" . DS . "xml" . DS . $module . ".xml")) {
			unlink(ROOT_PATH . "core" . DS . "modules" . DS . "xml" . DS . $module . ".xml");
		}
		return true;
	}

	public static function UnInstall($module) {
		if(!file_exists(ROOT_PATH."core".DS."modules".DS."xml".DS.$module.".xml")) {
			return self::UnInstallFile($module);
		}
		$db = self::init_db();
		$files_root = array();
		self::ReadRoot($files_root);
		try {
			$xml = simplexml_load_string(file_get_contents(ROOT_PATH . "core" . DS . "modules" . DS . "xml" . DS . $module . ".xml"));
		} catch(Exception $ex) {
			return self::UnInstallFile($module);
		}
		if(isset($xml->uninstall) && sizeof($xml->uninstall)>0 && isset($xml->files) && isset($xml->files)) {
			$sql = "";
			$ch = $xml->uninstall->children();
			for($num=0;$num<sizeof($ch);$num++) {
				switch($ch[$num]->getname()) {
					case "create":
						if(!isset($ch[$num]->attributes()->table) || empty($ch[$num]->attributes()->table) || empty($ch[$num])) {
							continue;
						}
						$sqli = trim($ch[$num]);
						if(empty($sqli)) {
							continue;
						}
						if(isset($ch[$num]->attributes()->force)) {
							$sql .= "DROP TABLE IF EXISTS `".$ch[$num]->attributes()->table."`;";
						}
						$sql .= "CREATE TABLE IF NOT EXISTS `".$ch[$num]->attributes()->table."` (".$sqli.") ENGINE=";
						if(isset($ch[$num]->attributes()->engine) && !empty($ch[$num]->attributes()->engine)) {
							$sql .= $ch[$num]->attributes()->engine;
						} else {
							$sql .= "MyISAM";
						}
						$sql .= " DEFAULT CHARSET=".self::get_config("db", "charset").";";
					break;
					case "alter":
						if(!isset($ch[$num]->attributes()->table) || empty($ch[$num]->attributes()->table)) {
							continue;
						}
						$sqli = trim($ch[$num]);
						if(empty($sqli)) {
							continue;
						}
						$sql .= "ALTER TABLE `".$ch[$num]->attributes()->table."` ".$sqli.";";
					break;
					case "delete":
						if(!isset($ch[$num]->attributes()->table) || empty($ch[$num]->attributes()->table)) {
							continue;
						}
						$mod = "AND";
						if(isset($ch[$num]->attributes()->mod) && !empty($ch[$num]->attributes()->mod)) {
							$mod = $ch[$num]->attributes()->mod;
						}
						$where = array();
						if(sizeof($ch[$num]->where)>0) {
							for ($in = 0; $in < sizeof($ch[$num]->where); $in++) {
								$where[] = $ch[$num]->where[$in];
							}
						}
						if(sizeof($where)>0) {
							$sql .= "DELETE FROM `".$ch[$num]->attributes()->table."` WHERE ".(sizeof($where)>0 ? implode(" ".$mod." ", $where) : "1").";";
						}
					break;
					case "drop":
						if(!isset($ch[$num]->attributes()->table) || empty($ch[$num]->attributes()->table)) {
							continue;
						}
						$sql .= "DROP TABLE IF EXISTS `".$ch[$num]->attributes()->table."`;";
					break;
					case "insert":
						if(!isset($ch[$num]->attributes()->table) || empty($ch[$num]->attributes()->table) || empty($ch[$num])) {
							continue;
						}
						$sqli = trim($ch[$num]);
						if(empty($sqli)) {
							continue;
						}
						$mod = "";
						if(isset($ch[$num]->attributes()->force)) {
							$mod = " IGNORE";
						}
						$sql .= "INSERT".$mod." INTO `".$ch[$num]->attributes()->table."` SET ".$sqli.";";
					break;
				}
			}
			for($i=0;$i<sizeof($xml->files->file);$i++) {
				if(file_exists(ROOT_PATH.$xml->files->file[$i]->attributes()->path)) {
					unlink(ROOT_PATH.$xml->files->file[$i]->attributes()->path);
				}
			}
			$db->doquery("SELECT `file` FROM `modules` WHERE `module` LIKE \"".$xml->info->attributes()->module."\"", true);
			while($files = $db->fetch_assoc()) {
				if(file_exists(ROOT_PATH.$files['file'])) {
					unlink(ROOT_PATH.$files['file']);
				}
			}
			$sql .= "DELETE FROM `modules` WHERE `module` LIKE \"".$xml->info->attributes()->module."\";";
			if(empty($sql)) {
				return false;
			}
			if(strpos($sql, ";")!==false) {
				$exp = explode(";", $sql);
				unset($sql);
				if(sizeof($exp)>1) {
					for($i=0;$i<sizeof($exp);$i++) {
						if(empty($exp[$i])) {
							continue;
						}
						$db->query($exp[$i]);
					}
				} else {
					$db->query(implode(";", $exp));
				}
			} else {
				$db->query($sql);
			}
			if(file_exists(ROOT_PATH . "core" . DS . "modules" . DS . "xml" . DS . $module . ".xml")) {
				unlink(ROOT_PATH . "core" . DS . "modules" . DS . "xml" . DS . $module . ".xml");
			}
			return true;
		} else {
			return self::UnInstallFile($module);
		}
	}
	public static function use_modules($page, $params=array()) {
		$modules = self::init_modules();
		$html = "";
		if(!isset($modules[$page])) {
			return $html;
		}
		for($i=0;$i<sizeof($modules[$page]);$i++) {
			if(isset($modules[$page][$i]['module']) && class_exists($modules[$page][$i]['module'])) {
				$class = $modules[$page][$i]['module'];
				if(empty($modules[$page][$i]['method'])) {
					continue;
				}
				$method = $modules[$page][$i]['method'];
				$mod = new $class();
				if(method_exists($mod, $method)) {
					$html .= $mod->$method($params);
				}
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
		$manifest['log'][$select][] = $set;
	return $manifest;
	}

	public static function manifest_getlog($select) {
	global $manifest;
		if(array_key_exists($select, $manifest['log'])) {
			return $manifest['log'][$select];
		} else {
			return false;
		}
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