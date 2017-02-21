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
			$func_name = $manifest['functions'][$func_name];
		} else {
			$func_name = 'or_' . $func_name;
		}
		if(is_callable($func_name)) {
			$result = call_user_func_array($func_name, $func_arg);
		}
	}
	return $result;
}

if(!function_exists("RandomCompat_strlen")) {
	function RandomCompat_strlen($binary_string) {
		if(!is_string($binary_string)) {
			throw new TypeError('RandomCompat_strlen() expects a string');
		}
		return strlen($binary_string);
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
		$strBuf = (defined('MB_OVERLOAD_STRING') && ini_get('mbstring.func_overload') & MB_OVERLOAD_STRING ? mb_strlen($buf, '8bit') : strlen($buf));
        if($buf !== false && RandomCompat_strlen($strBuf) === $bytes) {
            return $buf;
        }
        throw new Exception('Could not gather sufficient random data');
    }
}

function getMax($max) {
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

function mrand($min = 0, $max = 0){return function_call('mrand', array($min, $max));}
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

function location($link, $time = 0, $exit = true, $code = 302){return function_call('location', array($link, $time, $exit, $code));}
function or_location($link, $time = 0, $exit = true, $code = 302) {
	HTTP::Location(templates::view($link), $time, $exit, $code);
}


function search_file($file, $dir = ""){return function_call('search_file', array($file, $dir));}
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
	
function random_color() {
	return str_pad( dechex( mt_rand( 0, 100 ) ), 2, '0', STR_PAD_LEFT);
}

function removeBOM($string) { 
	if(substr($string, 0,3) == pack('CCC',0xef,0xbb,0xbf)) { 
		$string=substr($string, 3); 
	} 
	return $string; 
}

function sortByKey(&$arr) {
	uksort($arr, 'strnatcmp');
}

function sortByValue(&$arr) {
	usort($arr, 'strnatcmp');
}

function vdump() {
	$list = func_get_args();
	$last = end($list);
	if(is_string($last)) {
		$title = $last;
		$last = key($list);
		unset($list[$last]);
	} else {
		$title = "";
	}
	$backtrace = debug_backtrace();
	echo '<pre style="text-align:left;">'. (isset($backtrace[0]) ? "Called: ".$backtrace[0]['file']." [".$backtrace[0]['line']."]\n\n" : "").(!empty($title) ? "<b>".$title."</b>\n\n" : '');
	call_user_func_array("var_dump", $list);
	echo '</pre>';
}

function is_ssl() {
	return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']!='off') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO']=='https') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
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
?>