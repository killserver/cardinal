<?PHP
/*
 *
 * @version 3.0
 * @copyright 2014-2016 KilleR for Cardinal Engine
 *
 * Version Engine: 3.0
 * Version File: 0
 *
 * 0.4
 * add return header last modified in page for client
 * 0.5
 * add support one method cookie for engine
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

final class HTTP {
	
	public function HTTP() {
		
	}
	
	final public static function getip() {
		if(getenv('HTTP_X_FORWARDED_FOR')) {
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		} elseif(getenv('HTTP_CLIENT_IP')) {
			$ip = getenv('HTTP_CLIENT_IP');
		} else {
			$ip = getenv('REMOTE_ADDR');
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
		} else if(is_string($ip)) {
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
		if($save) {
			if((version_compare(PHP_VERSION_ID, '70000', '>=')) || strpos("localhost", $domain)!==false) {
				setcookie($name, $value, $time, "/");
			} else {
				setcookie($name, $value, $time, "/", ".".config::Select('default_http_hostname'), false, true);
			}
		} else {
			setcookie($name, $value, $time);
		}
	}
	
	final public static function lastmod($LastModified_unix) {
		$LastModified = gmdate("D, d M Y H:i:s \G\M\T", $LastModified_unix);
		$IfModifiedSince = false;
		if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']))
			$IfModifiedSince = strtotime(substr($_SERVER['HTTP_IF_MODIFIED_SINCE'], 5));
		if(!is_bool($IfModifiedSince) && $IfModifiedSince >= $LastModified_unix) {
			header($_SERVER['SERVER_PROTOCOL'].' 304 Not Modified');
			return;
		}
		header('Last-Modified: '.$LastModified);
		header('Expires: '.$LastModified);
	}
	
	final public static function echos($echo = null) {
		if(!empty($echo)) {
			echo $echo;
			unset($echo);
		}
		if(!defined("ALL_GOOD")) {
			define("ALL_GOOD", true);
		}
	}
	
}

?>