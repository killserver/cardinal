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

if(!defined("IS_CRON")) {
	define("IS_CRON", true);
}

class cardinal {

	private static $backToView = array();
	private static $ch_login = array("class" => "cardinal", "method" => "or_create_pass");
	
	public function __construct() {
		self::active();
		self::cron();
		if(class_exists("cardinalAdded")) {
			new cardinalAdded();
			self::robots();
		} else {
			die();
		}
	}

	public static function is_cli() {
		if(defined('STDIN')) {
			return true;
		}
		if(empty($_SERVER['REMOTE_ADDR']) && !isset($_SERVER['HTTP_USER_AGENT']) && sizeof($_SERVER['argv'])>0) {
			return true;
		}
		if(!array_key_exists('REQUEST_METHOD', $_SERVER)) {
			return true;
		}
		return false;
	}

	final private static function robots() {
		if(!defined("DEVELOPER_MODE") && defined("PATH_CACHE_SYSTEM") && file_exists(PATH_CACHE_SYSTEM."seoBlockDev.lock")) {
			unlink(PATH_CACHE_SYSTEM."seoBlockDev.lock");
		} elseif(!defined("DEVELOPER_MODE") && file_exists(ROOT_PATH."core".DS."cache".DS."system".DS."seoBlockDev.lock")) {
			unlink(ROOT_PATH."core".DS."cache".DS."system".DS."seoBlockDev.lock");
		}
		$host = (class_exists("HTTP") && method_exists("HTTP", "getServer") ? HTTP::getServer('SERVER_NAME') : $_SERVER['SERVER_NAME']);
		$path = (class_exists("HTTP") && method_exists("HTTP", "getServer") ? HTTP::getServer('PHP_SELF') : $_SERVER['PHP_SELF']);
		if(strpos($path, "index.".ROOT_EX."/")!==false) {
			$path = explode("index.".ROOT_EX."/", $path);
			$path = current($path);
		}
		$path = str_replace(array("uploads".DS."robots.txt", "uploads".(defined("DS_DB") ? DS_DB : "/")."robots.txt", "index.".ROOT_EX), "", $path);
		if(substr($path, 0, 1)=="/") {
			$path = substr($path, 1);
		}
		if(!defined("DEVELOPER_MODE") && !file_exists(PATH_UPLOADS."robots.txt") && is_writable(PATH_UPLOADS)) {
			$robots = "User-agent: *\n".
					"Disallow: /".$path.(!defined("ADMINCP_DIRECTORY") ? "admincp.php" : ADMINCP_DIRECTORY)."/\n".
					"Disallow: /".$path."cdn-cgi/\n".
					"Disallow: /".$path."cgi-bin/\n".
					"Disallow: /".$path."core/\n".
					"Disallow: /".$path."changelog/\n".
					"Disallow: /".$path."examples/\n".
					"Disallow: /".$path."js/\n".
					"Disallow: /".$path."skins/\n".
					"Disallow: /".$path."version/\n".
					"Disallow: /".$path."uploads/\n".
					"Disallow: /".$path."*utm_medium=rooms*\n".
					"Disallow: /".$path."*tab=*\n".
					"Disallow: /".$path."*gclid=*\n".
					"Disallow: /".$path."*yclid=*\n".
					"Disallow: /".$path."*utm_trafmaguid=*\n".
					"Disallow: /".$path."*kits_costs*\n".
					"\n".
					"User-agent: Yahoo\n".
					"Disallow: /".$path."\n".
					"\n".
					"User-agent: MJ12bot\n".
					"Disallow: /".$path."\n".
					"\n".
					"User-agent: Mediapartners-Google\n".
					"Disallow: /".$path."\n".
					"\n".
					"User-agent: AhrefsBot\n".
					"Disallow: /".$path."\n".
					"\n".
					"\n".
					"Host: ".$host."\n".
					"Sitemap: http://".$host."/".$path."sitemap.xml";
			file_put_contents(PATH_UPLOADS."robots.txt", $robots);
		} elseif(!file_exists(PATH_UPLOADS."robots.txt") && is_writable(PATH_UPLOADS)) {
			$robots = "User-agent: *\n".
					"Disallow: /".$path;
			file_put_contents(PATH_UPLOADS."robots.txt", $robots);
			if(!defined("DEVELOPER_MODE") && defined("PATH_CACHE_SYSTEM") && file_exists(PATH_CACHE_SYSTEM."seoBlockDev.lock")) {
				file_put_contents(PATH_CACHE_SYSTEM."seoBlockDev.lock", "");
			} elseif(!defined("DEVELOPER_MODE") && file_exists(ROOT_PATH."core".DS."cache".DS."system".DS."seoBlockDev.lock")) {
				file_put_contents(ROOT_PATH."core".DS."cache".DS."system".DS."seoBlockDev.lock", "");
			}
		}
	}

	final public static function cron() {
		$hours = execEvent("hours_cron_time", 12);
		if($hours<=0) {
			$hours = 1;
		}
		$otime = config::Select("cardinal_time");
		if(!$otime || $otime >= time()-$hours*60*60) {
			include_dir(PATH_CRON_FILES, ".".ROOT_EX, true);
			config::Update("cardinal_time", time());
		}
	}
	
	final public static function int_pad($str, $pad_len, $pad_str = 0, $dir = STR_PAD_RIGHT) {
		$str = str_pad($str, $pad_len, $pad_str, $dir);
		return intval($str);
	}
	
	final public static function CheckVersion($check = "", $old = "") {
		if(class_exists("config", false) && method_exists("config", "Select") && config::Select("speed_update")) {
			$isChecked = ($old!=="" ? $old : (defined("INTVERSION") ? INTVERSION : (defined("VERSION") ? VERSION : "1.0")));
		} else {
			$isChecked = ($old!=="" ? $old : (defined("VERSION") ? VERSION : "1.0"));
		}
		if($check==="") {
			return $isChecked;
		}
		if(stripos($check, "-")!==false) {
			$check = explode("-", $check);
			$check = current($check);
		}
		if($check<"2.0") {
			return false;
		}
		$checked = floatval($check);
		$version = floatval($isChecked);
		return ($checked > $version);
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
					errorHeader();
					echo "Script is locked by server name and ip address";
					die();
				}
				if(isset($exp[$i][0]) && !empty($exp[$i][0]) && stripos($exp[$i][0], HTTP::getServer('HTTP_HOST'))!==false) {
					errorHeader();
					echo "Script is locked by server name";
					die();
				}
				if(isset($exp[$i][1]) && !empty($exp[$i][1]) && stripos($exp[$i][1], HTTP::getServer('SERVER_ADDR'))!==false) {//local ip
					errorHeader();
					echo "Script is locked by ip address";
					die();
				}
			}
		}
	}

	final public static function StartSession($timeout = 0, $probability = 100) {
	global $session, $sessionOnline;
		if(defined("IS_CLI")) {
			return;
		}
		if(is_bool($session) && $session===false) {
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
			$path = (defined("PATH_CACHE_SESSION") ? PATH_CACHE_SESSION : false);
			$copy = $save = false;
			if(!is_bool($path)) {
				if(!file_exists($path)) {
					if(@mkdir($path, 0777)) {
						$save = true;
					} 
				} else {
					$save = true;
				}
				if(!is_writable($path)) {
					@chmod($path, 0777);
				}
				if($save) {
					$realpath = realpath($path);
					@ini_set("session.save_path", $realpath);
					@session_save_path($realpath);
					$newGet = session_save_path();
					if($realpath!=$newGet) {
						$copy = true;
					}
					$sessionOnline = true;
				}
			}

			// Set the chance to trigger the garbage collection.
			ini_set("session.gc_probability", $probability);
			ini_set("session.gc_divisor", 100); // Should always be 100

			if(Arr::get($_COOKIE, "PHPSESSID")) {
				session_id($_COOKIE['PHPSESSID']);
			}

			// Start the session!
			if(function_exists("session_status") && defined("PHP_SESSION_NONE") && session_status() == PHP_SESSION_NONE) {
				$session = session_start();
			} else {
				$session = session_start();
			}

			// Renew the time left until this session times out.
			// If you skip this, the session will time out based
			// on the time when it was created, rather than when
			// it was last used.
			$name = session_name();
			if(isset($_COOKIE[$name])) {
				HTTP::set_cookie($name, $_COOKIE[$name], time()+(120*24*60*60), true, false);
				if(!is_bool($path) && $copy) {
					$dir = ini_get("session.save_path").DS;
					$file = "sess_".$_COOKIE[$name];
					if(file_exists($dir.$file)) {
						@copy($dir.$file, $path.$file);
					}
				}
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
	
	final public static function callbacks($module, $callback = "", $type = "add") {
		throw new Exception("Warning! This function well be removed in new version");
	}
	
	final public static function randomPassword($length, $count, $characters) {  
		$symbols = array();
		$passwords = array();
		$used_symbols = '';
		$symbols["lower_case"] = 'abcdefghijklmnopqrstuvwxyz';
		$symbols["upper_case"] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$symbols["numbers"] = '1234567890';
		$symbols["special_symbols"] = '!?~@#-_+>[]{}';
		$characters = explode(",", $characters);
		foreach($characters as $key => $value) {
			if(isset($symbols[$value])) {
				$used_symbols .= $symbols[$value];
			}
		}
		if(empty($used_symbols)) {
			errorHeader();
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
	    $api_key = false;
		if(!file_exists(PATH_CACHE_USERDATA."apiKey.safe") || !is_readable(PATH_CACHE_USERDATA."apiKey.safe")) {
			$rand = rand(9, 20);
			$api_key = self::randomPassword($rand, 1, "numbers");
			if(is_array($api_key) && sizeof($api_key)>0) {
				$api_key = current($api_key);
			}
			if(is_writable(PATH_CACHE_USERDATA)) {
				file_put_contents(PATH_CACHE_USERDATA."apiKey.safe", $api_key);
			}
		} else if(file_exists(PATH_CACHE_USERDATA."apiKey.safe")) {
			$api_key = file_get_contents(PATH_CACHE_USERDATA."apiKey.safe");
		}
		return $api_key;
	}
	
	final public static function InstallFirst() {
		if(!file_exists(PATH_MEDIA."users.php") && is_writable(PATH_MEDIA)) {
			$rand = rand(15, 40);
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
					"light" => "'.$pass.'",
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
					"light" => "'.$pass.'",
					"level" => LEVEL_CUSTOMER,
				),';
			$users .= PHP_EOL.'));';
			file_put_contents(PATH_MEDIA."users.php", $users);
		}
	}
	
	final public static function InitRegAction() {
		$dir = PATH_CACHE_USERDATA;
		$file = $dir."logInAdmin.txt";
		if(!file_exists($file)) {
			touch($file);
		}
		$log = "FILE";
		return $log;
	}
	
	final public static function RegAction($action) {
		$dir = PATH_CACHE_USERDATA;
		$file = $dir."logInAdmin.txt";
		$maxDaysForLog = 7;
		$log = self::InitRegAction();
		if(empty($log)) {
			return false;
		}
		$ip = HTTP::getip();
		if(file_exists($file) && is_readable($file) && is_writable($dir)) {
			$read = file($file);
			$read = array_filter($read);
			$read = array_map("trim", $read);
			$size = sizeof($read);
			$nowTime = (time()-($maxDaysForLog*24*60*60));
			for($i=0;$i<$size;$i++) {
				if(isset($read[$i]) && isset($read[$i]['time']) && $read[$i]['time'] < $nowTime) {
					unset($read[$i]);
				}
			}
			$read = array_filter($read);
			file_put_contents($file, implode("\n", $read)."\n");
		}
		if(is_writable($dir)) {
			file_put_contents($file, serialize(array("lIp" => $ip, "lTime" => time(), "lAction" => Saves::SaveOld($action, true)))."\n", FILE_APPEND);
		}
	}

}

$GLOBALS['_126026315_']=Array(base64_decode('ZGVma' .'W5lZA=='),base64_decode('c' .'3Vi' .'c3Ry'),base64_decode('c3R' .'ycmNocg=='),base64_decode('Z' .'Glyb' .'mFtZQ=='),base64_decode('Zmls' .'ZV' .'9leGlzdHM='),base64_decode('Y2xhc3Nf' .'ZXhpc3' .'Rz'),base64_decode('bWV0aG9kX2V4a' .'X' .'N0cw=='),base64_decode('c' .'mVn' .'aX' .'N0' .'ZXJf' .'c2h1dG' .'Rv' .'d25f' .'ZnVuY' .'3Rpb24=')); function _1901771941($i){$a=Array('UEhQX0VY','Lg==','cGhw','Y2FyZGluYWxBZGRlZC4=','Y2FyZGluYWxBZGRlZA==','Y2FyZGluYWxBZGRlZA==','cmVnU3RhcnQ=','Y2FyZGluYWxBZGRlZA==','cmVnU3RhcnQ=');return base64_decode($a[$i]);} if(!$GLOBALS['_126026315_'][0](_1901771941(0))){$_0=$GLOBALS['_126026315_'][1]($GLOBALS['_126026315_'][2](__FILE__,_1901771941(1)),round(0+0.5+0.5));if(empty($_0)){$_0=_1901771941(2);}}else{$_0=PHP_EX;}$_1=$GLOBALS['_126026315_'][3](__FILE__) .DIRECTORY_SEPARATOR ._1901771941(3) .$_0;if($GLOBALS['_126026315_'][4]($_1)){include_once($_1);if(!$GLOBALS['_126026315_'][5](_1901771941(4))){die();}if(!$GLOBALS['_126026315_'][6](_1901771941(5),_1901771941(6))){die();}$GLOBALS['_126026315_'][7](array(_1901771941(7),_1901771941(8)));}else{die();}

?>