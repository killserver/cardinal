<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

function function_call($func_name, $func_arg = array()) {
global $manifest;
	if(isset($manifest['functions'][$func_name]) && is_array($manifest['functions'][$func_name]) && !is_callable($manifest['functions'][$func_name])) {
		/*foreach($manifest['functions'][$func_name] as $func_chain_name) {
			if(is_callable($func_chain_name)) {
				$result = call_user_func_array($func_chain_name, $func_arg);
			}
		}*/
		for($is=0;$is<sizeof($manifest['functions'][$func_name]);$is++) {
			if(is_callable($func_chain_name)) {
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

function mrand($min, $max){return function_call('mrand', array($min, $max));}
function or_mrand($min, $max) {
	if(function_exists("mt_rand")) {
		return mt_rand($min, $max);
	} else {
		return rand($min, $max);
	}
}

function location($link, $time=0){return function_call('location', array($link, $time));}
function or_location($link, $time=0) {
global $templates;
	if($time == 0) {
		header("Location: ".$templates->view($link));
	} else {
		header("Refresh: ".$time."; url=".$templates->view($link));
	}
exit();
}


function search_file($file, $dir = null){return function_call('search_file', array($file, $dir));}
function or_search_file($file, $dir = null) {
	if(empty($dir)) {
		return glob(ROOT_PATH.$file);
	} else {
		$ed = explode("/", $dir);
		$en = end($ed);
		if(!empty($en)) {
			$dir = $dir."/";
		}
		return glob(ROOT_PATH.$dir.$file);
	}
}

function read_dir($dir) {
	$files = array();
	if(is_dir($dir)) {
		if($dh = dir($dir)) {
			while(($file = $dh->read()) !== false) {
				if(is_file($dir.$file)) {
					$files[] = $file;
				}
			}
		$dh->close();
		}
	}
return $files;
}

/*function check_smartphone() {
	$phone_array = array('iphone', 'android', 'pocket', 'palm', 'windows ce', 'windowsce', 'mobile windows', 'cellphone', 'opera mobi', 'operamobi', 'ipod', 'small', 'sharp', 'sonyericsson', 'symbian', 'symbos', 'opera mini', 'nokia', 'htc_', 'samsung', 'motorola', 'smartphone', 'blackberry', 'playstation portable', 'windows phone', 'ucbrowser');
	$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
	foreach($phone_array as $value) {
		if(strpos($agent, $value) !== false) return true;
	}
	return false;

}*/
?>