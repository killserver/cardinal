<?php
/*
*
* Version Engine: 1.25.3
* Version File: 2
*
* 2.4
* add support XXX category
*
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

define("IS_CRON", true);

final class cardinal {

	private static $ch_login = array("class" => "cardinal", "method" => "or_create_pass");
	public function cardinal() {
		if(defined("INSTALLER")) {
			return;
		}
		if(isset($_SERVER['HTTP_USER_AGENT']) && !$this->robots(getenv("HTTP_USER_AGENT"))) {
			define("IS_BOT", false);
		} else {
			define("IS_BOT", true);
		}
		if(isset($_COOKIE['plus18'])) {
			define("IS_XXX", "true");
		}
		$otime = config::Select("cardinal_time");
		if(isset($_GET['d'])) {
			var_dump(CRON_TIME, $otime);die();
		}
		if($otime <= time()-12*60*60) {
			include_dir(ROOT_PATH."core/modules/cron/", ".".ROOT_EX);
			config::Update("cardinal_time", time());
		}
	}
	
	private function robots($useragent) {
		if(!isset($useragent) || empty($useragent) || is_bool($useragent)) {
			return false;
		}
		$arr = array();
		$bot_list = config::Select('robots');
		if(!is_bool($bot_list)) {
			$pcre = array_keys($bot_list);
			$dats = array_values($bot_list);
			for($i=0;$i<sizeof($pcre);$i++) {
				$arr["#.*".$pcre[$i].".*#si"] = $dats[$i];
			}
			$result = preg_replace(array_keys($arr), $arr, $useragent);
			return $result == $useragent ? false : $result;
		} else {
			return false;
		}
	}
	
	public static function set_eighteen() {
		if(!isset($_COOKIE['plus18'])) {
			setcookie("plus18", "1", (time()+(60*24*60*60)), "/", ".".config::Select("default_http_hostname"), false, true);
		} else {
			setcookie("plus18", "", (time()-(120*24*60*60)), "/", ".".config::Select("default_http_hostname"), false, true);
		}
	}
	
	private static function or_create_pass($pass) {
		$pass = md5(md5($pass).$pass);
		$pass = strrev($pass);
		$pass = sha1($pass);
		$pass = bin2hex($pass);
		return md5(md5($pass).$pass);
	}
	
	public static function change_pass($class = null, $method = null) {
		if(!empty($method) && !empty($class) && class_exists($class)) {
			self::$ch_login['class'] = $class;
			self::$ch_login['method'] = $method;
		} else if(!empty($class)) {
			self::$ch_login['class'] = $class;
			self::$ch_login['method'] = "login";
		}
	}
	
	public static function create_pass($pass) {
		$class = (self::$ch_login['class']);
		$method = (self::$ch_login['method']);
		return $class::$method($pass);
	}
	
	public static function get_eighteen() {
		if(isset($_COOKIE['plus18'])) {
			return true;
		} else {
			return false;
		}
	}
	
	public static function view_eighteen() {
		if(!cardinal::get_eighteen()) {
			templates::assign_vars(array(
				"title" => "{L_alert}",
				"error" => "{L_alert_up_eighteen}",
			));
			$view = templates::complited_assing_vars("info");
			templates::complited($view);
			templates::display();
		}
	}

	protected static function amper($data) {
		if(is_array($data)) {
			$returns = array();
			foreach($data as $name => $val) {
				if(!empty($val)) {
					$returns[] = $name."=".$val;
				} else {
					$returns[] = $name;
				}
			}
			return implode("&", $returns);
		} else {
			return $data;
		}
	}

	public static function hackers($page, $referer=null) {
		if(!empty($referer)) {
			$ref = ", referer = \"".urlencode($referer)."\"";
		} else {
			$ref = "";
		}
		db::doquery("INSERT INTO hackers SET ip = \"".HTTP::getip()."\", page = \"".urlencode($page)."\", post = \"".urlencode(self::amper($_POST))."\", get = \"".urlencode(self::amper($_GET))."\"".$ref.", activ = \"yes\"");
		location("{C_default_http_host}?hacker");
	}

	function __destruct() {
		unset($this);
	}

}

?>