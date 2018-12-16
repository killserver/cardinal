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
	public static $protocol = "http";
	
	public function __construct() {
		
	}
	
	final public static function getServer($name, $andEmpty = false, $return = false) {
		if(isset($_SERVER)) {
			if($andEmpty===true && isset($_SERVER[$name]) && !empty($_SERVER[$name])) {
				return $_SERVER[$name];
			} elseif($andEmpty===false && isset($_SERVER[$name])) {
				return $_SERVER[$name];
			} else {
				return $return;
			}
		} else {
			$name = getenv($name);
			if($andEmpty===true && $name!==false && !empty($name)) {
				return getenv($name);
			} elseif($andEmpty===false && $name!==false) {
				return getenv($name);
			} else {
				return $return;
			}
		}
	}
	
	final private static function execHTTPLang($res, $el) {
		$el = explode(';q=', $el);
		list($l, $q) = array_merge($el, array(1)); 
		$res[$l] = floatval($q);
		return $res;
	}
	
	final public static function getHTTPLangs() {
		if(self::getServer('HTTP_ACCEPT_LANGUAGE')) {
			$prefLocales = explode(',', self::getServer('HTTP_ACCEPT_LANGUAGE'));
			$prefLocales = array_reduce($prefLocales, "HTTP::execHTTPLang", array());
			arsort($prefLocales);
			$prefLocales = array_keys($prefLocales);
		} else {
			$prefLocales = array("");
		}
		return $prefLocales;
	}
	
	final public static function getip() {
		if(self::getServer('HTTP_CF_CONNECTING_IP', true)) {
			$ip = self::getServer('HTTP_CF_CONNECTING_IP', true);
		} elseif(self::getServer('HTTP_X_FORWARDED_FOR', true)) {
			$ip = self::getServer('HTTP_X_FORWARDED_FOR', true);
		} elseif(self::getServer('HTTP_CLIENT_IP', true)) {
			$ip = self::getServer('HTTP_CLIENT_IP', true);
		} elseif(self::getServer('REMOTE_ADDR', true)) {
			$ip = self::getServer('REMOTE_ADDR', true);
		} else {
			$ip = false;
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
	
	final public static function set_cookie($name, $value, $delete = false, $save = true, $allSubDomain = true) {
		if(class_exists("config") && method_exists("config", "Select")) {
			$domain = config::Select('default_http_hostname');
		} else {
			$domain = self::getServer("HTTP_HOST");
		}
		if(is_bool($delete)) {
			if(!$delete) {
				$time = time()+(120*24*60*60);
				$_COOKIE[$name] = $value;
			} else {
				$time = time()-(120*24*60*60);
				if(isset($_COOKIE[$name])) {
					unset($_COOKIE[$name]);
				}
			}
		} else {
			$time = $delete;
			if($time>0) {
				$_COOKIE[$name] = $value;
			} elseif(isset($_COOKIE[$name])) {
				unset($_COOKIE[$name]);
			}
		}
		$ret = false;
		if($save) {
			if((version_compare(PHP_VERSION_ID, '70000', '>=')) || strpos($domain, "localhost")!==false || strpos($domain, "127.0.0.1")!==false || strpos(self::getServer("SERVER_ADDR"), "127.0.0.1")!==false) {
				$ret = setcookie($name, $value, $time, "/");
			} else {
				if(version_compare(PHP_VERSION, '5.2', '<')) {
					$ret = setcookie($name, $value, $time, "/", ($allSubDomain ? ".".$domain : "")."; HttpOnly", false);
				} else {
					$ret = setcookie($name, $value, $time, "/", ($allSubDomain ? ".".$domain : ""), false, true);
				}
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
		if(self::getServer('HTTP_IF_MODIFIED_SINCE')) {
			$IfModifiedSince = strtotime(substr(self::getServer('HTTP_IF_MODIFIED_SINCE'), 5));
		}
		if(!is_bool($IfModifiedSince) && $IfModifiedSince >= $LastModified_unix) {
			header(self::getServer('SERVER_PROTOCOL', false, "HTTP 1/0").' 304 Not Modified');
			return false;
		}
		header('Last-Modified: '.$LastModified);
		header('Expires: '.$LastModified);
		return true;
	}
	
	final public static function parseRequest() {
		if(function_exists('apache_request_headers')) {
			return apache_request_headers();
		} elseif(extension_loaded('http')) {
			$headers = version_compare(phpversion('http'), '2.0.0', '>=') ?	\http\Env::getRequestHeader() :	http_get_request_headers();
			return $headers;
		}
		$headers = array();
		if(self::getServer('CONTENT_TYPE')) {
			$headers['content-type'] = self::getServer('CONTENT_TYPE');
		}
		if(self::getServer('CONTENT_LENGTH')) {
			$headers['content-length'] = self::getServer('CONTENT_LENGTH');
		}
		foreach($_SERVER as $key => $value) {
			if(strpos($key, 'HTTP_')!==false) {
				continue;
			}
			$headers[str_replace('_', '-', substr($key, 5))] = $value;
		}
		return $headers;
	}
	
	final public static function setSaveMime($path) {
		$base = pathinfo($path);
		if(!is_bool($base) && isset($base['dirname']) && file_exists($base['dirname']) && is_writable($base['dirname'])) {
			self::$pathSaveMime = $path;
		}
	}
	
	final public static function getContentTypes() {
		if(!self::$pathSaveMime) {
			return array();
		}
		try {
			if(file_exists(self::$pathSaveMime)) {
				$file = file_get_contents(self::$pathSaveMime);
			} else {
				$file = file_get_contents("https://raw.githubusercontent.com/skyzyx/mimetypes/master/mimetypes.json");
				$path = explode(DS, self::$pathSaveMime);
				$endPath = end($path);
				$path = str_replace($endPath, "", self::$pathSaveMime);
				if(is_writable($path)) {
					file_put_contents(self::$pathSaveMime, $file);
				}
			}
			$json = json_decode($file, true);
			return $json;
		} catch(Exception $ex) {
			return array();
		}
	}
	
	final public static function setContentType($type, $charset = "") {
		if(!self::$pathSaveMime) {
			return false;
		}
		try {
			if(strpos($type, "/")===false) {
				$json = self::getContentTypes();
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

	final public static function contentType($type, $charset = "") {
		return self::setContentType($type, $charset);
	}
	
	final public static function StatusHeader($code) {
		$code = abs(intval($code));
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

	final public static function sendError() {
		self::sendHeader(404);
	}

	final public static function sendHeader($text = false, $code = false) {
		if($text!==false && is_numeric($text) && $code===false) {
			$status = self::StatusHeader($text);
			$code = $text;
		} else {
			$status = $text;
		}
		if($text===false) {
			$status = self::StatusHeader(200);
		}
		if($code===false) {
			$code = 200;
		}
		$sapi_type = php_sapi_name();
		if(strpos($sapi_type, 'cgi')!==false) {
			header("Status: ".$code." ".$status);
		}
		header("HTTP/2.0 ".$code." ".$status);
	}
	
	final public static function Location($link, $time = 0, $exit = true, $code = 301) {
		if(defined("PHP_SAPI") && (PHP_SAPI != 'cgi-fcgi')) {
			$status = self::StatusHeader($code);
			header("HTTP/2.0 ".$code." ".$status);
		}
		if($time == 0) {
			if(function_exists("header_remove")) {
				header_remove("Location");
			}
			$link = self::ClearLocation($link);
			if(class_exists("cardinalEvent") && method_exists("cardinalEvent", "execute")) {
				$link = cardinalEvent::execute("HTTP::Location", $link);
			}
			header("Location: ".$link);
		} else {
			if(function_exists("header_remove")) {
				header_remove("Refresh");
			}
			$link = self::ClearLocation($link);
			if(class_exists("cardinalEvent") && method_exists("cardinalEvent", "execute")) {
				$link = cardinalEvent::execute("HTTP::Location", $link);
			}
			header("Refresh: ".$time."; url=".$link);
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
		if(class_exists("cardinalEvent") && method_exists("cardinalEvent", "execute")) {
			$echo = cardinalEvent::execute("HTTP::echos", $echo);
		}
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

	final public static function ajax($arr) {
		self::setContentType("application/json", config::Select("charset"));
		callAjax();
		self::echos(json_encode($arr), true);
	}
	
}

?>