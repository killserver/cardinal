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

	private static $backToView = array();
	private static $ch_login = array("class" => "cardinal", "method" => "or_create_pass");
	
	public function cardinal() {
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
	
	final public static function callbacks($module, $callback = "", $type = "add") {
		if(!is_callable($callback) && $type == "add") {
			throw new Exception("Callback return error in called method");
			die();
		}
		if($type=="add") {
			if(!isset(self::$backToView[$module])) {
				self::$backToView[$module] = array();
			}
			$size = sizeof(self::$backToView[$module]);
			self::$backToView[$module][$size] = $callback;
			return true;
		} else if($type=="remove") {
			if(!isset(self::$backToView[$module])) {
				return false;
			} else {
				unset(self::$backToView[$module]);
				return true;
			}
		} else if($type=="call") {
			if(!isset(self::$backToView[$module]) || !is_Array(self::$backToView[$module])) {
				return $callback;
			}
			$arr = array_keys(self::$backToView[$module]);
			for($i=0;$i<sizeof($arr);$i++) {
				$callback = call_user_func_array(self::$backToView[$module][$i], array($callback));
			}
			return $callback;
		}
	}
	
	final public static function randomPassword($length, $count, $characters) {  
		$symbols = array();
		$passwords = array();
		$used_symbols = '';
		$pass = '';
		$symbols["lower_case"] = 'abcdefghijklmnopqrstuvwxyz';
		$symbols["upper_case"] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$symbols["numbers"] = '1234567890';
		$symbols["special_symbols"] = '!?~@#-_+<>[]{}';
		$characters = explode(",", $characters);
		foreach($characters as $key => $value) {
			$used_symbols .= $symbols[$value];
		}
		$symbols_length = strlen($used_symbols) - 1;
		for($p=0;$p<$count;$p++) {
			$pass = '';
			for($i=0;$i<$length;$i++) {
				$n = rand(0, $symbols_length);
				$pass .= $used_symbols[$n];
			}
			$passwords[] = $pass;
		}
		return $passwords;
	}
	
	final public static function InstallFirst() {
		if(!file_exists(ROOT_PATH."core".DS."media".DS."users.php") && is_writable(ROOT_PATH."core".DS."media".DS)) {
			$rand = rand(6, 15);
			$pass = self::randomPassword($rand, 1, "lower_case,upper_case,numbers,special_symbols");
			if(is_array($pass) && sizeof($pass)>0) {
				$pass = current($pass);
			}
			$users = '<?php
			if(!defined("IS_CORE")) {
				echo "403 ERROR";
				die();
			}

			$users = array_merge($users, array(
				"admin" => array(
					"username" => "admin",
					"pass" => create_pass("'.$pass.'"),
					"admin_pass" => cardinal::create_pass("'.$pass.'"),
					"level" => LEVEL_ADMIN,
				),
			));';
			file_put_contents(ROOT_PATH."core".DS."media".DS."users.php", $users);
		}
	}
	
	final public static function InitRegAction() {
		$dir = ROOT_PATH."core".DS."cache".DS."system".DS;
		$file = $dir."logInAdmin.txt";
		$log = "";
		if(!defined("WITHOUT_DB") || db::connected()) {
			if(!file_exists($dir."logInAdmin.lock") && is_writable($dir)) {
				db::query("CREATE TABLE IF NOT EXISTS `logInAdmin` ( `lId` int not null auto_increment, `lIp` varchar(255) not null, `lTime` int(11) not null, `lAction` longtext not null, primary key `id`(`lId`), fulltext `ip`(`lIp`), fulltext `action`(`lAction`), key `time`(`lTime`) ) ENGINE=MyISAM;");
				file_put_contents($dir."logInAdmin.lock", "");
				$log = "DB";
			} else if(file_exists($dir."logInAdmin.lock")) {
				$log = "DB";
			}
		} elseif(is_writable($dir)) {
			$log = "FILE";
		}
		return $log;
	}
	
	final public static function RegAction($action) {
		$dir = ROOT_PATH."core".DS."cache".DS."system".DS;
		$file = $dir."logInAdmin.txt";
		$maxDaysForLog = 7;
		$log = self::InitRegAction();
		if(empty($log)) {
			return false;
		}
		$ip = HTTP::getip();
		if($log==="DB") {
			db::doquery("DELETE FROM `logInAdmin` WHERE `lTime` < (UNIX_TIMESTAMP()-(".$maxDaysForLog."*24*60*60))");
			db::doquery("INSERT INTO `logInAdmin` SET `lIp` = \"".$ip."\", `lAction` = \"".Saves::SaveOld($action, true)."\", `lTime` = UNIX_TIMESTAMP()");
		} elseif($log==="FILE") {
			if(file_exists($file) && is_readable($file) && is_writable($dir)) {
				$read = file($file);
				$read = array_map("trim", $read);
				$size = sizeof($read);
				$nowTime = (time()-($maxDaysForLog*24*60*60));
				for($i=0;$i<$size;$i++) {
					if(isset($read[$i]) && isset($read[$i]['time']) && $read[$i]['time'] < $nowTime) {
						unset($read[$i]);
					}
				}
				file_put_contents($file, implode("\n", $read));
			}
			file_put_contents($file, serialize(array("lIp" => $ip, "lTime" => time(), "lAction" => Saves::SaveOld($action, true)))."\n", FILE_APPEND);
		}
	}

	function __destruct() {
		unset($this);
	}

}

?>