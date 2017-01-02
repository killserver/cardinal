<?php
/*
 *
 * @version 1.25.7-a2
 * @copyright 2014-2015 KilleR for Cardinal Engine
 *
 * Version Engine: 1.25.7-a2
 * Version File: 2
 *
 * 2.4
 * add support XXX category
 * 2.5
 * delete old test constant
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

define("IS_CRON", true);

class cardinal {

	private static $ch_login = array("class" => "cardinal", "method" => "or_create_pass");
	public function cardinal() {
		if(defined("INSTALLER")) {
			return false;
		}
		if(!defined("IS_BOT")) {
            if (isset($_SERVER['HTTP_USER_AGENT']) && !$this->robots(getenv("HTTP_USER_AGENT"))) {
                define("IS_BOT", false);
            } else {
                define("IS_BOT", true);
            }
        }
		$otime = config::Select("cardinal_time");
		if($otime <= time()-12*60*60) {
			include_dir(ROOT_PATH."core".DS."modules".DS."cron".DS, ".".ROOT_EX);
			config::Update("cardinal_time", time());
		}
	}

	final public static function SaveCardinal($v) {
		$dv = $nv = "";
		for($i=0;$i<strlen($v);$i++) {
			$nv .= ord($v[$i]).";";
		}
		$v = $nv;
		unset($nv);
		if(function_exists("convert_uuencode")) {
			$dv .= "uu";
			$v = urlencode(convert_uuencode($v));
		}
		$v = urlencode($dv.$v);
		return $v;
	}
	
	final private function robots($userAgent) {
		if(!isset($userAgent) || empty($userAgent) || is_bool($userAgent)) {
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
			$result = preg_replace(array_keys($arr), $arr, $userAgent);
			return $result == $userAgent ? false : $result;
		} else {
			return false;
		}
	}
	
	final private static function or_create_pass($pass) {
		$pass = md5(md5($pass).$pass);
		$pass = strrev($pass);
		$pass = sha1($pass);
		$pass = bin2hex($pass);
	return md5(md5($pass).$pass);
	}
	
	final public static function change_pass($class = "", $method = "") {
		if(!empty($method) && !empty($class) && class_exists($class)) {
			self::$ch_login['class'] = $class;
			self::$ch_login['method'] = $method;
		} else if(!empty($class)) {
			self::$ch_login['class'] = $class;
			self::$ch_login['method'] = "login";
		}
	}
	
	final public static function create_pass($pass) {
		$class = (self::$ch_login['class']);
		$method = (self::$ch_login['method']);
		return $class::$method($pass);
	}
	
	final public static function StartSession() {
	global $session;
		if(!is_bool($session)) {
			session_start();
		}
	}

	final protected static function amper($data) {
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

	final public static function hackers($page, $referer = "") {
		if(defined("WITHOUT_DB")) {
			return false;
		}
		if(!empty($referer)) {
			$ref = ", referer = \"".urlencode($referer)."\"";
		} else {
			$ref = "";
		}
		db::doquery("INSERT INTO `hackers` SET `ip` = \"".HTTP::getip()."\", `page` = \"".urlencode($page)."\", `post` = \"".urlencode(self::amper($_POST))."\", `get` = \"".urlencode(self::amper($_GET))."\"".$ref.", `activ` = \"yes\"");
		location("{C_default_http_host}?hacker");
	}

	function __destruct() {
		unset($this);
	}

}

?>