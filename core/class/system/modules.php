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

class modules {
	
	private static $load_modules = false;
	private static $columns = array();
	private static $access_user = array('id', 'username', 'alt_name', 'level', 'email', 'time_reg', 'last_activ', 'activ', 'avatar');
	
	final public static function checkObject($obj, $name, $checkParent = false) {
		if(gettype($name)!="string") {
			errorHeader();
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
			if(file_exists(PATH_MODELS.$class.".".ROOT_EX)) {
				include_once(PATH_MODELS.$class.".".ROOT_EX);
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
						errorHeader();
						throw new Exception("Error loading model");
						return false;
					}
				}
			} else {
				errorHeader();
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
			errorHeader();
			throw new Exception("Error loading model");
			return false;
		}
	}

	final public static function loadModel($model) {
		return self::loadModels("Model".ucfirst($model), $model);
	}
	
	final public static function loader($class, $standard = array()) {
		if(!class_exists($class, false)) {
			if(file_exists(PATH_MODULES."autoload".DS.$class.".".ROOT_EX)) {
				include_once(PATH_MODULES."autoload".DS.$class.".".ROOT_EX);
			} else if(file_exists(PATH_CLASS.$class.".".ROOT_EX)) {
				include_once(PATH_CLASS.$class.".".ROOT_EX);
			} else if(file_exists(PATH_LOAD_LIBRARY.$class.".".ROOT_EX)) {
				include_once(PATH_LOAD_LIBRARY.$class.".".ROOT_EX);
			}
			if(!class_exists($class, false)) {
				errorHeader();
				throw new Exception('Class is not exists', 6);
			}
			$re_args = array();
			if(method_exists($class, "__construct")) {
				$refMethod = new ReflectionMethod($class,  '__construct');
				$params = $refMethod->getParameters();
				foreach($params as $key => $param) {
					$name = $param->getName();
					if($param->isPassedByReference() && isset($standard[$name])) {
						$re_args[$key] = &$standard[$name];
					} else if(isset($standard[$name])) {
						$re_args[$key] = $standard[$name];
					}
				}
			}
			$refClass = new ReflectionClass($class);
			return $refClass->newInstanceArgs((array) $re_args);
		} else {
			$re_args = array();
			if(method_exists($class, "__construct")) {
				$refMethod = new ReflectionMethod($class,  '__construct');
				$params = $refMethod->getParameters();
				foreach($params as $key => $param) {
					$name = $param->getName();
					if($param->isPassedByReference() && isset($standard[$name])) {
						$re_args[$key] = &$standard[$name];
					} else if(isset($standard[$name])) {
						$re_args[$key] = $standard[$name];
					}
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
	
	final public static function CheckVersion($check, $old = "") {
		return cardinal::CheckVersion($check, $old);
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
			$stripRoot = str_replace(ROOT_PATH, "", PATH_MODULES);
			$files = str_replace(array(ROOT_PATH, $stripRoot, $load), "", $file);
			if(!is_subclass_of($files, "modules")) {
				modules::manifest_set(array('dependency_modules', $files), $file);
			}
		}
		if(defined("WITHOUT_DB")) {
			if(file_exists(PATH_CACHE_USERDATA."modules.json")) {
				$modulesLoad = array();
				$files = file_get_contents(PATH_CACHE_USERDATA."modules.json");
				try {
					$json = json_decode($files, true);
					$modulesLoad = array_merge($modulesLoad, $json);
				} catch(Exception $ex) {}
				$fileCheck = str_replace(str_replace(ROOT_PATH, "", PATH_MODULES), "", $file);
				$fileCheck = str_replace(".class.".ROOT_EX, "", $fileCheck);
				if(isset($modulesLoad[$fileCheck]) && isset($modulesLoad[$fileCheck]['active']) && $modulesLoad[$fileCheck]['active']===true) {
					return true;
				}
			}
			if(file_exists(PATH_MODULES."loader.".ROOT_EX)) {
				$modulesLoad = array();
				include(PATH_MODULES."loader.".ROOT_EX);
				if(isset($modulesLoad[$file])) {
					return true;
				}
			} else if(file_exists(PATH_MODULES."loader.default.".ROOT_EX)) {
				$modulesLoad = array();
				include(PATH_MODULES."loader.default.".ROOT_EX);
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
				$db->doquery("SELECT `file` FROM {{modules}} WHERE `activ` LIKE \"yes\" AND `file` LIKE \"application%\"", true);
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
		User::PathUsers(PATH_CACHE_USERDATA);
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
		if(!isset($manifest['log'])) {
			$manifest['log'] = array();
		}
		if(!isset($manifest['log'][$select])) {
			$manifest['log'][$select] = array();
		}
		$manifest['log'][$select][] = ($set);
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

	final public static function manifest_set($select, $set, $args = array()) {
	global $manifest;
		$isArr = is_array($set);
		if($isArr) {
			$isArr = current($set);
			$isArr = is_array($isArr);
		}
		if(sizeof($args)>0) {
			$set = array("set" => $set, "args" => $args);
		}
		if(is_array($select) && sizeof($select)==3) {
			if(!isset($manifest[$select[0]])) {
				$manifest[$select[0]] = array();
			}
			if(!isset($manifest[$select[0]][$select[1]])) {
				$manifest[$select[0]][$select[1]] = array();
			}
			$manifest[$select[0]][$select[1]][$select[2]] = ($isArr ? array_merge($manifest[$select[0]][$select[1]][$select[2]], $set) : $set);
		} elseif(is_array($select) && sizeof($select)==2) {
			if(!isset($manifest[$select[0]])) {
				$manifest[$select[0]] = array();
			}
			$manifest[$select[0]][$select[1]] = ($isArr ? array_merge($manifest[$select[0]][$select[1]], $set) : $set);
		} elseif(is_array($select) && sizeof($select)==1) {
			$manifest[$select[0]] = ($isArr ? array_merge($manifest[$select[0]], $set) : $set);
		} else {
			$manifest[$select] = ($isArr ? array_merge($manifest[$select], $set) : $set);
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

	final public static function regCssJs($link, $type, $short = true) {
	global $manifest;
		$typeLink = "full";
		if(!$short && $type=="css") {
			$typeLink = "css";
		} else if(!$short && $type=="js") {
			$typeLink = "js";
		}
		if(!isset($manifest['create_js'])) {
			$manifest['create_js'] = array();
		}
		if(!isset($manifest['create_js'][$typeLink])) {
			$manifest['create_js'][$typeLink] = array();
		}
		if(!isset($manifest['create_css'])) {
			$manifest['create_css'] = array();
		}
		if(!isset($manifest['create_css'][$typeLink])) {
			$manifest['create_css'][$typeLink] = array();
		}
		if($type=="css") {
			if(is_array($link) && !isset($link['url'])) {
				foreach($link as $k => $linker) {
					if(is_numeric($k)) {
						if(is_string($linker)) {
							$linker = array("url" => $linker);
						}
						$key = (isset($linker['url']) ? $linker['url'] : current($linker));
						$manifest['create_css'][$typeLink][$key] = $linker;
					} else if(is_string($k)) {
						if(is_string($linker)) {
							$linker = array("url" => $linker);
						}
						$manifest['create_css'][$typeLink][$k] = $linker;
					}
				}
			} else {
				if(is_string($link)) {
					$link = array("url" => $link);
				}
				$key = (isset($link['url']) ? $link['url'] : current($link));
				$manifest['create_css'][$typeLink][$key] = $link;
			}
		} else {
			if(is_array($link) && !isset($link['url'])) {
				foreach($link as $k => $linker) {
					if(is_numeric($k)) {
						if(is_string($linker)) {
							$linker = array("url" => $linker);
						}
						$key = (isset($linker['url']) ? $linker['url'] : current($linker));
						$manifest['create_js'][$typeLink][$key] = $linker;
					} else if(is_string($k)) {
						if(is_string($linker)) {
							$linker = array("url" => $linker);
						}
						$manifest['create_js'][$typeLink][$k] = $linker;
					}
				}
			} else {
				if(is_string($link)) {
					$link = array("url" => $link);
				}
				$key = (isset($link['url']) ? $link['url'] : current($link));
				$manifest['create_js'][$typeLink][$key] = $link;
			}
		}
		return $manifest;
	}

	final public static function create_table($table_name, $fields, $force = false) {
		$db = self::init_db();
		if($force) {
			$db->query("DROP TABLE IF EXISTS {{".$table_name."}};");
		}
		$db->query("CREATE TABLE IF NOT EXISTS {{".$table_name."}} (".$fields.") ENGINE=MyISAM;");
		return true;
	}
	
	final public static function drop_table($table_name) {
		$db = self::init_db();
		$db->query("DROP TABLE IF EXISTS {{".$table_name."}};");
		return true;
	}
	
	final public static function add_fields($table_name, array $fields) {
		$db = self::init_db();
		$exists = $db->getTable($table_name);
		foreach($fields as $k => $v) {
			if($exists && !in_array($k, $db->getTable($table_name))) {
				$comment = "";
				if(is_array($v) && isset($v['comment'])) {
					$comment = $v['comment'];
					$v = $v['value'];
				}
				$db->query("ALTER TABLE {{".$table_name."}} ADD `".$k."` ".(strpos($v, "CHARACTER")!==false ? $v : $v." CHARACTER SET ".self::get_config("db", "charset")." COLLATE ".self::get_config("db", "charset")."_general_ci").(!empty($comment) ? " COMMENT ".db::escape($comment) : ""));
			}
		}
		return true;
	}
	
	final public static function remove_fields($table_name, array $fields) {
		$db = self::init_db();
		$exists = $db->getTable($table_name);
		foreach($fields as $k => $v) {
			if($exists && in_array($v, $db->getTable($table_name))) {
				$db->query("ALTER TABLE {{".$table_name."}} DROP COLUMN `".$v."`");
			}
		}
		return true;
	}
	
	final public static function modify_fields($table_name, array $fields) {
		$db = self::init_db();
		$exists = $db->getTable($table_name);
		foreach($fields as $k => $v) {
			$or = $k;
			if(isset($v['orName'])) {
				$or = $v['orName'];
				unset($v['orName']);
				$v = implode(" ", $v);
			}
			if($exists && in_array($or, $exists)) {
				$comment = "";
				if(is_array($v) && isset($v['comment'])) {
					$comment = $v['comment'];
					$v = $v['value'];
				}
				$db->query("ALTER TABLE {{".$table_name."}} CHANGE `".$or."` `".$k."` ".(strpos($v, "CHARACTER")!==false ? $v : $v." CHARACTER SET ".self::get_config("db", "charset")." COLLATE ".self::get_config("db", "charset")."_general_ci").(!empty($comment) ? " COMMENT ".db::escape($comment) : ""));
			}
		}
		return true;
	}

	final public static function initialize($class, $path = "") {
		$arr = array();
		if(file_exists(PATH_CACHE_USERDATA."modules.json")) {
			$file = file_get_contents(PATH_CACHE_USERDATA."modules.json");
			$arrs = json_decode($file, true);
			$arr = array_merge($arr, $arrs);
			if(isset($arr[$class]) && isset($arr[$class]['active']) && $arr[$class]['active']!==true) {
				return false;
			}
			if(!isset($arr[$class]['installTime']) && class_exists($class, false) && method_exists($class, "installation")) {
				call_user_func_array(array(&$class, "installation"), array());
				if(!isset($arr[$class])) {
					$arr[$class] = array();
				}
				$arr[$class] = array_merge($arr[$class], array("installTime" => time(), "version" => (property_exists($class, "version") ? $class::$version : "0.1")));
				@file_put_contents(PATH_CACHE_USERDATA."modules.json", json_encode($arr));
				cardinal::RegAction("Установка модуля \"".$class."\" версии ".(property_exists($class, "version") ? $class::$version : "0.1"));
			}
			if(isset($arr[$class]['installTime']) && class_exists($class, false) && isset($arr[$class]['version']) && property_exists($class, "version") && $class::$version > $arr[$class]['version']) {
				if(method_exists($class, "updater")) {
					call_user_func_array(array(&$class, "updater"), array("version" => $arr[$class]['version']));
				}
				if(!isset($arr[$class])) {
					$arr[$class] = array();
				}
				$arr[$class] = array_merge($arr[$class], array("updateTime" => time(), "version" => $class::$version));
				@file_put_contents(PATH_CACHE_USERDATA."modules.json", json_encode($arr));
				cardinal::RegAction("Обновление модуля \"".$class."\" с версии ".$arr[$class]['version']." до версии ".$class::$version);
			}
		}
		self::manifest_log('init_modules', array($class, $path));
		return true;
	}

	final public static function actived($class = "", $set = "") {
		if($class==="") {
			$d = debug_backtrace();
			if(isset($d[0]) && isset($d[0]['file'])) {
				$d = $d[0]['file'];
				$d = basename($d);
				$d = str_replace(array(".class.".ROOT_EX), "", $d);
				$class = $d;
			} else {
				throw new Exception("Error get module name", 1);
				die();
			}
		}
		$arr = array();
		if(file_exists(PATH_CACHE_USERDATA."modules.json")) {
			$file = file_get_contents(PATH_CACHE_USERDATA."modules.json");
			$arrs = json_decode($file, true);
			$arr = array_merge($arr, $arrs);
		}
		if($set!=="") {
			if(!isset($arr[$class])) {
				$arr[$class] = array();
			}
			$arr[$class]['active'] = $set;
			if(!is_writable(PATH_CACHE_USERDATA)) {
				@chmod(PATH_CACHE_USERDATA, 0777);
			}
			@file_put_contents(PATH_CACHE_USERDATA."modules.json", json_encode($arr));
			return true;
		} else {
			return (isset($arr[$class]) && isset($arr[$class]['active']) && $arr[$class]['active']===true ? $arr[$class]['active'] : false);
		}
	}

	final public static function setLangPanel() {
		global $lang;
		$rLang = Route::param('lang');
		if(!empty($rLang)) {
			$tmp = self::init_templates();
			$langs = self::init_lang();
			$langs->set_lang($rLang);
			Route::SetLang($rLang);
			$tmp->assign_var("lang", $rLang);
			$tmp->assign_var("lang_url", self::langURL());
			return array("lang" => $rLang, "langDB" => ucfirst($rLang));
		} else {
			return array();
		}
	}
	
	final private static function langURL() {
		$server = HTTP::getServer("REDIRECT_URL");
		if(!$server) {
			return self::get_config("default_http_local");
		}
		$REDIRECT_URL = $server;
		$Lang = Route::param('lang');
		$REDIRECT_URL = preg_replace('#'.self::get_config("default_http_local").$Lang.'#', '', $REDIRECT_URL);
		if(empty($REDIRECT_URL)) {
			$REDIRECT_URL = self::get_config("default_http_local");
		}
		return $REDIRECT_URL;
	}

}

?>