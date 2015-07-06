<?php
/*
*
* Version Engine: 1.25.5a7
* Version File: 2
*
* 2.2
* add checker connection to db and fix core
*
* 2.3
* add support lang without set variable for select lang pack
*
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

class lang {

	private static $lang = "ru";

	public static function lang_db() {
		if(!db::connected()) {
			return "";
		}
		if(!cache::Exists("lang_".self::$lang)) {
			db::doquery("SELECT orig, translate FROM lang WHERE lang = \"".self::$lang."\"", true);
			$langs = array();
			while($langs = db::fetch_array()) {
				$langs[$langs['orig']] = $langs['translate'];
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
		$ulang = (!empty($user['lang']) ? $user['lang'] : "");
		if(!empty($ulang)) {
			self::$lang = $ulang;
		} elseif(!empty($clang)) {
			self::$lang = $clang;
		}
	}

	public static function set_lang($langs) {
		self::$lang = $langs;
	}

	public static function init_lang() {
	global $manifest;
		$lang=array();
		if(isset($manifest['lang']['main']) && file_Exists(ROOT_PATH."core/lang/".self::$lang."/".$manifest['lang']['main'].".php")) {
			include(ROOT_PATH."core/lang/".self::$lang."/".$manifest['lang']['main'].".php");
			return array_merge($lang, self::lang_db());
		}
		if(file_exists(ROOT_PATH."core/lang/".self::$lang."/main.php")) {
			include(ROOT_PATH."core/lang/".self::$lang."/main.php");
			$db_lang = self::lang_db();
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
		if(isset($manifest['lang'][$page]) && file_Exists(ROOT_PATH."core/lang/".self::$lang."/".$manifest['lang'][$page].".php")) {
			include(ROOT_PATH."core/lang/".self::$lang."/".$manifest['lang'][$page].".php");
			return array_merge($lang, self::lang_db());
		}
		if(file_exists(ROOT_PATH."core/lang/".self::$lang."/".$page.".php")) {
			include(ROOT_PATH."core/lang/".self::$lang."/".$page.".php");
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