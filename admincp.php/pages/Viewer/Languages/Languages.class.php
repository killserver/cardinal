<?php

class Languages extends Core {
	
	function __construct() {
		$langs = "ru";
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