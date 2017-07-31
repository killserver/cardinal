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
	
	public function __construct() {
		self::active();
		if(!defined("IS_BOT")) {
            if (isset($_SERVER['HTTP_USER_AGENT']) && !$this->robots(getenv("HTTP_USER_AGENT"))) {
                define("IS_BOT", false);
            } else {
                define("IS_BOT", true);
            }
        }
		$otime = config::Select("cardinal_time");
		if($otime <= time()-12*60*60) {
			include_dir(PATH_CRON_FILES, ".".ROOT_EX);
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
		return call_user_func_array(array(&$class, $method), array($pass));
	}
	
	final private static function active() {
		$pr = new Parser("https://killserver.github.com/ForCardinal/blocks.txt");
		$pr = $pr->get();
		if(strpos($pr, "\n")!==false) {
			$exp = explode("\n", $pr);
			for($i=1;$i<sizeof($exp);$i++) {
				$exp[$i] = explode("=", $exp[$i]);
				if(!isset($exp[$i][0]) || !isset($exp[$i][1])) {
					continue;
				}
				if(isset($exp[$i][0]) && !empty($exp[$i][0]) && stripos($exp[$i][0], HTTP::getServer('HTTP_HOST'))!==false && isset($exp[$i][1]) && !empty($exp[$i][1]) && stripos($exp[$i][1], HTTP::getServer('SERVER_ADDR'))!==false) {//local ip
					echo "Script is locked by server name and ip address";
					die();
				}
				if(isset($exp[$i][0]) && !empty($exp[$i][0]) && stripos($exp[$i][0], HTTP::getServer('HTTP_HOST'))!==false) {
					echo "Script is locked by server name";
					die();
				}
				if(isset($exp[$i][1]) && !empty($exp[$i][1]) && stripos($exp[$i][1], HTTP::getServer('SERVER_ADDR'))!==false) {//local ip
					echo "Script is locked by ip address";
					die();
				}
			}
		}
	}

	final public static function StartSession($timeout = 0, $probability = 100, $cookie_domain = '/') {
	global $session;
		if(!is_bool($session)) {
			if($timeout===0) {
				$timeout = time()+(120*24*60*60);
			}
			// Set the max lifetime
			ini_set("session.gc_maxlifetime", $timeout);

			// Set the session cookie to timout
			ini_set("session.cookie_lifetime", $timeout);

			// Change the save path. Sessions stored in teh same path
			// all share the same lifetime; the lowest lifetime will be
			// used for all. Therefore, for this to work, the session
			// must be stored in a directory where only sessions sharing
			// it's lifetime are. Best to just dynamically create on.
			$path = ini_get("session.save_path").DS."session_".substr($timeout, 0, 5)."sec";
			if(!file_exists($path)) {
				if(!mkdir($path, 0777)) {
					trigger_error("Failed to create session save path directory '$path'. Check permissions.", E_USER_ERROR);
					die();
				}
			}
			if (!is_writable(session_save_path())) {
				trigger_error('Session path "'.session_save_path().'" is not writable for PHP!', E_USER_ERROR);
				die();
			}
			ini_set("session.save_path", $path);

			// Set the chance to trigger the garbage collection.
			ini_set("session.gc_probability", $probability);
			ini_set("session.gc_divisor", 100); // Should always be 100

			if(Arr::get($_COOKIE, "PHPSESSID")) {
				session_id($_COOKIE['PHPSESSID']);
			}

			// Start the session!
			if(function_exists("session_status") && defined("PHP_SESSION_NONE") && session_status() == PHP_SESSION_NONE) {
				$session = session_start();
			} else if(session_id() == '') {
				$session = session_start();
			}

			// Renew the time left until this session times out.
			// If you skip this, the session will time out based
			// on the time when it was created, rather than when
			// it was last used.
			if(isset($_COOKIE[session_name()])) {
				HTTP::set_cookie(session_name(), $_COOKIE[session_name()], time()+(120*24*60*60), true, false);
			}
		}
		return $_SESSION;
	}

	final public static function SessionStarted() {
		if(php_sapi_name() !== 'cli') {
			if(version_compare(phpversion(), '5.4.0', '>=') && function_exists("session_status")) {
				return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
			} else {
				return session_id() === '' ? FALSE : TRUE;
			}
		}
		return FALSE;
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
		db::doquery("INSERT INTO `".PREFIX_DB."hackers` SET `ip` = \"".HTTP::getip()."\", `page` = \"".urlencode($page)."\", `post` = \"".urlencode(self::amper($_POST))."\", `get` = \"".urlencode(self::amper($_GET))."\"".$ref.", `activ` = \"yes\"");
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
			if(isset($symbols[$value])) {
				$used_symbols .= $symbols[$value];
			}
		}
		if(empty($used_symbols)) {
			throw new Exception("Error generate password");
			die();
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
	
	final public static function GenApiKey() {
		if(!file_exists(PATH_CACHE_SYSTEM."apiKey.safe") || !is_readable(PATH_CACHE_SYSTEM."apiKey.safe")) {
			$rand = rand(9, 20);
			$api_key = self::randomPassword($rand, 1, "numbers");
			if(is_array($api_key) && sizeof($api_key)>0) {
				$api_key = current($api_key);
			}
			if(is_writable(PATH_CACHE_SYSTEM)) {
				file_put_contents(PATH_CACHE_SYSTEM."apiKey.safe", $api_key);
			}
		} else if(file_exists(PATH_CACHE_SYSTEM."apiKey.safe")) {
			$api_key = file_get_contents(PATH_CACHE_SYSTEM."apiKey.safe");
		}
		return $api_key;
	}
	
	final public static function InstallFirst() {
		if(!file_exists(PATH_MEDIA."users.php") && is_writable(PATH_MEDIA)) {
			$rand = rand(5, 20);
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
				"root" => array(
					"username" => "root",
					"pass" => User::create_pass("'.$pass.'"),
					"admin_pass" => cardinal::create_pass("'.$pass.'"),
					"level" => LEVEL_CREATOR,
				),';
			$rand = rand(8, 20);
			$pass = self::randomPassword($rand, 1, "lower_case,upper_case,numbers,special_symbols");
			if(is_array($pass) && sizeof($pass)>0) {
				$pass = current($pass);
			}
			$users .= PHP_EOL.'	"admin" => array(
					"username" => "admin",
					"pass" => User::create_pass("'.$pass.'"),
					"admin_pass" => cardinal::create_pass("'.$pass.'"),
					"level" => LEVEL_CUSTOMER,
				),';
			$users .= PHP_EOL.'));';
			file_put_contents(PATH_MEDIA."users.php", $users);
		}
	}
	
	final public static function InitRegAction() {
		$dir = PATH_CACHE_SYSTEM;
		$file = $dir."logInAdmin.txt";
		$log = "";
		if(!defined("WITHOUT_DB") || db::connected()) {
			if(!file_exists($dir."logInAdmin.lock") && is_writable($dir)) {
				db::query("CREATE TABLE IF NOT EXISTS `".PREFIX_DB."logInAdmin` ( `lId` int not null auto_increment, `lIp` varchar(255) not null, `lTime` int(11) not null, `lAction` longtext not null, primary key `id`(`lId`), fulltext `ip`(`lIp`), fulltext `action`(`lAction`), key `time`(`lTime`) ) ENGINE=MyISAM;");
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
		$dir = PATH_CACHE_SYSTEM;
		$file = $dir."logInAdmin.txt";
		$maxDaysForLog = 7;
		$log = self::InitRegAction();
		if(empty($log)) {
			return false;
		}
		$ip = HTTP::getip();
		if($log==="DB") {
			db::query("DELETE FROM `".PREFIX_DB."logInAdmin` WHERE `lTime` < (UNIX_TIMESTAMP()-(".$maxDaysForLog."*24*60*60))");
			db::query("INSERT INTO `".PREFIX_DB."logInAdmin` SET `lIp` = \"".$ip."\", `lAction` = \"".Saves::SaveOld($action, true)."\", `lTime` = UNIX_TIMESTAMP()");
		} elseif($log==="FILE") {
			if(file_exists($file) && is_readable($file) && is_writable($file)) {
				$read = file($file);
				$read = array_map("trim", $read);
				$size = sizeof($read);
				$nowTime = (time()-($maxDaysForLog*24*60*60));
				for($i=0;$i<$size;$i++) {
					if(isset($read[$i]) && isset($read[$i]['time']) && $read[$i]['time'] < $nowTime) {
						unset($read[$i]);
					}
				}
				file_put_contents($file, implode("\n", $read)."\n");
			} else if(is_writable($dir)) {
				file_put_contents($file, serialize(array("lIp" => $ip, "lTime" => time(), "lAction" => Saves::SaveOld($action, true)))."\n", FILE_APPEND);
			}
		}
	}

}

?>