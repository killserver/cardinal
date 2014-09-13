<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die;
}

class lang {

	private static $lang = "";

	static function lang_db() {
	global $db;
		if(!modules::init_cache()->exists("lang_".self::$lang)) {
			$db->doquery("SELECT orig, translate FROM lang WHERE lang = \"".self::$lang."\"", true);
			$langs = array();
			while($langs = $db->fetch_array()) {
				$langs[$langs['orig']] = $langs['translate'];
			}
			modules::init_cache()->set("lang_".self::$lang, $langs);
			$db->free();
		} else {
			$langs = modules::init_cache()->get("lang_".self::$lang);
		}
	return $langs;
	}

	function __construct() {
	global $config, $user;
		$clang = $config['lang'];
		$ulang = (!empty($user['lang']) ? $user['lang'] : "");
		if(!empty($ulang)) {
			self::$lang = $ulang;
		} else {
			self::$lang = $clang;
		}
	}

	static function set_lang($langs) {
		self::$lang = $langs;
	}

	static function init_lang() {
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

	static function include_lang($page) {
	global $lang, $config, $user, $manifest;
		$clang = $config['lang'];
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