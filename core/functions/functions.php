<?php
/*
 *
 * @version 2015-10-07 17:50:38 1.25.6-rc3
 * @copyright 2014-2015 KilleR for Cardinal Engine
 *
 * Version Engine: 1.25.6-rc3
 * Version File: 1
 *
 * 1.1
 * rebuild rand and location functions
 * 1.2
 * add support php 7 for rand function
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

function function_call($func_name, $func_arg = array()) {
global $manifest;
	if(isset($manifest['functions'][$func_name]) && is_array($manifest['functions'][$func_name]) && !is_callable($manifest['functions'][$func_name])) {
		for($is=0;$is<sizeof($manifest['functions'][$func_name]);$is++) {
			if(is_callable($manifest['functions'][$func_name][$is])) {
				$result = call_user_func_array($manifest['functions'][$func_name][$is], $func_arg);
			}
		}
	} else {
		if(isset($manifest['functions'][$func_name]) && is_callable($manifest['functions'][$func_name])) {
			$func_names = $manifest['functions'][$func_name];
		} else if(is_callable('or_' . $func_name)) {
			$func_names = 'or_' . $func_name;
		} else {
			$func_names = $func_name;
		}
		if(is_callable($func_names)) {
			$result = call_user_func_array($func_names, $func_arg);
		}
	}
	return $result;
}

function get_module_url($file = "", $module = "") { return function_call('get_module_url', array($file, $module)); }
function or_get_module_url($file = "", $module = "") {
	if(empty($module)) {
		$module = debug_backtrace();
		$module = $module[0]['file'];
	}
	$moduleDir = dirname($module).DS;
	$moduleDir = str_replace(ROOT_PATH, "", $moduleDir);
	return config::Select("default_http_local").str_replace(DS, "/", $moduleDir).(!empty($file) ? $file : "");
}

function get_module_path($module = "") { return function_call('get_module_path', array($module)); }
function or_get_module_path($module = "") {
	if(empty($module)) {
		$module = debug_backtrace();
		$module = $module[0]['file'];
	}
	$moduleDir = dirname($module).DS;
	return $moduleDir;
}

function get_site_path($path) { return function_call('get_site_path', array($path)); }
function or_get_site_path($path) {
	$str = str_replace(ROOT_PATH, "", $path);
	$str = str_replace(DS, "/", $str);
	return $str;
}

function loadConfig($file = "") {
global $config;
	if($file==='' && !defined("ROOT_PATH")) {
		throw new Exception("Error load config file. Path is not set");
		die();
	}
	if(!file_exists($file) && (defined("ROOT_PATH") && !file_exists(ROOT_PATH.$file))) {
		throw new Exception("Error load config file. File is not exists");
		die();
	}
	if(defined("ROOT_PATH") && file_exists(ROOT_PATH.$file)) {
		$file = ROOT_PATH.$file;
	} else if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.$file)) {
		$file = dirname(__FILE__).DIRECTORY_SEPARATOR.$file;
	} else {
		return false;
	}
	
	$autodetect = ini_get('auto_detect_line_endings');
	ini_set('auto_detect_line_endings', '1');
	$lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	ini_set('auto_detect_line_endings', $autodetect);
	
	for($i=0;$i<sizeof($lines);$i++) {
		if(isset($lines[$i]) && isset($lines[$i][0]) && ($lines[$i][0] === '#' || $lines[$i][0] === ';')) {
			continue;
		}
		if(isset($lines[$i]) && strpos($lines[$i], "=")!==false) {
			continue;
		}
		$exp = array_map('trim', explode('=', $lines[$i], 2));
		if(function_exists('apache_getenv') && function_exists('apache_setenv')) {
			apache_setenv($exp[0], $exp[1]);
		}
		if(function_exists('putenv')) {
			putenv($exp[0]."=".$exp[1]);
		}
		$_ENV[$exp[0]] = $exp[1];
		$_SERVER[$exp[0]] = $exp[1];
		$config[$exp[0]] = $exp[1];
		if(class_exists("config", false) && method_exists("config", "Set")) {
			config::Set($exp[0], $exp[1]);
		}
	}
}	

if(!function_exists('getallheaders')) {
	function getallheaders() {
		$headers = array();
		foreach($_SERVER as $name => $value) {
			if(substr($name, 0, 5) == 'HTTP_') {
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			} else {
				$headers[$name] = $value;
			}
		}
		return $headers; 
	} 
}

if(!function_exists("RandomCompat_strlen")) {
	function RandomCompat_strlen($binary_string) {
		if(!is_string($binary_string)) {
			throw new TypeError('RandomCompat_strlen() expects a string');
		}
		if(function_exists('mb_strlen')) {
			return mb_strlen($binary_string, '8bit');
		}
		return strlen($binary_string);
	}
}

// nmail() -> new PHPMailer
// nmail("me@mail.ru") -> send test mess
// nmail("me@mail.ru", "body") -> send mess and body = second argument
// nmail("me@mail.ru", "message", "title") -> send mess
function nmail() { return function_call('nmail', func_get_args()); }
function or_nmail() {
	$server = (class_exists("HTTP", false) && method_exists("HTTP", "getServer") ? HTTP::getServer("HTTP_HOST") : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ""));
	$get = func_get_args();
	$mail = new PHPMailer(true);
	if(sizeof($get)==0) {
		return $mail;
	} else if(sizeof($get)==1) {
		$for = $get[0];
		$body = "Test message for you. This message generated automatic in Cardinal Engine".(defined("VERSION") ? " in version ".VERSION : "");
		$head = "Message for you. In site: ".$server;
	} else if(sizeof($get)==2) {
		$for = $get[0];
		$body = $get[1];
		$head = "Message for you. In site: ".$server;
	} else if(sizeof($get)==3) {
		$for = $get[0];
		$body = $get[1];
		$head = $get[2];
	} else {
		throw new Exception("This operation is not permission", 1);
		die();
	}
	$mail->CharSet = (class_exists("config") && method_exists("config", "Select") && config::Select("charset") ? config::Select("charset") : "UTF-8");
	$mail->ContentType = 'text/html';
	$mail->Priority = 1;
	$mail->From = "info@".$server;
	$mail->FromName = "info";
	if(!is_array($for)) {
		$for = array($for => "".$for);
	}
	foreach($for as $k => $v) {
		$mail->AddAddress($v, $k);
	}
	$mail->isHTML(true);
	$mail->Subject = $head;
	$mail->AltBody = $mail->Body = $body;
	try {
		$er = $mail->Send();
	} catch(Exception $ex) {
		$er = $ex;
	}
	return $er;
}

if(!defined("ROUND_HALF_UP")) {
	define("ROUND_HALF_UP", 1);
}
if(!defined("ROUND_HALF_DOWN")) {
	define("ROUND_HALF_DOWN", 1);
}
if(!defined("ROUND_HALF_EVEN")) {
	define("ROUND_HALF_EVEN", 1);
}
if(!defined("ROUND_HALF_ODD")) {
	define("ROUND_HALF_ODD", 1);
}
function nround($value, $precision = 0, $mode = ROUND_HALF_UP, $native = TRUE) { return function_call('nround', array($value, $precision, $mode, $native)); }
function or_nround($value, $precision = 0, $mode = ROUND_HALF_UP, $native = TRUE) {
	if(version_compare(PHP_VERSION, '5.3', '>=') AND $native) {
		return round($value, $precision, $mode);
	}
	if($mode === ROUND_HALF_UP) {
		return round($value, $precision);
	} else {
		$factor = ($precision === 0) ? 1 : pow(10, $precision);
		switch($mode) {
			case ROUND_HALF_DOWN:
			case ROUND_HALF_EVEN:
			case ROUND_HALF_ODD:
				if(($value * $factor) - floor($value * $factor) === 0.5) {
					if($mode === ROUND_HALF_DOWN) {
						$up = ($value < 0);
					} else {
						$up = (!(!(floor($value * $factor) & 1)) === ($mode===ROUND_HALF_EVEN));
					}

					if($up) {
						$value = ceil($value * $factor);
					} else {
						$value = floor($value * $factor);
					}
					return $value / $factor;
				} else {
					return round($value, $precision);
				}
			break;
		}
	}
}

if(!function_exists('random_bytes')) {
    function random_bytes($bytes) {
		if(!function_exists("mcrypt_create_iv")) {
			throw new Exception('Mcrypt is not installed');
		}
        try {
			if(is_numeric($bytes)) {
				$bytes += 0;
			}
			if(is_float($bytes) && $bytes > ~PHP_INT_MAX && $bytes < PHP_INT_MAX) {
				$bytes = (int) $bytes;
			}
        } catch(Exception $ex) {
            throw new Exception('random_bytes(): $bytes must be an integer');
        }
        if($bytes < 1) {
            throw new Exception('Length must be greater than 0');
        }
        $buf = mcrypt_create_iv($bytes, MCRYPT_DEV_URANDOM);
        if($buf !== false && RandomCompat_strlen($buf) === $bytes) {
            return $buf;
        }
        throw new Exception('Could not gather sufficient random data');
    }
}

function cardinal_version($check = "") {
	$isChecked = defined("INTVERSION") ? INTVERSION : VERSION;
	if(empty($check)) {
		return $isChecked;
	}
	if(stripos($check, "-")!==false) {
		$check = explode("-", $check);
		$check = current($check);
	}
	if(class_exists("config", false) && method_exists("config", "Select") && config::Select("speed_update")) {
		$if = ($check) > ($isChecked);
	} else {
		$checked = intval(str_replace(".", "0", $check));
		$version = intval(str_replace(".", "0", $isChecked));
		if(strlen($checked) > strlen($version)) {
			$version = int_pad($version, strlen($checked));
		} else if(strlen($checked) < strlen($version)) {
			$checked = int_pad($checked, strlen($version));
		}
		$if = $checked>$version;
	}
	return $if;
}

if(!defined("RAND_FLOAT_MT")) {
	define("RAND_FLOAT_MT", 1);
}
if(!defined("RAND_FLOAT_LCG")) {
	define("RAND_FLOAT_LCG", 2);
}
if(!defined("RAND_FLOAT")) {
	define("RAND_FLOAT", 3);
}

function randomFloat($min, $max, $type = RAND_FLOAT) { return function_call('randomFloat', array($min, $max, $type)); }
function or_randomFloat($min, $max, $type = RAND_FLOAT) {
	if($type===RAND_FLOAT_MT && function_exists("mt_rand") && function_exists("mt_getrandmax")) {
		return $min + abs($max - $min) * mt_rand(0, mt_getrandmax()) / mt_getrandmax(); 
	} elseif($type===RAND_FLOAT_LCG && function_exists("lcg_value")) {
		return $min + lcg_value() * abs($max - $min);
	} elseif($type===RAND_FLOAT || ($type!==RAND_FLOAT && $type!==RAND_FLOAT_LCG && $type!==RAND_FLOAT_MT)) {
		return $min + rand(0, getrandmax()) / getrandmax() * abs($max - $min);
	}
}

function sha512($str) {
	$ret = false;
	if(function_exists("mhash") && defined("MHASH_SHA512")) {
		$ret = mhash(MHASH_SHA512, $str);
	} elseif(function_exists("openssl_digest")) { //5.3.0
		$ret = openssl_digest($str, 'sha512');
	} elseif(function_exists("hash") && function_exists("hash_algos") && in_array("sha512", hash_algos())) { //5.1.2
		$ret = hash("sha512", $str);
	}
	return $ret;
}

function getMax($max) { return function_call('getMax', array($max)); }
function or_getMax($max) {
	if(function_exists('openssl_random_pseudo_bytes')) {
	     do {
	         $result = floor($max*(hexdec(bin2hex(openssl_random_pseudo_bytes(4)))/0xffffffff));
	     } while($result == $max);
	} elseif(function_exists("mt_rand")) {
		$result = mt_rand(0, $max);
	} else {
		$result = rand(0, $max);
	}
	return $result;
}

function mrand($min = 0, $max = 0) { return function_call('mrand', array($min, $max)); }
function or_mrand($min = 0, $max = 0) {
	if($min==0 && $max==0) {
		if(function_exists("random_int") && defined("PHP_INT_MIN")) {
			$min = PHP_INT_MIN;
		}
		if(function_exists("random_int") && defined("PHP_INT_MAX")) {
			$max = PHP_INT_MAX;
		} else {
			if(function_exists("mt_rand") && function_exists("mt_getrandmax")) {
				$max = mt_getrandmax();
			} else {
				$max = getrandmax();
			}
		}
	}
	if(function_exists("random_int")) {
		return random_int($min, $max);
	}
	if(function_exists("mt_rand")) {
		return mt_rand($min, $max);
	} else {
		return rand($min, $max);
	}
}

function in_array_strpos($str, $arr, $rebuild = false) {
	$ret = false;
	$arr = array_values($arr);
	for($i=0;$i<sizeof($arr);$i++) {
		if($rebuild) {
			$res = strpos($arr[$i], $str)!==false;
		} else {
			$res = strpos($str, $arr[$i])!==false;
		}
		if($res) {
			$ret = true;
			break;
		}
	}
	return $ret;
}

function location($link, $time = 0, $exit = true, $code = 301){return function_call('location', array($link, $time, $exit, $code));}
function or_location($link, $time = 0, $exit = true, $code = 301) {
	HTTP::Location(templates::view($link), $time, $exit, $code);
}


function search_file($file, $dir = "") { return function_call('search_file', array($file, $dir)); }
function or_search_file($file, $dir = "") {
	if(empty($dir)) {
		return glob(ROOT_PATH.$file);
	} else {
		$ed = explode(DS, $dir);
		$en = end($ed);
		if(!empty($en)) {
			$dir = $dir.DS;
		}
		return glob(ROOT_PATH.$dir.$file);
	}
}

function read_dir($dir, $type = "all", $addDir = false) {
	$files = array();
	if(is_dir($dir)) {
		if($dh = dir($dir)) {
			while(($file = $dh->read()) !== false) {
				if(($type=="dir" || is_file($dir.$file)) && (($type=="dir" || $type=="all") || strpos($file, $type)!==false) && $file!="." && $file!="..") {
					$files[] = ($addDir ? $dir : "").$file;
				}
			}
		$dh->close();
		}
	}
return $files;
}

if(!function_exists("boolval")) {
	function boolval($val) {
		return (bool) $val;
	}
}

function is_serialized($data) {
	if(!is_string($data)) {
		return false;
	}
	$data = trim($data);
	if('N;' == $data) {
		return true;
	}
	if(!preg_match('/^([adObis]):/', $data, $badions)) {
		return false;
	}
	switch($badions[1]) {
		case 'a':
		case 'O':
		case 's':
			if (preg_match("/^".$badions[1].":[0-9]+:.*[;}]\$/s", $data)) {
				return true;
			}
		break;
		case 'b':
		case 'i':
		case 'd':
			if(preg_match("/^".$badions[1].":[0-9.E-]+;\$/", $data)) {
				return true;
			}
		break;
	}
	return false;
}

function is_xml($string) {
	if(!defined('LIBXML_COMPACT')) {
		new Exception('libxml is required to use is_xml()');
		die();
	}
	$internal_errors = libxml_use_internal_errors();
	libxml_use_internal_errors(true);
	$result = simplexml_load_string($string) !== false;
	libxml_use_internal_errors($internal_errors);
	return $result;
}

function is_html($string) {
	return strlen(strip_tags($string)) < strlen($string);
}

if(!function_exists('is_iterable')) {
	/**
	 * Check wether or not a variable is iterable (i.e array or \Traversable)
	 *
	 * @param  array|\Traversable $iterable
	 * @return bool
	 */
	function is_iterable($iterable) {
		return (is_array($iterable) || $iterable instanceof \Traversable);
	}
}
if(!function_exists('iterable_to_array')) {
	/**
	 * Copy the iterable into an array. If the iterable is already an array, return it.
	 *
	 * @param  array|\Traversable $iterable
	 * @return array
	 */
	function iterable_to_array($iterable) {
		return (is_array($iterable) ? $iterable : iterator_to_array($iterable));
	}
}
if(!function_exists('iterable_to_traversable')) {
	/**
	 * If the iterable is not intance of \Traversable, it is an array => convert it to an ArrayIterator.
	 *
	 * @param  $iterable
	 * @return \Traversable
	 */
	function iterable_to_traversable($iterable) {
		if($iterable instanceof Traversable) {
			return $iterable;
		} elseif(is_array($iterable)) {
			return new ArrayIterator($iterable);
		} else {
			throw new \InvalidArgumentException(sprintf('Expected array or \\Traversable, got %s', (is_object($iterable) ? get_class($iterable) : gettype($iterable))));
		}
	}
}

function removeBOM($string) { 
	if(substr($string, 0, 3) == pack('CCC', 0xef, 0xbb, 0xbf)) { 
		$string=substr($string, 3); 
	} 
	return $string; 
}

function sortByKey(&$arr) {
	uksort($arr, 'strnatcmp');
	return $arr;
}

function sortByValue(&$arr) {
	uasort($arr, 'strnatcmp');
	return $arr;
}

if(!function_exists("hex2bin")) {
	function hex2bin($hexstr) {
		$n = strlen($hexstr);
		$sbin = "";
		$i = 0;
		while($i<$n) {
			$a = substr($hexstr, $i, 2);
			$c = pack("H*",$a);
			if($i==0) {
				$sbin = $c;
			} else {
				$sbin .= $c;
			}
			$i+=2;
		}
		return $sbin;
	}
}

function vdump() {
	$list = func_get_args();
	$last = end($list);
	if(is_string($last) && sizeof($list)>1) {
		$title = $last;
		$last = key($list);
		unset($list[$last]);
	} else {
		$title = "";
	}
	$backtrace = debug_backtrace();
	echo '<pre style="text-align:left;">'. (isset($backtrace[0]) ? "<b style=\"color:#00f;\">Called:</b> ".$backtrace[0]['file']." [".$backtrace[0]['line']."]\n\n" : "").(!empty($title) ? "<b>".$title."</b>\n\n" : '');
	if(sizeof($list)>0) {
		call_user_func_array("var_dump", $list);
	}
	echo '</pre>';
}

function vdebug() {
	Debug::activation();
	Debug::echoDebugMode(true);
	Debug::limitOnView(0);
	$backtrace = debug_backtrace();
	echo '<pre style="text-align:left;">'. (isset($backtrace[0]) ? "<b style=\"color:#70f;\">Called:</b> ".$backtrace[0]['file']." [".$backtrace[0]['line']."]\n\n" : "")."</pre>";
	if(func_num_args()>0) {
		echo call_user_func_array(array("Debug", "vars"), func_get_args());
	}
}

function is_ssl() {
	if(
		   (HTTP::getServer('HTTPS') && HTTP::getServer('HTTPS') !== 'off')
        || (HTTP::getServer('HTTP_X_FORWARDED_PROTO') && HTTP::getServer('HTTP_X_FORWARDED_PROTO') == 'https')
        || (HTTP::getServer('HTTP_X_FORWARDED_SSL') && HTTP::getServer('HTTP_X_FORWARDED_SSL') == 'on')
        || (HTTP::getServer('SERVER_PORT', true) && HTTP::getServer('SERVER_PORT') == 443)
        || (HTTP::getServer('HTTP_X_FORWARDED_PORT', true) && HTTP::getServer('HTTP_X_FORWARDED_PORT') == 443)
        || (HTTP::getServer('REQUEST_SCHEME', true) && HTTP::getServer('REQUEST_SCHEME') == 'https')
		|| (HTTP::getServer('CF_VISITOR', true) && HTTP::getServer('CF_VISITOR') == '{"scheme":"https"}')
		|| (HTTP::getServer('HTTP_CF_VISITOR', true) && HTTP::getServer('HTTP_CF_VISITOR') == '{"scheme":"https"}')
    ) {
		return true;
	} else {
		return false;
	}
}

function protocol() {
	return (is_ssl() ? "https" : "http");
}

function check_smartphone() {
global $mobileDetect;
	if(!is_object($mobileDetect)) {
		$mobileDetect = new Mobile_Detect();
	}
	if($mobileDetect->isMobile() || $mobileDetect->isTablet()) {
		return true;
	} else {
		return false;
	}
}

function pageBar($total, $current, $prefix = "", $postfix = "") {
	$pageBar = "";
	if($total<=5) {
		for($i=1; $i<=$total; $i++)
			if ($current != $i) {
				$pageBar .= " <a href=\"".$prefix.$i.$postfix."\">".$i."</a>";
			} else {
				$pageBar .= " <span>".$i."</span>";
			}
	} else {
		for($i=1;$i<=$total;$i++) {
			if($i<=2 || ($i>=($current-2) && $i<=($current+2)) || $i>=($total-1)) {
				if(($i == $current-2) && ($current-2 > 3)) {
					$pageBar .= ' ...';
				}
				if($i != $current) {
					$pageBar .= " <a href=\"".$prefix.$i.$postfix."\">".$i."</a>";
				} else {
					$pageBar .= " <span>".$i."</span>";
				}
				if(($i == $current + 2) && ($current + 2 < $total - 2)) {
					$pageBar .= ' ...';
				}
			}
		}
	}
	return $pageBar;
}

function callAjax() {
	templates::$gzip = false;
	Debug::activShow(false);
}

?>