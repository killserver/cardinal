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

class modules {
	
	private static $load_modules = false;
	private static $load_hooks = false;
	private static $columns = array();
	private static $access_user = array('id', 'username', 'alt_name', 'level', 'email', 'time_reg', 'last_activ', 'activ', 'avatar');
	
	final public static function checkObject($obj, $name, $checkParent = false) {
		if(gettype($name)!="string") {
			throw new Exception("Error set #2 parameter");
		}
		if($checkParent === true && is_object($obj) && get_parent_class($obj)===$name) {
			return true;
		} else if(is_object($obj) && get_class($obj)===$name) {
			return true;
		} else {
			return false;
		}
	}
	
	final public static function setParam($name, $type, $func) {
	global $manifest;
		if(isset($manifest['applyParam']) && !isset($manifest['applyParam'][$name])) {
			$manifest['applyParam'][$name] = array();
			$manifest['applyParam'][$name][$type] = array();
			$manifest['applyParam'][$name][$type][] = $func;
			return true;
		} else if(isset($manifest['applyParam']) && isset($manifest['applyParam'][$name]) && !isset($manifest['applyParam'][$name][$type])) {
			$manifest['applyParam'][$name][$type] = array();
			$manifest['applyParam'][$name][$type][] = $func;
			return true;
		} else if(isset($manifest['applyParam']) && isset($manifest['applyParam'][$name]) && isset($manifest['applyParam'][$name][$type])) {
			$manifest['applyParam'][$name][$type][] = $func;
			return true;
		} else {
			return false;
		}
	}
	
	final public static function applyParam($param, $type, $name) {
	global $manifest;
		if(isset($manifest['applyParam']) && isset($manifest['applyParam'][$name]) && is_array($manifest['applyParam'][$name]) && sizeof($manifest['applyParam'][$name])>0 && isset($manifest['applyParam'][$name][$type]) && is_array($manifest['applyParam'][$name][$type]) && sizeof($manifest['applyParam'][$name][$type])>0) {
			for($i=0;$i<sizeof($manifest['applyParam'][$name][$type]);$i++) {
				for($z=0;$z<sizeof($param);$z++) {
					$param[$z] = call_user_func_array($manifest['applyParam'][$name][$type][$i], array($param[$z]));
				}
			}
		}
		return $param;
	}

	final public static function get_config($get, $array = "", $default = false) {
	global $config;
		if(!empty($array)) {
			if(isset($config[$get][$array])) {
				return $config[$get][$array];
			} else {
				return $default;
			}
		} else {
			if(isset($config[$get])) {
				return $config[$get];
			} else {
				return $default;
			}
		}
	}

	final public static function set_config($get, $val) {
	global $config;
		if(is_array($get)) {
			if(!isset($config[$get[0]])) {
				$config[$get[0]] = array();
			}
			$config[$get[0]][$get[1]] = $val;
			return true;
		} else {
			$config[$get] = $val;
			return true;
		}
	}
	
	final public static function loadModels($class, $autoload = false) {
		if(!class_exists($class, false)) {
			if(file_exists(ROOT_PATH."core".DS."modules".DS."models".DS.$class.".".ROOT_EX)) {
				include_once(ROOT_PATH."core".DS."modules".DS."models".DS.$class.".".ROOT_EX);
				if(is_bool($autoload) && $autoload === false) {
					return true;
				} else {
					if(class_exists($class, false)) {
						$ret = new $class();
						if(method_exists($ret, "init_model")) {
							$ret->init_model($autoload);
						}
						return $ret;
					} else {
						throw new Exception("Error loading model");
						return false;
					}
				}
			} else {
				throw new Exception("Error loading model. File not found");
				return false;
			}
		}
		if(class_exists($class, false)) {
			$ret = new $class();
			if(method_exists($ret, "init_model")) {
				$ret->init_model($autoload);
			}
			return $ret;
		} else {
			throw new Exception("Error loading model");
			return false;
		}
	}
	
	final public static function loader($class, $standard = array()) {
		if(!class_exists($class, false)) {
			if(file_exists(ROOT_PATH."core".DS."modules".DS."autoload".DS.$class.".".ROOT_EX)) {
				include_once(ROOT_PATH."core".DS."modules".DS."autoload".DS.$class.".".ROOT_EX);
			} else if(file_exists(ROOT_PATH."core".DS."class".DS.$class.".".ROOT_EX)) {
				include_once(ROOT_PATH."core".DS."class".DS.$class.".".ROOT_EX);
			} else if(file_exists(ROOT_PATH."core".DS."modules".DS."library".DS.$class.".".ROOT_EX)) {
				include_once(ROOT_PATH."core".DS."modules".DS."library".DS.$class.".".ROOT_EX);
			}
			if(!class_exists($class, false)) {
				throw new Exception('Class is not exists', 6);
			}
			$refMethod = new ReflectionMethod($class,  '__construct');
			$params = $refMethod->getParameters();
			$re_args = array();
			foreach($params as $key => $param) {
				$name = $param->getName();
				if($param->isPassedByReference() && isset($standard[$name])) {
					$re_args[$key] = &$standard[$name];
				} else if(isset($standard[$name])) {
					$re_args[$key] = $standard[$name];
				}
			}
			$refClass = new ReflectionClass($class);
			return $refClass->newInstanceArgs((array) $re_args);
		} else {
			$refMethod = new ReflectionMethod($class,  '__construct');
			$params = $refMethod->getParameters();
			$re_args = array();
			foreach($params as $key => $param) {
				$name = $param->getName();
				if($param->isPassedByReference() && isset($standard[$name])) {
					$re_args[$key] = &$standard[$name];
				} else if(isset($standard[$name])) {
					$re_args[$key] = $standard[$name];
				}
			}
			$refClass = new ReflectionClass($class);
			return $refClass->newInstanceArgs((array) $re_args);
		}
	}
	
	final private static function implodeData($del, $arr) {
		$err = false;
		$arr = array_values($arr);
		for($i=0;$i<sizeof($arr);$i++) {
			if(is_array($arr[$i])) {
				$err = true;
				break;
			}
		}
		return ($err ? "" : implode($del, $arr));
	}

	final public static function get_lang($get, $array = "") {
	global $lang;
		$langs = self::init_lang();
		$lang = $langs->init_lang(false);
		if(strlen($array)>0 && is_array($lang) && isset($lang[$get][$array])) {
			$return = array($lang[$get][$array]);
		} else if(isset($lang[$get]) && is_array($lang)) {
			$return = array($lang[$get]);
		} else if(func_num_args()>0) {
			$return = func_get_args();
		}
		$return = self::applyParam($return, 'after', "_e");
		return self::implodeData("", $return);
	}
	
	final public static function init_mobileDetect() {
	global $mobileDetect;
		if(!is_object($mobileDetect)) {
			$mobileDetect = new Mobile_Detect();
		}
		return $mobileDetect;
	}

	final public static function init_templates() {
	global $templates;
		if(empty($templates)) {
			return new templates();
		} else {
			return $templates;
		}
	}

	final public static function init_lang($lang = false) {
	global $langInit;
		if(empty($langInit)) {
			$langInit = new lang($lang);
			return $langInit;
		} else {
			return $langInit;
		}
	}

	final public static function init_bb() {
		return new bbcodes();
	}

	final public static function init_config() {
		return new config();
	}

	final public static function init_db() {
	global $db;
		if(!$db) {
			$dbs = new db();
			return $dbs;
		} else {
			return $db;
		}
	}

	final public static function init_cache() {
	global $cache;
		if(!$cache) {
			return new cache();
		} else {
			return $cache;
		}
	}
	
	final public static function CheckVersion($check) {
		if(function_exists("cardinal_version")) {
			return cardinal_version($check);
		} else {
			$isChecked = (defined("INTVERSION") ? INTVERSION : (defined("VERSION") ? VERSION : $old));
			if(empty($check)) {
				return $isChecked;
			}
			if(stripos($check, "-")!==false) {
				$check = explode("-", $check);
				$check = current($check);
			}
			if(class_exists("config") && method_exists("config", "Select") && config::Select("speed_update")) {
				$if = ($check)>($isChecked);
			} else {
				$checked = intval(str_replace(".", "", $check));
				$version = intval(str_replace(".", "", $isChecked));
				if(strlen($checked)>strlen($version)) {
					$version = int_pad($version, strlen($checked));
				}
				$if = $checked>$version;
			}
			return $if;
		}
		return $if;
	}
	
	final private static function ExecHooks($module, $param = array()) {
		try {
			$dir = ROOT_PATH."core".DS."modules".DS."hooks".DS;
			if(is_dir($dir)) {
				if($dh = dir($dir)) {
					while(($file = $dh->read()) !== false) {
						if($file != "index.".ROOT_EX && $file != "." && $file != ".." && strpos($file, $module) !== false) {
							require_once($dir.$file);
							$class = str_replace(".".ROOT_EX, "", $file);
							if(class_exists($class)) {
								$classes = new $class();
								if(method_exists($classes, "init_hook")) {
									$classes->init_hook($param);
								}
								unset($classes);
							}
						}
					}
				$dh->close();
				}
			}
			return true;
		} catch(Exception $ex) {
			return false;
		}
	}
	
	final public static function load_hooks($module, $param = array()) {
		if(defined("WITHOUT_DB")) {
			if(file_exists(ROOT_PATH."core".DS."modules".DS."hooks".DS."loader.".ROOT_EX)) {
				$hooksLoad = array();
				include(ROOT_PATH."core".DS."modules".DS."hooks".DS."loader.".ROOT_EX);
				if(!isset($hooksLoad[$module])) {
					return false;
				}
				return self::ExecHooks($module, $param);
			} else if(file_exists(ROOT_PATH."core".DS."modules".DS."hooks".DS."loader.default.".ROOT_EX)) {
				$hooksLoad = array();
				include(ROOT_PATH."core".DS."modules".DS."hooks".DS."loader.default.".ROOT_EX);
				if(!isset($hooksLoad[$module])) {
					return false;
				}
				return self::ExecHooks($module, $param);
			} else {
				return false;
			}
		}
		if(is_bool(self::$load_hooks)) {
			$cache = self::init_cache();
			if(!$cache->exists("load_hooks")) {
				$db = self::init_db();
				$db->doquery("SELECT `module` FROM `modules` WHERE `activ` = \"yes\" AND `file` LIKE \"core%".$module.".class.".ROOT_EX."\"", true);
				self::$load_hooks = array();
				while($row = $db->fetch_assoc()) {
					self::$load_hooks[$row['module']] = true;
				}
				$cache->set("load_hooks", self::$load_hooks);
			} else {
				self::$load_hooks = $cache->get("load_hooks");
			}
		}
		if(!isset(self::$load_hooks[$module])) {
			return false;
		}
		return self::ExecHooks($module, $param);
	}
	
	final public static function load_modules($file, $load) {
		if(!defined("WITHOUT_DB") && (defined("IS_INSTALLER") || !self::init_db()->connected())) {
			return false;
		}
		if(!defined("WITHOUT_DB") || !defined("START_VERSION")) {
			return true;
		}
		if(self::CheckVersion("3.1", START_VERSION)) {
			return true;
		}
		if(self::CheckVersion("3.5", START_VERSION)) {
			$files = str_replace(array(ROOT_PATH, "core".DS."modules".DS, $load), "", $file);
			if(!is_subclass_of($files, "modules")) {
				modules::manifest_set(array('dependency_modules', $files), $file);
			}
		}
		if(defined("WITHOUT_DB")) {
			if(file_exists(ROOT_PATH."core".DS."modules".DS."loader.".ROOT_EX)) {
				$modulesLoad = array();
				include(ROOT_PATH."core".DS."modules".DS."loader.".ROOT_EX);
				if(isset($modulesLoad[$file])) {
					return true;
				}
			} else if(file_exists(ROOT_PATH."core".DS."modules".DS."loader.default.".ROOT_EX)) {
				$modulesLoad = array();
				include(ROOT_PATH."core".DS."modules".DS."loader.default.".ROOT_EX);
				if(isset($modulesLoad[$file])) {
					return true;
				}
			}
			return false;
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

	final private static function init_modules() {
		if(defined("WITHOUT_DB") || defined("IS_INSTALLER") || !self::init_db()->connected()) {
			return array();
		}
		$cache = self::init_cache();
		if(!$cache->exists("modules")) {
//INSERT INTO `modules` SET activ = "yes", page = "reg", module = "reg_email"
			self::init_db()->doquery("SELECT `page`, `module`, `method`, `param`, `tpl` FROM `modules` WHERE `activ` = \"yes\"", true);
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
	
	final private static function parsePrimary($files) {
		$pr = -1;
		for($i=0;$i<sizeof($files);$i++) {
			if(isset($files) && isset($files[$i]) && isset($files[$i]->attributes()->primary)) {
				$pr = $i;
				break;
			}
		}
		return $pr;
	}

	final private static function ReadRoot(&$arr, $dir = "") {
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

	final private static function SearchOnArray($file, $array = array()) {
		$array = array_values($array);
		$file = (string) $file;
		for($i=0;$i<sizeof($array);$i++) {
			if(strpos($array[$i], $file) !== false) {
				return $array[$i];
			}
		}
		return "";
	}

	final private static function ReadXML($xml) {
		if(!defined("WITHOUT_DB")) {
			$db = self::init_db();
		}
		$files_root = array();
		self::ReadRoot($files_root);
		$sql = "";
		$parsePrimary = -1;
		if(isset($xml->files) && isset($xml->files->file)) {
			$parsePrimary = self::parsePrimary($xml->files->file);
		}
		$name = false;
		if(isset($xml->install) && sizeof($xml->install)>0 && isset($xml->uninstall) && sizeof($xml->uninstall)>0 && isset($xml->files) && $parsePrimary>=0 && isset($xml->files->file[$parsePrimary]) && isset($xml->files->file[$parsePrimary]->attributes()->path)) {
			if(isset($xml->sql)) {
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
								$sql .= "DROP TABLE IF EXISTS `".$ch[$num]->attributes()->table."`!;";
							}
							$sql .= "CREATE TABLE IF NOT EXISTS `".$ch[$num]->attributes()->table."` (".$sqli.") ENGINE=";
							if(isset($ch[$num]->attributes()->engine) && !empty($ch[$num]->attributes()->engine)) {
								$sql .= $ch[$num]->attributes()->engine;
							} else {
								$sql .= "MyISAM";
							}
							$sql .= " DEFAULT CHARSET=".self::get_config("db", "charset")."!;";
						break;
						case "alter":
							if(!isset($ch[$num]->attributes()->table) || empty($ch[$num]->attributes()->table)) {
								continue;
							}
							$table = (string) $ch[$num]->attributes()->table;
							if(!isset($ch[$num]->add)) {
								continue;
							}
							for($yt=0;$yt<sizeof($ch[$num]->add);$yt++) {
								if(!isset($ch[$num]->add[$yt]->{"key"})) {
									continue;
								}
								if(!defined("WITHOUT_DB") && !isset(self::$columns[$table])) {
									$db->doquery("SHOW COLUMNS FROM `".$table."`", true);
									while($row = $db->fetch_assoc()) {
										self::$columns[$table][$row['Field']] = $row['Field'];
									}
								}
								$key = (string) $ch[$num]->add[$yt]->{"key"};
								if(isset(self::$columns[$table][$key])) {
									continue;
								}
								$par = (string) $ch[$num]->add[$yt]->{"param"};
								$type = (((string) $ch[$num]->add[$yt]->{"type"})=="KEY" ? "KEY `".$key."` (`".$key."`)" : "FULLTEXT `".$key."` (`".$key."`)");
								$sqli = "`".$key."` ".$par.", ADD ".$type;
								$sql .= "ALTER TABLE `".$ch[$num]->attributes()->table."` ADD ".$sqli."!;";
							}
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
								$sql .= "DELETE FROM `".$ch[$num]->attributes()->table."` WHERE ".(sizeof($where)>0 ? implode(" ".$mod." ", $where) : "1")."!;";
							}
						break;
						case "drop":
							if(!isset($ch[$num]->attributes()->table) || empty($ch[$num]->attributes()->table)) {
								continue;
							}
							$sql .= "DROP TABLE IF EXISTS `".$ch[$num]->attributes()->table."`!;";
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
							$sql .= "INSERT".$mod." INTO `".$ch[$num]->attributes()->table."` SET ".$sqli."!;";
						break;
					}
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
				$sql .= "INSERT INTO `modules` SET ".implode(", ", array_map("self::insertModule", array_keys($param), array_values($param)))."!;";
			}
			// register all files for module
			$fileList = 0;
			for($i=0;$i<sizeof($xml->files->file);$i++) {
				$name = "name=".$xml->info->attributes()->module;
				$sql .= "INSERT INTO `modules` SET `file` = \"".self::SearchOnArray($xml->files->file[$i]->attributes()->path, $files_root)."\", `module` = \"".(isset($xml->install->type) ? $xml->install->type : "site")."_-_".$xml->info->attributes()->module."\", `type` = \"".(isset($xml->install->type) ? $xml->install->type : "site")."\"!;";
				$fileList++;
			}
		}
		if(!defined("WITHOUT_DB")) {
			if(empty($sql) || $fileList==0) {
				return "Error sql configuration";
			}
			if(strpos($sql, "!;")!==false) {
				$exp = explode("!;", $sql);
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
		}
		return $name;
	}
	
	final private static function insertModule($k, $v) {
		return "`".$k."` = \"".$v."\"";
	}
	
	final public static function Install($module, $file = false, $names = "") {
		if($file) {
			if(!file_exists(ROOT_PATH."core".DS."cache".DS."system".DS.$module.".tar")) {
				return "File archive is not exists";
			}
			try {
				$tar_object = new Archive_Tar(ROOT_PATH."core".DS."cache".DS."system".DS.$module.".tar", "gz");
				$list = $tar_object->listContent();
				if(is_array($list) && sizeof($list)>0) {
					$tar_object->extractModify(ROOT_PATH, ucfirst($names)."/");
					cardinal::RegAction("Распаковка модуля \"".$module."\"");
				}
			} catch(Exception $ex) {
				return "Error unzip file";
			}
			unlink(ROOT_PATH."core".DS."cache".DS."system".DS.$module.".tar");
			return true;
		} else {
			if(!file_exists(ROOT_PATH."core".DS."modules".DS."xml".DS.$module.".xml")) {
				return "File configuration is not exists";
			}
			try {
				$xml = simplexml_load_string(file_get_contents(ROOT_PATH . "core" . DS . "modules" . DS . "xml" . DS . $module . ".xml"));
			} catch(Exception $ex) {
				return "Falled parse configuration";
			}
			$return = self::ReadXML($xml);
			if(strpos($return, "name=")!==false) {
				$exp = explode("=", $return);
				return self::Install($module, true, $exp[1]);
			} else {
				return $return;
			}
		}
	}
	
	final private static function FindXML($module) {
		$files = read_dir(ROOT_PATH . "core" . DS . "modules" . DS . "xml" . DS);
		for($i=0;$i<sizeof($files);$i++) {
			if($files[$i]=="index.php") {
				continue;
			}
			try {
				$xml = simplexml_load_string(file_get_contents(ROOT_PATH . "core" . DS . "modules" . DS . "xml" . DS . $files[$i]));
				if(isset($xml->info) && isset($xml->info->attributes()->module) && $xml->info->attributes()->module==$module) {
					return str_Replace(".xml", "", $files[$i]);
				}
			} catch(Exception $ex) {
				return $module;
			}
		}
	}
	
	final private static function UnInstallFile($module, $file = "") {
		if(empty($file)) {
			$file = $module;
		}
		if(!defined("WITHOUT_DB")) {
			$db = self::init_db();
			$db->doquery("SELECT `file` FROM `modules` WHERE `module` LIKE \"%".$module."\"", true);
			if($db->num_rows()>0) {
				while($files = $db->fetch_assoc()) {
					if(!empty($files['file']) && file_exists(ROOT_PATH.$files['file'])) {
						unlink(ROOT_PATH.$files['file']);
					}
				}
				$db->doquery("DELETE FROM `modules` WHERE `module` LIKE \"%".$module."\"");
			}
			if(file_exists(ROOT_PATH . "core" . DS . "modules" . DS . "xml" . DS . $file . ".xml")) {
				unlink(ROOT_PATH . "core" . DS . "modules" . DS . "xml" . DS . $file . ".xml");
			}
			cardinal::RegAction("Удаление файлов модуля \"".$file."\"");
			return true;
		} else {
			return true;
		}
	}

	final public static function UnInstall($module) {
		$moduleFile = self::FindXML($module);
		if(!file_exists(ROOT_PATH."core".DS."modules".DS."xml".DS.$moduleFile.".xml")) {
			return self::UnInstallFile($module, $moduleFile);
		}
		if(!defined("WITHOUT_DB")) {
			$db = self::init_db();
		}
		$files_root = array();
		self::ReadRoot($files_root);
		try {
			$xml = simplexml_load_string(file_get_contents(ROOT_PATH . "core" . DS . "modules" . DS . "xml" . DS . $moduleFile . ".xml"));
		} catch(Exception $ex) {
			return self::UnInstallFile($module, $moduleFile);
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
			
			if(!defined("WITHOUT_DB")) {
				$db->doquery("SELECT `file` FROM `modules` WHERE `module` LIKE \"%".$xml->info->attributes()->module."\"", true);
				while($files = $db->fetch_assoc()) {
					if(file_exists(ROOT_PATH.$files['file'])) {
						unlink(ROOT_PATH.$files['file']);
					}
				}
				$sql .= "DELETE FROM `modules` WHERE `module` LIKE \"%".$xml->info->attributes()->module."\";";
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
				if(file_exists(ROOT_PATH . "core" . DS . "modules" . DS . "xml" . DS . $moduleFile . ".xml")) {
					unlink(ROOT_PATH . "core" . DS . "modules" . DS . "xml" . DS . $moduleFile . ".xml");
				}
			}
			cardinal::RegAction("Удаление файлов модуля \"".$moduleFile."\"");
			return true;
		} else {
			return self::UnInstallFile($module, $moduleFile);
		}
	}
	
	final public static function use_modules($page, $params = array()) {
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
				if(self::ChangeList($mod, $method)) {
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
	
	final private static function ChangeList($mod, $name) {
		$className = get_class($mod);
		$reflection = new ReflectionClass($className);
		return $reflection->getMethod("change_db")->class != "modules";
	}

	final public static function change_db($page, $db) {
		$modules = self::init_modules();
		if(isset($modules[$page])) {
			for($i=0;$i<sizeof($modules[$page]);$i++) {
				if(isset($modules[$page][$i]['module']) && class_exists($modules[$page][$i]['module'])) {
					$class = $modules[$page][$i]['module'];
					$mod = new $class();
					if(self::ChangeList($mod, "change_db")) {
						$db = $mod->change_db($db);
					}
					unset($mod);
				}
			}
		}
	return $db;
	}

	final public static function select_db($page, $db) {
		$modules = self::init_modules();
		for($i=0;$i<sizeof($modules[$page]);$i++) {
			if(isset($modules[$page][$i]['module']) && class_exists($modules[$page][$i]['module'])) {
				$class = $modules[$page][$i]['module'];
				$mod = new $class();
				if(self::ChangeList($mod, "select_db")) {
					$db = $mod->select_db($db);
				}
				unset($mod);
			}
		}
	return $db;
	}
	
	final public static function CheckNewVersion($module) {
		if(defined("WITHOUT_DB")) {
			return false;
		}
		if(!file_exists(ROOT_PATH . "core" . DS . "modules" . DS . "xml" . DS . $module . ".xml")) {
			return false;
		}
		try {
			$xml = simplexml_load_string(file_get_contents(ROOT_PATH . "core" . DS . "modules" . DS . "xml" . DS . $module . ".xml"));
		} catch(Exception $ex) {
			return false;
		}
		$version = (string) $xml->info->version;
		$file = new Parser(SERVER_MODULES."shop/search/api/".$module."/yaml?CV=".VERSION."&MV=".$version);
		$file->header();
		$file->header_array();
		$file->init();
		$files = $file->get();
		$hr = $files->getHeaders();
		$file = (string) ($file);
		if(strpos($hr['Content-Type'], "application/x-yaml")===false || $hr['code']!=200) {
			return false;
		}
		$arr = Spyc::YAMLLoadString($files->getHtml());
		if(self::CheckVersion($version, $arr['Version'])) {
			return true;
		} else {
			return false;
		}
	}
	
	final public static function AccessUser($arr) {
		if(is_string($arr) && $arr != "light") {
			self::$access_user = array_merge(self::$access_user, array($arr));
			return true;
		} else if(is_array($arr)) {
			$arr = array_values($arr);
			if(in_array("light", $arr)) {
				return false;
			}
			self::$access_user = array_merge(self::$access_user, $arr);
			return true;
		} else {
			return false;
		}
	}

	final public static function get_user($get) {
	global $user;
		$user = User::load();
		if(in_array($get, self::$access_user)) {
			if(isset($user[$get])) {
				return $user[$get];
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	final public static function manifest_log($select, $set) {
	global $manifest;
		$manifest['log'][$select][] = $set;
	return $manifest;
	}

	final public static function manifest_getlog($select) {
	global $manifest;
		if(array_key_exists($select, $manifest['log'])) {
			return $manifest['log'][$select];
		} else {
			return false;
		}
	return $manifest;
	}

	final public static function manifest_set($select, $set) {
	global $manifest;
		if(is_array($select) && sizeof($select)==3) {
			if(!isset($manifest[$select[0]])) {
				$manifest[$select[0]] = array();
			}
			if(!isset($manifest[$select[0]][$select[1]])) {
				$manifest[$select[0]][$select[1]] = array();
			}
			$manifest[$select[0]][$select[1]][$select[2]] = $set;
		} elseif(is_array($select) && sizeof($select)==2) {
			if(!isset($manifest[$select[0]])) {
				$manifest[$select[0]] = array();
			}
			$manifest[$select[0]][$select[1]] = $set;
		} elseif(is_array($select) && sizeof($select)==1) {
			$manifest[$select[0]] = $set;
		} else {
			$manifest[$select] = $set;
		}
	return $manifest;
	}

	final public static function manifest_get($get) {
	global $manifest;
		if(is_array($get)) {
			if(sizeof($get)==3 && isset($manifest[$get[0]]) && isset($manifest[$get[0]][$get[1]]) && isset($manifest[$get[0]][$get[1]][$get[2]])) {
				return $manifest[$get[0]][$get[1]][$get[2]];
			} elseif(sizeof($get)==2 && isset($manifest[$get[0]]) && isset($manifest[$get[0]][$get[1]])) {
				return $manifest[$get[0]][$get[1]];
			} elseif(sizeof($get)==1 && isset($manifest[$get[0]])) {
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