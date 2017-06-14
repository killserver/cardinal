<?php

class Languages extends Core {
	
	function implodes($k, $v) {
		return $k."[-@-]".$v;
	}
	
	function translate($text, $to) {
		$ret = "";
		if(!config::Select("apiKeyTranslate")) {
			return $text;
		}
		$isArr = false;
		if(is_array($text)) {
			$orText = $text;
			$text = implode("[@]", $text);
			$isArr = true;
		}
		$text = urlencode($text);
		for($i=0;$i<strlen($text);$i+=10000) {
			$subText = substr($text, $i, 10000);
			$p = new Parser("https://translate.yandex.net/api/v1.5/tr.json/translate");
			$p->post(array("text" => $subText, "key" => config::Select("apiKeyTranslate"), "lang" => $to));
			$resp = json_decode($p->get(), true);
			$ret .= (is_string($resp['text']) ? $resp['text'] : current($resp['text']));
		}
		if($isArr) {
			$ret = array_map('trim', explode('[@]', $ret));
			$arr = array();
			$keys = array_keys($orText);
			for($i=0;$i<sizeof($ret);$i++) {
				$arr[$keys[$i]] = $ret[$i];
			}
			return $arr;
		} else {
			return $ret;
		}
	}
	
	function __construct() {
		$langs = "ru";
		if(Arr::get($_GET, 'createLang', false)) {
			$newLang = Arr::get($_GET, 'createLang');
			lang::include_lang("install");
			$this->ParseLang();
			global $lang;
			$arr = array_merge(array(), $lang);
			$admin = ROOT_PATH.ADMINCP_DIRECTORY.DS."temp".DS.config::Select("skins", "admincp").DS;
			$dir = read_dir($admin);
			sort($dir);
			for($z=0;$z<sizeof($dir);$z++) {
				$file = file_get_contents($admin.$dir[$z]);
				preg_match_all("#\{L_(['\"]|)(.+?)(\[(.*?)\]|)\\1\}#", $file, $match);
				for($i=0;$i<sizeof($match[2]);$i++) {
					$arr[$match[2][$i]] = $match[2][$i];
				}
			}
			$admin = ROOT_PATH."skins".DS;
			$dir = read_dir($admin);
			for($z=0;$z<sizeof($dir);$z++) {
				$file = file_get_contents($admin.$dir[$z]);
				preg_match_all("#\{L_(['\"]|)(.+?)(\[(.*?)\]|)\\1\}#", $file, $match);
				for($i=0;$i<sizeof($match[2]);$i++) {
					$arr[$match[2][$i]] = $match[2][$i];
				}
			}
			$admin = ROOT_PATH."skins".DS.config::Select("skins", "skins").DS;
			$dir = read_dir($admin);
			for($z=0;$z<sizeof($dir);$z++) {
				$file = file_get_contents($admin.$dir[$z]);
				preg_match_all("#\{L_(['\"]|)(.+?)(\[(.*?)\]|)\\1\}#", $file, $match);
				for($i=0;$i<sizeof($match[2]);$i++) {
					$arr[$match[2][$i]] = $match[2][$i];
				}
			}
			$arr['lang_ini'] = $newLang;
			foreach($arr as $k => $v) {
				$translate = lang::get_lang($v);
				if(!empty($translate)) {
					$arr[$k] = $v = $translate;
				}
				$v = $this->translate($v, $newLang);
				lang::Update($newLang, $k, $v);
			}
			return;
		}
		if(Arr::get($_GET, 'lang', false)) {
			if(!lang::checkLang(Arr::get($_GET, 'lang'))) {
				new Errors();
				die();
			}
			$langs = Arr::get($_GET, 'lang', 'ru');
			lang::set_lang($langs);
		}
		if(isset($_GET['saveLang'])) {
			if(lang::Update($langs, urldecode($_POST['orLang']), urldecode($_POST['translate']))) {
				$ret = "1";
			} else {
				$ret = "0";
			}
			HTTP::echos($ret);
			die();
		}
		if(isset($_GET['resetLang'])) {
			if(lang::LangReset($langs, urldecode($_POST['orLang']))) {
				$ret = "1";
			} else {
				$ret = "0";
			}
			HTTP::echos($ret);
			die();
		}
		templates::assign_var("initLang", $langs);
		$lang = lang::init_lang(true);
		if(!is_array($lang)) {
			die();
		}
		$i = 0;
		foreach($lang as $k => $v) {
			if(!is_string($v)) {
				continue;
			}
			templates::assign_vars(array("or" => $k, "lang" => str_replace(array("{"), array("&#123;"), $v)), "langList", "lang".$i);
			$i++;
		}
		$this->Prints("Lang");
	}
	
}

?>