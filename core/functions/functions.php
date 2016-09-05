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

function mrand($min = 0, $max = 0){return function_call('mrand', array($min, $max));}
function or_mrand($min = 0, $max = 0) {
	if($min==0 && $max==0) {
		if(function_exists("random_int") && defined("PHP_INT_MIN")) {
			$min = PHP_INT_MIN;
		}
		if(function_exists("random_int") && defined("PHP_INT_MAX")) {
			$max = PHP_INT_MAX;
		} else {
			if(function_exists("mt_rand")) {
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

function location($link, $time = 0, $exit = true){return function_call('location', array($link, $time, $exit));}
function or_location($link, $time = 0, $exit = true) {
	if($time == 0) {
		header("Location: ".templates::view($link));
	} else {
		header("Refresh: ".$time."; url=".templates::view($link));
	}
	if($exit) {
		exit();
	}
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

function read_dir($dir, $type = "all") {
	$files = array();
	if(is_dir($dir)) {
		if($dh = dir($dir)) {
			while(($file = $dh->read()) !== false) {
				if(is_file($dir.$file) && ($type=="all" || strpos($file, $type)!==false)) {
					$files[] = $file;
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

function vdump($var, $title = "") {
	echo '<pre style="text-align:left;">'. (isset($backtrace[0]) ? "Called: ".$backtrace[0]['file']." [".$backtrace[0]['line']."]\n\n" : "").(($title) ? "<b>".$title."</b>\n\n" : '');
	var_dump($var);
	echo '</pre>';
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