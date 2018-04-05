<?php
if(!defined("IS_ADMIN")) {
echo "403 ERROR";
die();
}


class Settings extends Core {
	
	protected static $sub_nav = array();
	protected static $func = array();
	
	private function Save() {
		$error_type = Arr::get($_POST, 'error_type', config::Select('error_type'));
		$speed_update = Arr::get($_POST, 'speed_update', "0");
		$default_http_local = Arr::get($_POST, 'PATH', config::Select("default_http_local"));
		$default_http_hostname = Arr::get($_POST, 'SERVER', config::Select("default_http_hostname"));
		$default_http_host = Arr::get($_POST, 'PATH', config::Select("default_http_host"));
		$default_http_mobyhost = Arr::get($_POST, 'mobyhost', config::Select("default_http_mobyhost"));
		$cache_type = Arr::get($_POST, 'cache_type', "CACHE_FILE");
		$cache_host = Arr::get($_POST, 'cache_host', config::Select("cache", "host"));
		$cache_port = Arr::get($_POST, 'cache_port', config::Select("cache", "port"));
		$cache_user = Arr::get($_POST, 'cache_user', config::Select("cache", "user"));
		$cache_pass = Arr::get($_POST, 'cache_pass', config::Select("cache", "pass"));
		$cache_path = Arr::get($_POST, 'cache_path', config::Select("cache", "path"));
		$viewport = Arr::get($_POST, 'viewport', config::Select("viewport"));
		$ParsePHP = Arr::get($_POST, 'ParsePHP', "0");
		$sitename = Arr::get($_POST, 'sitename', lang::get_lang('sitename'));
		$description = Arr::get($_POST, 'description', lang::get_lang('description'));
		$replaceProtocols = array("https", "http", "://");
		$config = '<?php
		if(!defined("IS_CORE")) {
		echo "403 ERROR";
		die();
		}
		
		if(!defined("COOK_USER")) {
			define("COOK_USER", "'.COOK_USER.'");
		}
		if(!defined("COOK_PASS")) {
			define("COOK_PASS", "'.COOK_PASS.'");
		}
		if(!defined("COOK_ADMIN_USER")) {
			define("COOK_ADMIN_USER", "'.COOK_ADMIN_USER.'");
		}
		if(!defined("COOK_ADMIN_PASS")) {
			define("COOK_ADMIN_PASS", "'.COOK_ADMIN_PASS.'");
		}
		if(!defined("START_VERSION")) {
			define("START_VERSION", "'.(defined("START_VERSION") ? START_VERSION : "3.1").'");
		}

		$config = array_merge($config, array(
			"api_key" => "'.config::Select("api_key").'",
			"logs" => '.Saves::SaveOld($error_type, true).',
			"speed_update" => '.($speed_update=="1" ? "true" : "false").',
			"hosting" => true,
			"default_http_local" => "'.str_replace(array_merge($replaceProtocols, array($_SERVER['HTTP_HOST'])), "", $default_http_local).'",
			"default_http_hostname" => "'.Saves::SaveOld($default_http_hostname, true).'",
			"default_http_host" => HTTP::$protocol."://'.Saves::SaveOld(str_replace($replaceProtocols, "", $default_http_host), true).'",
			"default_http_mobyhost" => "'.Saves::SaveOld(str_replace($replaceProtocols, "", $default_http_mobyhost), true).'",
			"lang" => "ru",
			"cache" => array(
				"type" => '.Saves::SaveOld($cache_type, true).',
				"server" => "'.Saves::SaveOld($cache_host, true).'",
				"port" => '.Saves::SaveOld($cache_port, true).',
				"login" => "'.Saves::SaveOld($cache_user, true).'",
				"pass" => "'.Saves::SaveOld($cache_pass, true).'",
				"path" => "'.Saves::SaveOld($cache_path, true).'",
			),
			"viewport" => "'.Saves::SaveOld($viewport, true).'",
			"ParsePHP" => '.($ParsePHP=="1" ? "true" : "false").','.$this->Saves($_POST).'
			"lang" => "ru",
			"charset" => "utf-8",
		));

		?>';
		if(file_exists(PATH_MEDIA."config.install.php")) {
			unlink(PATH_MEDIA."config.install.php");
		}
		file_put_contents(PATH_MEDIA."config.install.php", $config);
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
		if(file_exists(PATH_MEDIA."config.lang.php")) {
			unlink(PATH_MEDIA."config.lang.php");
		}
		file_put_contents(PATH_MEDIA."config.lang.php", $lang);
		cardinal::RegAction("Внесение изменений в настройки сайта");
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
		$descr = lang::get_lang("s_description");
		config::SetDefault("");
		templates::accessNull();
		templates::assign_vars(array(
			"API" => config::Select("api_key"),
			"SERPATH" => config::Select("default_http_host"),
			"SERNAME" => config::Select("default_http_hostname"),
			"mail_from" => config::Select("mail_from"),
			"max_news" => config::Select("max_news"),
			"sitename" => htmlspecialchars($name),
			"description" => htmlspecialchars($descr),
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
ReadPlugins(dirname(__FILE__).DIRECTORY_SEPARATOR."Plugins".DIRECTORY_SEPARATOR, "Settings");

?>