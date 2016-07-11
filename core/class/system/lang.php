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
 * add checker connection to db and fix core
 * 2.3
 * add support lang without set variable for select lang pack
 * 2.4
 * add config lang creating in installer
 * 2.5
 * add getting lang now
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

class lang {

	private static $lang = "ru";

	public static function lang_db() {
		$langs = array();
		if(!db::connected()) {
			return $langs;
		}
		if(!cache::Exists("lang_".self::$lang)) {
			db::doquery("SELECT `orig`, `translate` FROM `lang` WHERE lang LIKE \"".self::$lang."\"", true);
			while($lang = db::fetch_array()) {
				$langs[$lang['orig']] = $lang['translate'];
			}
			cache::Set("lang_".self::$lang, $langs);
			db::free();
		} else {
			$langs = cache::Get("lang_".self::$lang);
		}
	return $langs;
	}

	public function __construct() {
	global $user;
		$clang = config::Select('lang');
		$ulang = (isset($user['lang']) && !empty($user['lang']) ? $user['lang'] : "");
		if(!empty($ulang)) {
			self::$lang = $ulang;
		} elseif(!empty($clang)) {
			self::$lang = $clang;
		}
	}

	public static function set_lang($langs) {
		self::$lang = $langs;
	}
	
	public static function get_lg() {
		return self::$lang;
	}

	public static function init_lang($db = true) {
	global $manifest;
		$lang=array();
		if($db) {
			if(isset($manifest['lang']['main']) && file_Exists(ROOT_PATH."core".DS."lang".DS.self::$lang.DS.$manifest['lang']['main'].".php")) {
				include(ROOT_PATH."core".DS."lang".DS.self::$lang.DS.$manifest['lang']['main'].".php");
				return array_merge($lang, self::lang_db());
			}
		}
		if(file_exists(ROOT_PATH."core".DS."lang".DS.self::$lang.DS."main.php")) {
			include(ROOT_PATH."core".DS."lang".DS.self::$lang.DS."main.php");
			if($db) {
				$db_lang = self::lang_db();
			} else {
				$db_lang = array();
			}
			if(file_exists(ROOT_PATH."core".DS."media".DS."config.lang.php")) {
				include(ROOT_PATH."core".DS."media".DS."config.lang.php");
			}
			if(is_array($db_lang)) {
				return array_merge($lang, $db_lang);
			} else {
				return $lang;
			}
		} else {
			return "false";
		}
	}
	
	public static function get_lang($name, $sub=null) {
	global $lang;
		if(!empty($sub) && isset($lang[$name][$sub])) {
			return $lang[$name][$sub];
		} else if(isset($lang[$name])) {
			return $lang[$name];
		} else {
			return "";
		}
	}

	public static function include_lang($page) {
	global $lang, $user, $manifest;
		$clang = config::Select('lang');
		$ulang = (!empty($user['lang']) ? $user['lang'] : "");
		if(!empty($ulang)) {
			self::$lang = $ulang;
		} else {
			self::$lang = $clang;
		}
		if(isset($manifest['lang'][$page]) && file_Exists(ROOT_PATH."core".DS."lang".DS.self::$lang.DS.$manifest['lang'][$page].".php")) {
			include(ROOT_PATH."core".DS."lang".DS.self::$lang.DS.$manifest['lang'][$page].".php");
			return array_merge($lang, self::lang_db());
		}
		if(file_exists(ROOT_PATH."core".DS."lang".DS.self::$lang.DS.$page.".php")) {
			include(ROOT_PATH."core".DS."lang".DS.self::$lang.DS.$page.".php");
			$langs = self::lang_db();
			if(is_array($langs)) {
				return array_merge($lang, $langs);
			} else {
				return $lang;
			}
		}
	}

	function __destruct() {
		unset($this);
	}

}
?>