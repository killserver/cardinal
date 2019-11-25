<?php

class SettingUser extends Core {

	public static $html = array();
	public static $head = array();
	private static $exclude = array();
	private static $title = "";

	public static function add($html, $name = ""/*, $altName = ""*/) {
		/*if(empty($altName)) {
			$altName = uniqid();
		}*/
		$file = $filename = "";
		$trace = debug_backtrace();
		if(isset($trace[0]) && isset($trace[0]['file'])) {
			$files = pathinfo($trace[0]['file']);
			$file = (isset($files['filename']) ? $files['filename'] : "");
			$filename = (isset($files['filename']) ? $files['filename'] : "");
		}
		if(empty($filename)) {
			$filename = uniqid();
		}
		self::$head[$filename] = '<li><a href="#'.$filename.'" data-toggle="tab"><span>'.(empty($name) ? "{L_'Настройки'}".(!empty($file) ? "&nbsp;{L_'".$file."'}" : "") : $name).'</span></a></li>';
		self::$html[$filename] = '<div class="tab-pane" id="'.$filename.'">'.$html.'</div>';
	}

	public static function exclude($name) {
		self::$exclude[$name] = $name;
	}

	public static function setTitle($title) {
		self::$title = $title;
	}

	function Saves($arr) {
		$arr = execEvent("save_settings", $arr);
		$ret = "";
		foreach($arr as $k => $v) {
			$ret .= "'".$k."' => ".(is_array($v) ? "array(".$this->Saves($v).")," : "'".str_replace("'", "\'", $v)."',");
		}
		return $ret;
	}

	function Save() {
		$sitename = Arr::get($_POST, 'sitename');
		$description = Arr::get($_POST, 'description');
		if(!empty($sitename)) {
			unset($_POST['sitename']);
		}
		if(!empty($description)) {
			unset($_POST['description']);
		}
		if(empty($sitename)) {
			$sitename = lang::get_lang('sitename');
		}
		if(empty($description)) {
			$description = lang::get_lang('s_description');
		}
		$config = array();
		if(file_exists(PATH_MEDIA."config.init.".ROOT_EX)) {
			include(PATH_MEDIA."config.init.".ROOT_EX);
		}
		$_POST = execEvent("pre_save_settings", $_POST);
		$_POST = array_merge($config, $_POST);
		$config = '<?php
		if(!defined("IS_CORE")) {
		echo "403 ERROR";
		die();
		}

		$config = array_merge($config, array(
		// start
		'.$this->Saves($_POST).'
		// start
		));

		?>';
		if(file_exists(PATH_MEDIA."config.init.".ROOT_EX)) {
			unlink(PATH_MEDIA."config.init.".ROOT_EX);
		}
		file_put_contents(PATH_MEDIA."config.init.".ROOT_EX, $config);
		$lang = '<?php
		if(!defined("IS_CORE")) {
		echo "403 ERROR";
		die();
		}

		$lang = array_merge($lang, array(
			"sitename" => "'.Saves::SaveOld($sitename, true).'",
			"s_description" => "'.Saves::SaveOld($description, true).'",
		));

		?>';
		$lang = charcode($lang);
		if(file_exists(PATH_MEDIA."config.lang.".ROOT_EX)) {
			unlink(PATH_MEDIA."config.lang.".ROOT_EX);
		}
		file_put_contents(PATH_MEDIA."config.lang.".ROOT_EX, $lang);
		cardinal::RegAction("Внесение изменений в настройки сайта V2");
		setcookie("SaveDone", "1", time()+100);
		location("./?pages=SettingUser");
	}

	function __construct() {
		if(sizeof($_POST)>0) {
			$this->Save();
			return;
		}
		execEventRef("SettingUser_page", self::$head, self::$html);
		$tmp = templates::load_templates("SettingUser");
		foreach(self::$exclude as $v) {
			if(isset(self::$head[$v])) {
				unset(self::$head[$v]);
			}
			if(isset(self::$html[$v])) {
				unset(self::$html[$v]);
			}
		}
		sortByKey(self::$head);
		sortByKey(self::$html);
		templates::accessNull();
		$tmp = str_replace("{head}", (sizeof(self::$head)>1 ? implode("", self::$head) : ""), $tmp);
		$tmp = str_replace("{data}", implode("", self::$html), $tmp);
		$tmp = templates::comp_datas($tmp);
		$name = lang::get_lang("sitename");
		$descr = lang::get_lang("s_description");
		$this->title((empty(self::$title) ? "{L_'Настройки'}" : self::$title));
		$tmp = str_replace("{sitename}", htmlspecialchars($name), $tmp);
		$tmp = str_replace("{s_description}", htmlspecialchars($descr), $tmp);
		$this->Prints($tmp, true);
	}

}
ReadPlugins(dirname(__FILE__)."/Plugins/", "SettingUser");

?>