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

/**
 * Class lang
 */
class lang {

    /**
     * @var bool|string Set language
     */
    private static $lang = "ru";
    /**
     * @var bool|mixed|string Set default language
     */
    private static $defaultLang = "ru";

    /**
     * @return array|bool|string Initialize language in database
     */
    final public static function lang_db() {
		$langs = array();
		if(defined("WITHOUT_DB")) {
			$fileLang = self::merge(self::$lang, "", "", "get");
			return array_merge($langs, $fileLang);
		}
		if(!cache::Exists("lang_".self::$lang)) {
			db::doquery("SELECT `orig`, `translate` FROM `lang` WHERE `lang` LIKE \"".self::$lang."\"", true);
			while($lang = db::fetch_assoc()) {
				$langs[$lang['orig']] = $lang['translate'];
			}
			cache::Set("lang_".self::$lang, $langs);
			db::free();
		} else {
			$langs = cache::Get("lang_".self::$lang);
		}
	return $langs;
	}

    /**
     * Update element in database
     * @param string $lang Needed language
     * @param string $orig Original word
     * @param string $translate To translate word
     * @return bool Result change element in database
     */
    final public static function Update($lang, $orig, $translate) {
		if(
			Validate::CheckType($lang, "string") && Validate::not_empty($lang)
				&&
			Validate::CheckType($orig, "string") && Validate::not_empty($orig)
				&&
			Validate::CheckType($translate, "string") && Validate::not_empty($translate)
		) {
			if(!defined("WITHOUT_DB") && db::connected()) {
				db::doquery("REPLACE INTO `lang` SET `lang` = '".Saves::SaveEscape(Saves::SaveText($lang))."', `orig` = '".Saves::SaveEscape(Saves::SaveText($orig))."', `translate` = '".Saves::SaveEscape(Saves::SaveText($translate))."'");
				return true;
			} else {
				return self::merge($lang, $orig, $translate, "edit");
			}
		} else {
			return false;
		}
	}

    /**
     * lang constructor.
     * @param bool $lang Set needed language
     */
    public function __construct($lang = false) {
	global $user;
		if(is_bool($lang) && $lang === false) {
			$clang = config::Select('lang');
			$ulang = (isset($user['lang']) && !empty($user['lang']) ? $user['lang'] : "");
			if(!empty($ulang)) {
				self::$defaultLang = self::$lang = $ulang;
			} elseif(!empty($clang)) {
				self::$defaultLang = self::$lang = $clang;
			}
		} else {
			self::$lang = $lang;
			$clang = config::Select('lang');
			$ulang = (isset($user['lang']) && !empty($user['lang']) ? $user['lang'] : "");
			if(!empty($ulang)) {
				self::$defaultLang = $ulang;
			} elseif(!empty($clang)) {
				self::$defaultLang = $clang;
			}
		}
	}

    /**
     * @param string $langs Set needed language
     */
    final public static function set_lang($langs) {
		self::$lang = $langs;
	}
	
	final public static function checkLang($lang) {
		$num = 0;
		if(!defined("WITHOUT_DB") && db::connected()) {
			db::doquery("SELECT `orig` FROM `lang` WHERE `lang` LIKE \"".$lang."\"", true);
			$num = db::num_rows();
		}
		return (defined("WITHOUT_DB") ? self::merge($lang, "", "", "check") : true) || (file_exists(ROOT_PATH."core".DS."lang".DS.$lang.DS) || $num>0);
	}

    /**
     * @return bool|string Return set language
     */
    final public static function get_lg() {
		return self::$lang;
	}
	
	final private static function merge($lang, $orig = "", $tr = "", $type = "get") {
		if($type=="edit") {
			$fileLang = array();
			if(file_exists(ROOT_PATH."core".DS."cache".DS."system".DS."lang".$lang.".db") && is_readable(ROOT_PATH."core".DS."cache".DS."system".DS."lang".$lang.".db")) {
				$file = file_get_contents(ROOT_PATH."core".DS."cache".DS."system".DS."lang".$lang.".db");
				$fileLang = unserialize(hex2bin($file));
			}
			$fileLang = array_merge($fileLang, array($orig => $tr));
			if(is_writable(ROOT_PATH."core".DS."cache".DS."system".DS)) {
				file_put_contents(ROOT_PATH."core".DS."cache".DS."system".DS."lang".$lang.".db", bin2hex(serialize($fileLang)));
				return true;
			} else {
				return false;
			}
		} else if($type=="get") {
			$fileLang = array();
			if(file_exists(ROOT_PATH."core".DS."cache".DS."system".DS."lang".$lang.".db") && is_readable(ROOT_PATH."core".DS."cache".DS."system".DS."lang".$lang.".db")) {
				$file = file_get_contents(ROOT_PATH."core".DS."cache".DS."system".DS."lang".$lang.".db");
				$fileLang = unserialize(hex2bin($file));
			}
			return $fileLang;
		} else if($type=="check") {
			return file_exists(ROOT_PATH."core".DS."cache".DS."system".DS."lang".$lang.".db") && is_readable(ROOT_PATH."core".DS."cache".DS."system".DS."lang".$lang.".db");
		} else if($type=="merge") {
			$fileLang = array();
			if(file_exists(ROOT_PATH."core".DS."cache".DS."system".DS."lang".$lang.".db") && is_readable(ROOT_PATH."core".DS."cache".DS."system".DS."lang".$lang.".db")) {
				$file = file_get_contents(ROOT_PATH."core".DS."cache".DS."system".DS."lang".$lang.".db");
				$fileLang = unserialize(hex2bin($file));
			}
			return array_merge($orig, $fileLang);
		} else if($type=="del") {
			$fileLang = array();
			if(file_exists(ROOT_PATH."core".DS."cache".DS."system".DS."lang".$lang.".db") && is_readable(ROOT_PATH."core".DS."cache".DS."system".DS."lang".$lang.".db")) {
				$file = file_get_contents(ROOT_PATH."core".DS."cache".DS."system".DS."lang".$lang.".db");
				$fileLang = unserialize(hex2bin($file));
			}
			if(isset($fileLang[$orig])) {
				unset($fileLang[$orig]);
				if(is_writable(ROOT_PATH."core".DS."cache".DS."system".DS)) {
					file_put_contents(ROOT_PATH."core".DS."cache".DS."system".DS."lang".$lang.".db", bin2hex(serialize($fileLang)));
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
	}
	
	final public static function LangReset($lang, $data) {
		return self::merge($lang, $data, "", "del");
	}

    /**
     * Initialize language panel
     * @param bool $db Used language in database
     * @return array|string Try get all data in selected language
     */
    final public static function init_lang($db = true) {
	global $lang, $manifest;
		if(!is_array($lang) || sizeof($lang)==0) {
			$lang = array();
		}
		if(isset($manifest['lang']['main']) && file_Exists(ROOT_PATH."core".DS."lang".DS.self::$lang.DS.$manifest['lang']['main'].".".ROOT_EX)) {
			include(ROOT_PATH."core".DS."lang".DS.self::$lang.DS.$manifest['lang']['main'].".".ROOT_EX);
			if($db) {
				$lang = array_merge($lang, self::lang_db());
			} else {
				$lang = self::merge(self::$lang, $lang, "", "merge");
			}
			return $lang;
		}
		if(file_exists(ROOT_PATH."core".DS."lang".DS.self::$lang.DS."main.".ROOT_EX)) {
			include(ROOT_PATH."core".DS."lang".DS.self::$lang.DS."main.".ROOT_EX);
			$db_lang = array();
			if($db) {
				$db_lang = self::lang_db();
			}
			if(file_exists(ROOT_PATH."core".DS."media".DS."config.lang.".ROOT_EX)) {
				include(ROOT_PATH."core".DS."media".DS."config.lang.".ROOT_EX);
			}
			if(is_array($db_lang) && sizeof($db_lang)>0) {
				$lang = array_merge($lang, $db_lang);
			} else {
				$lang = self::merge(self::$lang, $lang, "", "merge");
			}
			return $lang;
		} elseif(self::merge(self::$lang, "", "", "check")) {
			$lang = self::merge(self::$lang, "", "", "get");
			return $lang;
		} elseif(file_exists(ROOT_PATH."core".DS."lang".DS.self::$defaultLang.DS."main.".ROOT_EX)) {
			include(ROOT_PATH."core".DS."lang".DS.self::$defaultLang.DS."main.".ROOT_EX);
			if($db) {
				$db_lang = self::lang_db();
			} else {
				$db_lang = array();
			}
			if(file_exists(ROOT_PATH."core".DS."media".DS."config.lang.".ROOT_EX)) {
				include(ROOT_PATH."core".DS."media".DS."config.lang.".ROOT_EX);
			}
			if(is_array($db_lang) && sizeof($db_lang)>0) {
				$lang = array_merge($lang, $db_lang);
			} else {
				$lang = self::merge(self::$defaultLang, $lang, "", "merge");
			}
			return $lang;
		} else {
			return "false";
		}
	}

    /**
     * Try get language in language panel
     * @param string $name Needed language
     * @param string $sub Needed sub language
     * @return string Result returned language
     */
    final public static function get_lang($name, $sub = "") {
	global $lang;
		if(!empty($sub) && isset($lang[$name][$sub])) {
			return $lang[$name][$sub];
		} else if(isset($lang[$name])) {
			return $lang[$name];
		} else {
			return "";
		}
	}

    /**
     * Try set language in language panel
     * @param string $name Needed language
     * @param string $val Value language
     * @param string $sub Needed sub language
     * @return bool Result setting language
     */
    final public static function setLang($name, $val, $sub = "") {
	global $lang;
		if(!empty($sub)) {
			if(!isset($lang[$name])) {
				$lang[$name] = array();
			}
			$lang[$name][$sub] = $val;
			return true;
		} else {
			$lang[$name] = $val;
			return true;
		}
	}

    /**
     * Include file language
     * @param string $page Needed file in language panel
     * @param bool $db Use database after include file
     * @return array|string Language panel
     */
    final public static function include_lang($page, $db = true) {
	global $lang, $user, $manifest;
		$clang = config::Select('lang');
		$ulang = (isset($user['lang']) && !empty($user['lang']) ? $user['lang'] : "");
		if(!empty($ulang)) {
			self::$lang = $ulang;
		} else if(empty(self::$lang)) {
			self::$lang = $clang;
		}
		if(isset($manifest['lang'][$page]) && file_Exists(ROOT_PATH."core".DS."lang".DS.self::$lang.DS.$manifest['lang'][$page].".".ROOT_EX)) {
			include(ROOT_PATH."core".DS."lang".DS.self::$lang.DS.$manifest['lang'][$page].".".ROOT_EX);
			$lang = array_merge($lang, self::lang_db());
			return $lang;
		}
		if(file_exists(ROOT_PATH."core".DS."lang".DS.self::$lang.DS.$page.".".ROOT_EX)) {
			include(ROOT_PATH."core".DS."lang".DS.self::$lang.DS.$page.".".ROOT_EX);
			if($db) {
				$langs = self::lang_db();
				$lang = array_merge($lang, $langs);
			} else {
				$lang = self::merge(self::$lang, $lang, "", "merge");
			}
			return $lang;
		} elseif(self::merge(self::$lang, "", "", "check")) {
			$lang = self::merge(self::$lang, "", "", "get");
			return $lang;
		} elseif(file_exists(ROOT_PATH."core".DS."lang".DS.self::$defaultLang.DS.$page.".".ROOT_EX)) {
			include(ROOT_PATH."core".DS."lang".DS.self::$defaultLang.DS.$page.".".ROOT_EX);
			$langs = self::lang_db();
			if(is_array($langs)) {
				$lang = array_merge($lang, $langs);
			} else {
				$lang = self::merge(self::$defaultLang, $lang, "", "merge");
			}
			return $lang;
		}
	}

    /**
     * Try get element language as object
     * @param string $val Get element in language panel
     * @return mixed Element in language panel
     */
    public function __get($val) {
		return self::get_lang($val);
	}

    /**
     * Try set element language as object
     * @param string $name Name element in language panel
     * @param string $val Value element in language panel
     * @return bool Result setting language
     */
    public function __set($name, $val) {
		return self::setLang($name, $val);
	}

    /**
     * Destructor this class as object
     */
    function __destruct() {
		unset($this);
	}

}
?>