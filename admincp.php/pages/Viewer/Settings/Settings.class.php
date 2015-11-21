<?php
if(!defined("IS_ADMIN")) {
echo "403 ERROR";
die();
}


class Settings extends Core {
	
	protected static $sub_nav = array();
	protected static $func = array();
	
	private function Save() {
		if(file_exists(ROOT_PATH."core/media/config.install.php")) {
			unlink(ROOT_PATH."core/media/config.install.php");
		}
		$config = '<?php
		if(!defined("IS_CORE")) {
		echo "403 ERROR";
		die();
		}
		
		define("COOK_USER", "'.COOK_USER.'");
		define("COOK_PASS", "'.COOK_PASS.'");
		define("COOK_ADMIN_USER", "'.COOK_ADMIN_USER.'");
		define("COOK_ADMIN_PASS", "'.COOK_ADMIN_PASS.'");

		if(isset($_SERVER[\'HTTPS\']) && $_SERVER[\'HTTPS\']!=\'off\') {
			$protocol = "https";
		} else if(isset($_SERVER[\'HTTP_X_FORWARDED_PROTO\']) && $_SERVER[\'HTTP_X_FORWARDED_PROTO\']==\'https\') {
			$protocol = "https";
		} else {
			$protocol = "http";
		}

		$config = array_merge($config, array(
			"api_key" => "'.config::Select("api_key").'",
			"logs" => '.saves($_POST['error_type'], true).',
			"hosting" => true,
			"default_http_hostname" => "'.saves($_POST['SERVER'], true).'",
			"default_http_host" => $protocol."://'.saves(str_replace(array("http", "https", "://"), "", $_POST['PATH']), true).'",
			"lang" => "ru",
			"cache" => array(
				"type" => '.saves($_POST['cache_type'], true).',
				"server" => "'.saves($_POST['cache_host'], true).'",
				"port" => '.saves($_POST['cache_port'], true).',
				"login" => "'.saves($_POST['cache_user'], true).'",
				"pass" => "'.saves($_POST['cache_pass'], true).'",
				"path" => "'.saves($_POST['cache_path'], true).'",
			),'.$this->Saves($_POST).'
			"lang" => "ru",
			"charset" => "utf-8",
		));

		?>';
		file_put_contents(ROOT_PATH."core/media/config.install.php", $config);
		if(file_exists(ROOT_PATH."core/media/config.lang.php")) {
			unlink(ROOT_PATH."core/media/config.lang.php");
		}
		$lang = '<?php
		if(!defined("IS_CORE")) {
		echo "403 ERROR";
		die();
		}

		$lang = array_merge($lang, array(
			"sitename" => "'.saves($_POST['sitename'], true).'",
			"s_description" => "'.saves($_POST['description'], true).'",
			"s_keywords" => "'.saves($_POST['keywords'], true).'",
		));

		?>';
		$lang = charcode($lang);
		file_put_contents(ROOT_PATH."core/media/config.lang.php", $lang);
		setcookie("SaveDone", "1", time()+10);
		location("./?pages=Settings");
	}
	
	private function Saves($post) {
		$return = "";
		foreach(self::$func as $name => $func) {
			$return .= call_user_func_array($func, array($post));
		}
		return $return;
	}
	
	public static function AddFunc($func) {
		self::$func[$func['name']] = $func['func'];
	}
	
	public static function AddNav($data) {
		self::$sub_nav = array_merge(self::$sub_nav, $data);
	}
	
	function __construct() {
		if(sizeof($_POST)>0) {
			$this->Save();
			return;
		}
		$name = lang::get_lang("sitename");
		$key = lang::get_lang("s_keywords");
		$descr = lang::get_lang("s_description");
		templates::assign_vars(array(
			"API" => config::Select("api_key"),
			"SERPATH" => config::Select("default_http_host"),
			"SERNAME" => config::Select("default_http_hostname"),
			"mail_from" => config::Select("mail_from"),
			"max_news" => config::Select("max_news"),
			"cache_host" => config::Select("cache", "server"),
			"cache_port" => config::Select("cache", "port"),
			"cache_user" => config::Select("cache", "login"),
			"cache_pass" => config::Select("cache", "pass"),
			"cache_path" => config::Select("cache", "path"),
			"sitename" => $name,
			"keywords" => $key,
			"description" => $descr,
		));
		if(sizeof(self::$sub_nav)>0) {
			for($i=0;$i<sizeof(self::$sub_nav);$i++) {
				templates::assign_vars(array("subname" => self::$sub_nav[$i]['subname'], "name" => self::$sub_nav[$i]['name']), "sub_nav", "sub_nav".$i);
				templates::assign_vars(array("subname" => self::$sub_nav[$i]['subname'], "options" => self::$sub_nav[$i]['options']), "sub_nav_options", "sub_nav".$i);
			}
		}
		$this->Prints("Settings");
	}
	
}
ReadPlugins(dirname(__FILE__)."/Plugins/", "Settings");

?>