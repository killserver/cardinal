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

$phpEx = substr(strrchr(__FILE__, '.'), 1);
if(!defined("ROOT_EX") && strpos($phpEx, '/') === false) {
	define("ROOT_EX", $phpEx);
}

/**
 * Class lang
 */
class lang implements ArrayAccess {

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
		$fileLang = self::merge(self::$lang, "", "", "get");
		return array_merge($langs, $fileLang);
	}

	final public static function in_array_strpos($str, $arr, $rebuild = false) {
		$ret = false;
		$arr = array_values($arr);
		for($i=0;$i<sizeof($arr);$i++) {
			if($rebuild) {
				$res = strpos($arr[$i], $str)!==false;
			} else {
				$res = strpos($str, $arr[$i])!==false;
			}
			if($res) {
				$ret = true;
				break;
			}
		}
		return $ret;
	}

	final private static function read_dir($dir, $type = "all", $exclusions = array()) {
		if(function_exists("read_dir")) {
			return read_dir($dir, $type, false, false, $exclusions);
		}
		$exclusions[] = ".";
		$exclusions[] = "..";
		$files = array();
		if(is_dir($dir)) {
			if($dh = dir($dir)) {
				while(($file = $dh->read()) !== false) {
					if(self::in_array_strpos($file, $exclusions, true)) {
						continue;
					}
					if(($type=="dir" || is_file($dir.$file)) && (($type=="dir" || $type=="all") || (is_array($type) ? self::in_array_strpos($file, $type) : strpos($file, $type)!==false)) && $file!="." && $file!=".." && $file!="index.".ROOT_EX && $file!="index.html" && $file!=".htaccess") {
						$files[] = $file;
					}
				}
			$dh->close();
			}
		}
	return $files;
	}
	
	final private static function nsubstr($text, $start, $end = "") {
		if(empty($end)) {
			$end = strlen($text);
		}
		return substr($text, $start, $end);
	}
	
	final public static function support($clear = false) {
		global $mainLangSite;
		$arr = array();
		$mainLang = (modules::manifest_get("mainLang")!==false ? modules::manifest_get("mainLang") : (!is_null($mainLangSite) ? $mainLangSite : "ru"));
		$arr["lang".$mainLang.".db"] = "lang".$mainLang.".db";
		$dirLangs = defined("PATH_CACHE_LANGS") ? PATH_CACHE_LANGS : dirname(__FILE__).DIRECTORY_SEPARATOR;
		$dirLangs = self::read_dir($dirLangs, ".db");
		$arrLangs = array();
		for($i=0;$i<sizeof($dirLangs);$i++) {
			if(strpos($dirLangs[$i], "lang")===false) {
				unset($dirLangs[$i]);
			} else {
				$arrLangs[$dirLangs[$i]] = $dirLangs[$i];
			}
		}
		$arr = array_merge($arr, $arrLangs);
		$pathLangs = (!defined("PATH_LANGS") ? dirname(__FILE__).DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR : PATH_LANGS);
		$langs = self::read_dir($pathLangs, "dir");
		$arrLangs = array();
		for($t=0;$t<sizeof($langs);$t++) {
			if(strpos($langs[$t], ".html")===false && strpos($langs[$t], ".php")===false) {
				$arrLangs["lang".$langs[$t].".db"] =  "lang".$langs[$t].".db";
			}
		}
		$arr = array_merge($arr, $arrLangs);
		$arr = array_values($arr);
		if($clear) {
			for($i=0;$i<sizeof($arr);$i++) {
				$arr[$i] = self::nsubstr($arr[$i], 4, -3);
			}
		}
		return $arr;
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
			(Validate::CheckType($translate, "string") || Validate::CheckType($translate, "array")) && Validate::not_empty($translate)
		) {
			return self::merge($lang, $orig, $translate, "edit");
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
			if(class_exists("config") && method_exists("config", "Select")) {
				$clang = config::Select('lang');
				$ulang = (isset($user['lang']) && !empty($user['lang']) ? $user['lang'] : "");
				if(!empty($ulang)) {
					self::$lang = $ulang;
				} elseif(!empty($clang)) {
					self::$lang = $clang;
				}
				if(($t = config::Select('default_lang'))!==false) {
					self::$defaultLang = $t;
				}
			}
		} else {
			self::$lang = $lang;
			if(class_exists("config") && method_exists("config", "Select")) {
				$clang = config::Select('lang');
				$ulang = (isset($user['lang']) && !empty($user['lang']) ? $user['lang'] : "");
				if(!empty($ulang)) {
					self::$defaultLang = $ulang;
				} elseif(!empty($clang)) {
					self::$defaultLang = $clang;
				}
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
		return self::merge($lang, "", "", "check");
	}

    /**
     * @return bool|string Return set language
     */
    final public static function get_lg() {
		return self::$lang;
	}
	
	final private static function hex2bin($hexstr) {
		$n = strlen($hexstr);
		$sbin = "";
		$i = 0;
		while($i<$n) {
			$a = substr($hexstr, $i, 2);
			$c = pack("H*", $a);
			if($i==0) {
				$sbin = $c;
			} else {
				$sbin .= $c;
			}
			$i+=2;
		}
		return $sbin;
	}
	
	final private static function merge($langSelect, $orig = "", $tr = "", $type = "get") {
		global $lang;
		$dirLangs = defined("PATH_CACHE_LANGS") ? PATH_CACHE_LANGS : dirname(__FILE__).DIRECTORY_SEPARATOR;
		if($type=="edit") {
			$fileLang = array();
			if(file_exists($dirLangs."lang".$langSelect.".db") && is_readable($dirLangs."lang".$langSelect.".db")) {
				$file = file_get_contents($dirLangs."lang".$langSelect.".db");
				$fileLang = unserialize(self::hex2bin($file));
			}
			$fileLang = array_merge($fileLang, array($orig => $tr));
			if(is_writable($dirLangs)) {
				file_put_contents($dirLangs."lang".$langSelect.".db", bin2hex(serialize($fileLang)));
				return true;
			} else {
				return false;
			}
		} else if($type=="get") {
			$fileLang = array();
			if(file_exists($dirLangs."lang".$langSelect.".db") && is_readable($dirLangs."lang".$langSelect.".db")) {
				$file = file_get_contents($dirLangs."lang".$langSelect.".db");
				$fileLang = unserialize(self::hex2bin($file));
			}
			return array_replace_recursive($fileLang, $lang);
		} else if($type=="check") {
			return (file_exists($dirLangs."lang".$langSelect.".db") && is_readable($dirLangs."lang".$langSelect.".db")) || $langSelect==self::$defaultLang;
		} else if($type=="merge") {
			$fileLang = array();
			if(file_exists($dirLangs."lang".$langSelect.".db") && is_readable($dirLangs."lang".$langSelect.".db")) {
				$file = file_get_contents($dirLangs."lang".$langSelect.".db");
				$fileLang = unserialize(self::hex2bin($file));
			}
			return array_replace_recursive($fileLang, $orig);
		} else if($type=="del") {
			$fileLang = array();
			if(file_exists($dirLangs."lang".$langSelect.".db") && is_readable($dirLangs."lang".$langSelect.".db")) {
				$file = file_get_contents($dirLangs."lang".$langSelect.".db");
				$fileLang = unserialize(self::hex2bin($file));
			}
			if(isset($fileLang[$orig])) {
				unset($fileLang[$orig]);
				if(is_writable($dirLangs)) {
					file_put_contents($dirLangs."lang".$langSelect.".db", bin2hex(serialize($fileLang)));
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else if($type=="remove") {
			if(file_exists($dirLangs."lang".$langSelect.".db") && is_readable($dirLangs."lang".$langSelect.".db")) {
				unlink($dirLangs."lang".$langSelect.".db");
				return true;
			}
			return false;
		}
	}

	final public static function Remove($lang) {
		if(file_exists(PATH_LANGS.$lang.DS)) {
			$files = scandir(PATH_LANGS.$lang.DS);
			for($i=0;$i<sizeof($files);$i++) {
				if($files[$i]!="." && $files[$i]!=".." && file_exists(PATH_LANGS.$lang.DS.$files[$i])) {
					@unlink(PATH_LANGS.$lang.DS.$files[$i]);
				}
			}
			@unlink(PATH_LANGS.$lang.DS);
		}
		return self::merge($lang, "", "", "remove");
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
		$saveLang = $lang;
		if(isset($manifest['lang']) && isset($manifest['lang']['main']) && defined("PATH_LANGS") && file_Exists(PATH_LANGS.self::$lang.DS.$manifest['lang']['main'].".".ROOT_EX)) {
			include(PATH_LANGS.self::$lang.DS.$manifest['lang']['main'].".".ROOT_EX);
			if($db) {
				$lang = array_merge($saveLang, self::lang_db());
			} else {
				$lang = self::merge(self::$lang, $saveLang, "", "merge");
			}
			return $lang;
		}
		if(defined("PATH_LANGS") && self::merge(self::$lang, "", "", "check")) {
			if(file_exists(PATH_LANGS.self::$defaultLang.DS."main.".ROOT_EX)) {
				include(PATH_LANGS.self::$defaultLang.DS."main.".ROOT_EX);
			}
			$db_lang = array();
			if($db) {
				$db_lang = self::lang_db();
			}
			if(is_array($db_lang) && sizeof($db_lang)>0) {
				$lang = array_merge($lang, $db_lang);
			} else {
				$lang = self::merge(self::$lang, $lang, "", "merge");
			}
			$lang = array_replace_recursive($lang, $saveLang);
			if(file_exists(PATH_MEDIA."config.lang.".ROOT_EX)) {
				include(PATH_MEDIA."config.lang.".ROOT_EX);
			}
			return $lang;
		} else if(defined("PATH_LANGS") && file_exists(PATH_LANGS.self::$lang.DS."main.".ROOT_EX)) {
			include(PATH_LANGS.self::$lang.DS."main.".ROOT_EX);
			$db_lang = array();
			if($db) {
				$db_lang = self::lang_db();
			}
			if(is_array($db_lang) && sizeof($db_lang)>0) {
				$lang = array_merge($lang, $db_lang);
			} else {
				$lang = self::merge(self::$lang, $saveLang, "", "merge");
			}
			$lang = array_replace_recursive($lang, $saveLang);
			if(file_exists(PATH_MEDIA."config.lang.".ROOT_EX)) {
				include(PATH_MEDIA."config.lang.".ROOT_EX);
			}
			return $lang;
		} elseif(!defined("PATH_LANGS") && file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR.self::$lang.DIRECTORY_SEPARATOR."main.".ROOT_EX)) {
			include(dirname(__FILE__).DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR.self::$lang.DIRECTORY_SEPARATOR."main.".ROOT_EX);
			$db_lang = array();
			if($db) {
				$db_lang = self::lang_db();
			}
			if(is_array($db_lang) && sizeof($db_lang)>0) {
				$lang = array_merge($lang, $db_lang);
			} else {
				$lang = self::merge(self::$lang, $lang, "", "merge");
			}
			$lang = array_replace_recursive($lang, $saveLang);
			return $lang;
		} elseif(self::merge(self::$lang, "", "", "check")) {
			$lang = self::merge(self::$lang, "", "", "get");
			$lang = array_replace_recursive($lang, $saveLang);
			return $lang;
		} elseif(defined("PATH_LANGS") && file_exists(PATH_LANGS.self::$defaultLang.DS."main.".ROOT_EX)) {
			include(PATH_LANGS.self::$defaultLang.DS."main.".ROOT_EX);
			if($db) {
				$db_lang = self::lang_db();
			} else {
				$db_lang = array();
			}
			if(is_array($db_lang) && sizeof($db_lang)>0) {
				$lang = array_merge($lang, $db_lang);
			} else {
				$lang = self::merge(self::$defaultLang, $lang, "", "merge");
			}
			$lang = array_replace_recursive($lang, $saveLang);
			if(file_exists(PATH_MEDIA."config.lang.".ROOT_EX)) {
				include(PATH_MEDIA."config.lang.".ROOT_EX);
			}
			return $lang;
		} elseif(!defined("PATH_LANGS") && file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR.self::$defaultLang.DIRECTORY_SEPARATOR."main.".ROOT_EX)) {
			include(dirname(__FILE__).DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR.self::$defaultLang.DS."main.".ROOT_EX);
			if($db) {
				$db_lang = self::lang_db();
			} else {
				$db_lang = array();
			}
			if(is_array($db_lang) && sizeof($db_lang)>0) {
				$lang = array_merge($lang, $db_lang);
			} else {
				$lang = self::merge(self::$defaultLang, $lang, "", "merge");
			}
			$lang = array_replace_recursive($lang, $saveLang);
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
		if(is_string($name) && is_string($sub) && !empty($sub) && isset($lang[$name][$sub])) {
			return $lang[$name][$sub];
		} else if(is_string($name) && isset($lang[$name])) {
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
		if(!isset($lang) || !is_Array($lang)) {
			$lang = array();
		}
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
    final public static function include_lang($page, $db = true, $single = false) {
	global $lang, $user, $manifest;
		if($single) {
			$lang = array();
		}
		if(class_exists("config") && method_exists("config", "Select")) {
			$clang = config::Select('lang');
			$ulang = (isset($user['lang']) && !empty($user['lang']) ? $user['lang'] : "");
			if(!empty($ulang)) {
				self::$lang = $ulang;
			} else if(empty(self::$lang)) {
				self::$lang = $clang;
			}
		} else {
			self::$lang = "ru";
		}
		if(defined("PATH_LANGS") && isset($manifest['lang']) && isset($manifest['lang'][$page]) && file_Exists(PATH_LANGS.self::$lang.DS.$manifest['lang'][$page].".".ROOT_EX)) {
			include(PATH_LANGS.self::$lang.DS.$manifest['lang'][$page].".".ROOT_EX);
			$langs = self::lang_db();
			$lang = array_merge($lang, $langs);
		} elseif(defined("PATH_LANGS") && file_exists(PATH_LANGS.self::$lang.DS.$page.".".ROOT_EX)) {
			include(PATH_LANGS.self::$lang.DS.$page.".".ROOT_EX);
			if($db) {
				$langs = self::lang_db();
				$lang = array_merge($lang, $langs);
			}
			$lang = self::merge(self::$lang, $lang, "", "merge");
		} elseif(!defined("PATH_LANGS") && file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR.self::$lang.DIRECTORY_SEPARATOR.$page.".".ROOT_EX)) {
			include(dirname(__FILE__).DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR.self::$lang.DIRECTORY_SEPARATOR.$page.".".ROOT_EX);
			if($db) {
				$langs = self::lang_db();
				$lang = array_merge($lang, $langs);
			}
			$lang = self::merge(self::$lang, $lang, "", "merge");
		} elseif(self::merge(self::$lang, "", "", "check")) {
			$lang = self::merge(self::$lang, "", "", "get");
		} elseif(defined("PATH_LANGS") && file_exists(PATH_LANGS.self::$defaultLang.DS.$page.".".ROOT_EX)) {
			include(PATH_LANGS.self::$defaultLang.DS.$page.".".ROOT_EX);
			if($db) {
				$langs = self::lang_db();
				$lang = array_merge($lang, $langs);
			}
			$lang = self::merge(self::$defaultLang, $lang, "", "merge");
		} elseif(!defined("PATH_LANGS") && file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR.self::$defaultLang.DIRECTORY_SEPARATOR.$page.".".ROOT_EX)) {
			include(dirname(__FILE__).DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR.self::$defaultLang.DIRECTORY_SEPARATOR.$page.".".ROOT_EX);
			if($db) {
				$langs = self::lang_db();
				$lang = array_merge($lang, $langs);
			}
			$lang = self::merge(self::$defaultLang, $lang, "", "merge");
		}
		return $lang;
	}

	final public static function translateSupport() {
		$p = new Parser(LANGUAGE_SUPPORT_SERVICE."?".time());
		$echo = $p->get();
		if(is_serialized($echo)) {
			$arr = unserialize($echo);
		} else {
			$arr = array();
		}
		return $arr;
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
	
	public function offsetSet($offset, $value) {
		if(is_null($offset)) {
			self::setLang("", $value);
		} else {
			self::setLang($offset, $value);
		}
    }
	
	public function offsetExists($offset) {
		$lang = self::get_lang($offset);
		return !empty($lang);
	}
	
	public function offsetUnset($offset) {
	global $lang;
		if(isset($lang[$offset])) {
			unset($lang[$offset]);
		}
	}
	
	public function offsetGet($offset) {
		return self::get_lang($offset);
	}

}
?>