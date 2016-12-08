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
		$base = pathinfo($path);
		if(!is_bool($base) && isset($base['dirname']) && file_exists($base['dirname']) && is_writable($base['dirname'])) {
			self::$pathSaveMime = $path;
		}
	}
	
	final public static function setContentType($type, $charset = "") {
		if(!self::$pathSaveMime) {
			return false;
		}
		try {
			if(strpos($type, "/")===false) {
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
	
	final public static function StatusHeader($code) {
		$code = abs(int($code));
		$StatusHeaderList = array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			102 => 'Processing',
 
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			207 => 'Multi-Status',
			226 => 'IM Used',
 
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			306 => 'Reserved',
			307 => 'Temporary Redirect',
			308 => 'Permanent Redirect',
 
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			418 => 'I\'m a teapot',
			421 => 'Misdirected Request',
			422 => 'Unprocessable Entity',
			423 => 'Locked',
			424 => 'Failed Dependency',
			426 => 'Upgrade Required',
			428 => 'Precondition Required',
			429 => 'Too Many Requests',
			431 => 'Request Header Fields Too Large',
			451 => 'Unavailable For Legal Reasons',
 
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported',
			506 => 'Variant Also Negotiates',
			507 => 'Insufficient Storage',
			510 => 'Not Extended',
			511 => 'Network Authentication Required',
		);
		if(isset($StatusHeaderList[$code])) {
			return $StatusHeaderList[$code];
		} else {
			return '';
		}
	}
	
	final public static function Location($link, $time = 0, $exit = true, $code = 302) {
		if(defined("PHP_SAPI") && PHP_SAPI != 'cgi-fcgi') {
			self::StatusHeader($status);
		}
		if($time == 0) {
			header("Location: ".self::ClearLocation($link), true, $code);
		} else {
			header("Refresh: ".$time."; url=".self::ClearLocation($link), true, $code);
		}
		if($exit) {
			exit();
		}
	}
	
	final public static function ClearLocation($location) {
		$regex = '/
			(
				(?: [\xC2-\xDF][\x80-\xBF]        # double-byte sequences   110xxxxx 10xxxxxx
				|   \xE0[\xA0-\xBF][\x80-\xBF]    # triple-byte sequences   1110xxxx 10xxxxxx * 2
				|   [\xE1-\xEC][\x80-\xBF]{2}
				|   \xED[\x80-\x9F][\x80-\xBF]
				|   [\xEE-\xEF][\x80-\xBF]{2}
				|   \xF0[\x90-\xBF][\x80-\xBF]{2} # four-byte sequences   11110xxx 10xxxxxx * 3
				|   [\xF1-\xF3][\x80-\xBF]{3}
				|   \xF4[\x80-\x8F][\x80-\xBF]{2}
			){1,40}                              # ...one or more times
			)/x';
		$location = preg_replace_callback($regex, 'HTTP::sanitize_utf8_in_redirect', $location);
		$location = preg_replace('|[^a-z0-9-~+_.?#=&;,/:%!*\[\]()@]|i', '', $location);
		$location = self::saveUrl($location);
		// remove %0d and %0a from location
		return str_replace(array('%0d', '%0a', '%0D', '%0A'), "", $location);
	}

	final public static function saveUrl($string, $deleteZero = true) {
		$string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', "", $string);
		if($deleteZero) {
			$string = preg_replace( '/\\\\+0+/', '', $string );
		}
		return $string;
	}

	final public static function sanitize_utf8_in_redirect($matches) {
		return urlencode($matches[0]);
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