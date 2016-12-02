<?PHP
/*
 *
 * @version 5.4
 * @copyright 2014-2016 KilleR for Cardinal Engine
 *
 * Version Engine: 5.4
 * Version File: 1
 *
 * 0.4
 * add return header last modified in page for client
 * 0.5
 * add support one method cookie for engine
 * 1.0
 * add content type
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

class HTTP {
	
	private static $pathSaveMime = false;
	
	public function HTTP() {
		
	}
	
	final public static function getip() {
		if(isset($_SERVER)) {
			if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} elseif(isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif(isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR'])) {
				$ip = $_SERVER['REMOTE_ADDR'];
			} else {
				$ip = false;
			}
		} else {
			if(getenv('HTTP_X_FORWARDED_FOR')) {
				$ip = getenv('HTTP_X_FORWARDED_FOR');
			} elseif(getenv('HTTP_CLIENT_IP')) {
				$ip = getenv('HTTP_CLIENT_IP');
			} elseif(getenv('REMOTE_ADDR')) {
				$ip = getenv('REMOTE_ADDR');
			} else {
				$ip = false;
			}
		}
		if(strpos($ip, ",")!==false) {
			$ips = explode(",", $ip);
			$ip = current($ips);
			unset($ips);
		}
	return $ip;
	}
	
	final public static function CheckIp($ip) {
		if(is_array($ip)) {
			return in_array(self::getip(), $ip);
		} elseif(is_string($ip)) {
			return self::getip()==$ip;
		} else {
			return false;
		}
	}
	
	final public static function set_cookie($name, $value, $delete = false, $save = true) {
		$domain = config::Select('default_http_hostname');
		if(is_bool($delete)) {
			if(!$delete) {
				$time = time()+(120*24*60*60);
			} else {
				$time = time()-(120*24*60*60);
			}
		} else {
			$time = $delete;
		}
		$ret = false;
		if($save) {
			if((version_compare(PHP_VERSION_ID, '70000', '>=')) || strpos("localhost", $domain)!==false || strpos("127.0.0.1", $domain)!==false) {
				$ret = setcookie($name, $value, $time, "/");
			} else {
				$ret = setcookie($name, $value, $time, "/", ".".$domain, false, true);
			}
		} else {
			$ret = setcookie($name, $value, $time);
		}
		return $ret;
	}
	
	final public static function lastmod($LastModified_unix = 0) {
		$LastModified_unix = intval($LastModified_unix);
		$LastModified = gmdate("D, d M Y H:i:s \G\M\T", $LastModified_unix);
		$IfModifiedSince = false;
		if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
			$IfModifiedSince = strtotime(substr($_SERVER['HTTP_IF_MODIFIED_SINCE'], 5));
		}
		if(!is_bool($IfModifiedSince) && $IfModifiedSince >= $LastModified_unix) {
			header($_SERVER['SERVER_PROTOCOL'].' 304 Not Modified');
			return false;
		}
		header('Last-Modified: '.$LastModified);
		header('Expires: '.$LastModified);
		return true;
	}
	
	final public static function setSaveMime($path) {
		if(file_exists($path) && is_writable($path)) {
			self::$pathSaveMime = $path;
		}
	}
	
	final public static function setContentType($type, $charset = "") {
		if(!self::$pathSaveMime) {
			return false;
		}
		try {
			if(strpos($type, "/")!==false) {
				if(file_exists(self::$pathSaveMime)) {
					$file = file_get_contents(self::$pathSaveMime);
				} else {
					$file = file_get_contents("https://raw.githubusercontent.com/skyzyx/mimetypes/master/mimetypes.json");
					file_put_contents(self::$pathSaveMime, $file);
				}
				$json = json_decode($file, true);
				if(!is_array($json) || !isset($json[$type])) {
					return false;
				} else {
					header("Content-Type: ".$json[$type].(!empty($charset) ? "; charset=".$charset : ""), true);
					return true;
				}
			} else {
				header("Content-Type: ".$type.(!empty($charset) ? "; charset=".$charset : ""), true);
				return true;
			}
			return true;
		} catch(Exception $ex) {
			return false;
		}
	}
	
	final public static function echos($echo = "", $die = false) {
		if(!empty($echo)) {
			echo $echo;
			unset($echo);
		}
		if(!defined("ALL_GOOD")) {
			define("ALL_GOOD", true);
		}
		if($die) {
			die();
		}
	}
	
}

?>