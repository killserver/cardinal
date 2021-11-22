<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

class Debug {
	
	private static $charset = "utf-8";
	private static $echoDebug = false;
	private static $disableShow = false;

	final public static function activShow($show = true) {
		self::$disableShow = (!$show);
	}
	
	final public static function echoDebugMode($mode = "") {
		if($mode!=="") {
			self::$echoDebug = $mode;
		} else {
			return self::$echoDebug;
		}
	}
	
	final public static function activation($mode = DEBUG_MODE_ONLY_DEBUG, $echo = false) {
		ini_set('display_errors', 1);
		error_reporting(E_ALL);
		if(extension_loaded('xdebug')) {
			ini_set('xdebug.collect_params', 3);
		}
		if($mode!==DEBUG_MODE_ONLY_DEBUG) {
			addEvent("shutdownCardinal", "Debug::DebugAll", array($mode, $echo));
		}
	}
	
	final public static function TplDebug($arr) {
		$filesizename = array(" Bytes", " Kb", " Mb", " Gb", " Tb", " Pb", " Eb", " Zb", " Yb");
		templates::assign_var("time_work", $arr['time_work']);
		templates::assign_var("memory", $arr['memory']);
		for($i=0;$i<sizeof($arr['db']['list']);$i++) {
			templates::assign_vars($arr['db']['list'][$i], "db_query", "db".$i);
		}
		templates::assign_var("db_time", number_format($arr['db']['time'], 5, '.', ' '));
		templates::assign_var("db_count", $arr['db']['num']);
		/* Start Include */
		$size = $lines = 0;
		templates::assign_var("count_include", sizeof($arr['included_files']));
		for($i=0;$i<sizeof($arr['included_files']);$i++) {
			templates::assign_vars(array(
				"file" => $arr['included_files'][$i]['file'],
				"size" => $arr['included_files'][$i]['size'],
				"line" => $arr['included_files'][$i]['lines'],
			), "include", "include".$i);
			$lines += $arr['included_files'][$i]['lines'];
			$size += $arr['included_files'][$i]['sizeNum'];
		}
		templates::assign_var("total_includeline", $lines);
		$size = sprintf("%u", $size);
		$size = ($size ? round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i] : '0 Bytes');
		templates::assign_var("total_includesize", $size);
		/* End Include */
		/* Start GET */
		if(is_array($_GET)) {
			$get = $_GET;
		} else {
			$get = array();
		}
		templates::assign_var("count_get", sizeof($get));
		$i = 0;
		foreach($get as $k => $v) {
			templates::assign_vars(array("key" => $k, "val" => (is_string($v) ? $v : var_export($v, true))), "gets", "get".$i);
			$i++;
		}
		/* End GET */
		/* Start POST */
		if(is_array($_POST)) {
			$post = $_POST;
		} else {
			$post = array();
		}
		templates::assign_var("count_post", sizeof($post));
		$i = 0;
		foreach($post as $k => $v) {
			templates::assign_vars(array("key" => $k, "val" => (is_string($v) ? $v : var_export($v, true))), "posts", "posts".$i);
			$i++;
		}
		/* End POST */
		/* Start COOKIE */
		if(is_array($_COOKIE)) {
			$cookie = $_COOKIE;
		} else {
			$cookie = array();
		}
		templates::assign_var("count_cookie", sizeof($cookie));
		$i = 0;
		foreach($cookie as $k => $v) {
			templates::assign_vars(array("key" => $k, "val" => (is_string($v) ? $v : var_export($v, true))), "cookies", "cookie".$i);
			$i++;
		}
		/* End COOKIE */
		/* Start SERVER */
		templates::assign_var("count_server", sizeof($_SERVER));
		$i = 0;
		foreach($_SERVER as $k => $v) {
			templates::assign_vars(array("key" => $k, "val" => (is_string($v) ? $v : var_export($v, true))), "servers", "server".$i);
			$i++;
		}
		/* End SERVER */
		/* Start Route */
		$params = Route::param();
		templates::assign_var("count_router", sizeof($params));
		$i = 0;
		foreach($params as $k => $v) {
			templates::assign_vars(array("key" => $k, "val" => (is_string($v) ? $v : var_export($v, true))), "router", "router".$i);
			$i++;
		}
		/* End Route */
		$i = 0;
		$events = cardinalEvent::getEventList();
		templates::assign_var("count_events", sizeof($events));
		foreach($events as $k => $v) {
			$time = $v['time'];
			$called = $v['called'];
			$v = $v['data'];
			templates::assign_vars(array(
				"name" => (isset($v['function']) && is_string($v['function']) && strpos($v['function'], "Ref")!==false ? "<span style='color:#00ff00'>[?]</span>&nbsp;" : "").$k,
				"file" => str_replace(ROOT_PATH, DS, (isset($v['file']) ? $v['file'] : "")),
				"line" => (isset($v['line']) ? $v['line'] : ""),
				"time" => self::timespan($time, microtime_float()),
				"called" => $called,
				"args" => sizeof($v['args'])-1,
			), "events", "events".$i);
			$i++;
		}
		$i = 0;
		$work = $arr['time_work'];
		foreach(self::$breakpoint as $name => $v) {
            $left = round($v['start'] / $v['duration'], 2);
            $width = min(round($work / $v['duration'], 2), 100 - $left);
			templates::assign_vars(array("width" => $width, "left" => $left, "label" => $name), "timeline", "timeline".$i);
			$i++;
		}

		templates::dir_skins("skins");
		templates::set_skins("");
		$tpl = templates::completed_assign_vars("debug_panel", "core");
		return templates::view($tpl);
	}

	private static function timespan($seconds = 1, $time = 0) {
		if(!is_numeric($seconds)) {
			$seconds = 1;
		}
		if(is_numeric($time) && $time<=0) {
			$time = time();
		}
		if($time >= $seconds) {
			$seconds = $time - $seconds;
		}

		$result = array();
		$years = floor($seconds / 31536000);

		if($years > 0) {
			$result[] = $years.' years';
		}

		$seconds -= $years*31536000;
		$months = floor($seconds/2628000);

		if($years > 0 || $months > 0) {
			if($months > 0) {
				$result[] = $months.' months';
			}
			$seconds -= $months * 2628000;
		}

		$weeks = floor($seconds / 604800);
		if($years > 0 || $months > 0 || $weeks > 0) {
			if($weeks > 0) {
				$result[] = $weeks.' week';
			}
			$seconds -= $weeks * 604800;
		}

		$days = floor($seconds / 86400);
		if($months > 0 || $weeks > 0 || $days > 0) {
			if($days > 0) {
				$result[] = $days.' days';
			}
			$seconds -= $days * 86400;
		}

		$hours = floor($seconds / 3600);
		if($days > 0 || $hours > 0) {
			if($hours > 0) {
				$result[] = $hours.' hours';
			}
			$seconds -= $hours * 3600;
		}

		$minutes = floor($seconds / 60);
		if($days > 0 || $hours > 0 || $minutes > 0) {
			if($minutes > 0) {
				$result[] = $minutes.' minutes';
			}
			$seconds -= $minutes * 60;
		}

		if(empty($result)) {
			$result[] = $seconds.' seconds';
		}
	return implode(" ", $result);
	}
	
	final public static function FileLine($file) {
		$lines = 0;
		$fh = fopen($file, "r");
		while(fgets($fh) !== false) {
			$lines++;
		}
		fclose($fh);
		return $lines;
	}

	private static function switcher() {
		$fn = func_get_args();
		$check = $fn[0];
		$array = $fn[1];
		if(!is_array($array)) {
			$array = array($array);
		}
		$ret = false;
		for($i=0;$i<sizeof($array);$i++) {
			if($array[$i]===$check) {
				$ret = true;
				break;
			}
		}
		return $ret;
	}
	
	final public static function DebugAll($type = "", $echo = false) {
		if(empty($type)) {
			$type = DEBUG_MEMORY * DEBUG_TIME * DEBUG_INCLUDE * DEBUG_DB * DEBUG_TEMPLATE;
		}
		$incl_files = $files = $db_querys = $include = array();
		$memory = $memoryNum = $time = $incl_filesize = $filesize = $db_time = $db_num = $tmp = 0;
		$filesizename = array(" Bytes", " Kb", " Mb", " Gb", " Tb", " Pb", " Eb", " Zb", " Yb");
		if(self::switcher($type, DEBUG_MEMORY)) {
			$size = sprintf("%u", memory_get_peak_usage()-MEMORY_GET);
			$memoryNum = $size;
			$memory = $size ? round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i] : '0 Bytes';
			unset($size, $filesizename, $i);
		} else if(self::switcher($type, DEBUG_TIME)) {
			$time = microtime_float();
			$Times = $time-SYSTEM_TIME_START_FLOAT;
			$Times += $tmp;
			$Times += $db_time;
			if($Times<0) {
				$Times = 0;
			}
		} else if(self::switcher($type, DEBUG_INCLUDE)) {
			$num = 0;
			$incl_files = get_included_files();
			foreach($incl_files as $f) {
				if(file_exists($f)) {
					$include[$num]['file'] = str_replace(ROOT_PATH, DS, $f);
					$include[$num]['lines'] = self::FileLine($f);
					$size = sprintf("%u", filesize($f));
					$include[$num]['sizeNum'] = filesize($f);
					$include[$num]['size'] = ($size ? round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i] : '0 Bytes');
					$num++;
				}
			}
			unset($size, $i);
		} else if(self::switcher($type, DEBUG_DB)) {
			$db_time = db::$time;
			$db_num = db::$num;
			$db_querys = db::$querys;
		} else if(self::switcher($type, DEBUG_TEMPLATE)) {
			$tmp = templates::$time;
		} else if(self::switcher($type, array(DEBUG_MEMORY * DEBUG_TIME * DEBUG_INCLUDE, DEBUG_CORE))) {
			$num = 0;
			$incl_files = get_included_files();
			foreach($incl_files as $f) {
				if(file_exists($f)) {
					$include[$num]['file'] = str_replace(ROOT_PATH, DS, $f);
					$include[$num]['lines'] = self::FileLine($f);
					$size = sprintf("%u", filesize($f));
					$include[$num]['sizeNum'] = filesize($f);
					$include[$num]['size'] = ($size ? round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i] : '0 Bytes');
					$num++;
				}
			}
			unset($size, $i);
			$size = sprintf("%u", memory_get_peak_usage()-MEMORY_GET);
			$memoryNum = $size;
			$memory = $size ? round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i] : '0 Bytes';
			unset($size, $filesizename, $i);
		} else if(self::switcher($type, array(DEBUG_INCLUDE, DEBUG_FILE))) {
			$num = 0;
			$incl_files = get_included_files();
			foreach($incl_files as $f) {
				if(file_exists($f)) {
					$include[$num]['file'] = str_replace(ROOT_PATH, DS, $f);
					$include[$num]['lines'] = self::FileLine($f);
					$size = sprintf("%u", filesize($f));
					$include[$num]['sizeNum'] = filesize($f);
					$include[$num]['size'] = ($size ? round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i] : '0 Bytes');
					$num++;
				}
			}
			unset($size, $i);
		} else if(self::switcher($type, array(DEBUG_DB * DEBUG_TEMPLATE, DEBUG_DBTEMP))) {
			$db_time = db::$time;
			$db_num = db::$num;
			$db_querys = db::$querys;
			$tmp = templates::$time;
		} else {
			$tmp = templates::$time;
			$db_time = db::$time;
			$db_num = db::$num;
			$db_querys = db::$querys;
			$num = 0;
			$incl_files = get_included_files();
			foreach($incl_files as $f) {
				if(file_exists($f)) {
					$include[$num]['file'] = str_replace(ROOT_PATH, DS, $f);
					$include[$num]['lines'] = self::FileLine($f);
					$size = sprintf("%u", filesize($f));
					$include[$num]['sizeNum'] = filesize($f);
					$include[$num]['size'] = ($size ? round($size / pow(1024, ($isize = floor(log($size, 1024)))), 2) . $filesizename[$isize] : '0 Bytes');
					$num++;
				}
			}
			unset($size, $isize);
			$size = sprintf("%u", memory_get_peak_usage()-MEMORY_GET);
			$memoryNum = $size;
			$memory = $size ? round($size / pow(1024, ($isize = floor(log($size, 1024)))), 2) . $filesizename[$isize] : '0 Bytes';
			unset($size, $filesizename, $isize);
		}
		$time = microtime_float();
		$Times = $time-SYSTEM_TIME_START_FLOAT;
		$Times += $tmp;
		$Times += $db_time;
		if($Times<0) {
			$Times = 0;
		}
		$arr = array("memory" => $memory, "memoryNum" => $memoryNum, "time_work" => $Times, "included_files" => $include, "work_template" => $tmp, "db" => array("time" => $db_time, "num" => $db_num, "list" => $db_querys));
		unset($memory, $time, $incl_filesize, $incl_files, $include, $files, $tmp, $db_time, $db_num, $db_querys);
		if(!$echo) {
			return $arr;
		} else {
			$arr = self::TplDebug($arr);
			self::viewOnPage($arr);
		}
	}
	
	public static function viewOnPage($data) {
		if(!self::$disableShow) {
			echo $data;
		}
	}
	
	private static function strip_ascii_ctrl($str) {
		return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $str);
	}
	
	private static function is_ascii($str) {
		if(is_array($str)) {
			$str = implode($str);
		}
		return !preg_match('/[^\x00-\x7F]/S', $str);
	}
	
	private static function clean($var, $charset = "") {
		if(!$charset) {
			// Use the application character set
			$charset = self::$charset;
		}
		if(is_array($var) || is_object($var)) {
			foreach($var as $key => $val) {
				// Recursion!
				$var[self::clean($key)] = self::clean($val);
			}
		} elseif(is_string($var) && $var !== '') {
			// Remove control characters
			$var = self::strip_ascii_ctrl($var);
			if(!self::is_ascii($var)) {
				// Temporarily save the mb_substitute_character() value into a variable
				$mb_substitute_character = mb_substitute_character();
				// Disable substituting illegal characters with the default '?' character
				mb_substitute_character('none');
				// convert encoding, this is expensive, used when $var is not ASCII
				$var = mb_convert_encoding($var, $charset, $charset);
				// Reset mb_substitute_character() value back to the original setting
				mb_substitute_character($mb_substitute_character);
			}
		}
		return $var;
	}

	private static function getExecutionFileStack($backtrace) {
		$ret = array("file" => "cloud execution", "line" => -1);
		for($i=0;$i<sizeof($backtrace);$i++) {
			if(isset($backtrace[$i]['class']) && isset($backtrace[$i]['function']) && $backtrace[$i]['class']=="Debug" && $backtrace[$i]['function']=="vdump") {
				continue;
			}
			if(isset($backtrace[$i]['file']) && strpos($backtrace[$i]['file'], "cardinalEvent.php")!==false) {
				continue;
			}
			if(isset($backtrace[$i]['file']) && strpos($backtrace[$i]['file'], "loadConfig.php")!==false && isset($backtrace[$i]['class']) && $backtrace[$i]['class']=="cardinalEvent") {
				continue;
			}
			$ret = $backtrace[$i];
			break;
		}
		if(isset($ret['function']) && $ret['function']=="execEvent" && isset($ret['args']) && isset($ret['args'][0])) {
			$eventer = cardinalEvent::getExecutors($ret['args'][0]);
			if(isset($eventer[0]) && isset($eventer[0]['loader'])) {
				$ret = $eventer[0]['loader'];
			}
			$ret['event'] = $ret['args'][0];
		}
		return $ret;
	}

	final public static function vdump() {
		global $printedVdump;
		$list = func_get_args();
		// $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		$e = new \Exception;
		$backtrace = $e->getTrace();
		if(!defined("IS_CLI") && (!isset($printedVdump) || $printedVdump===false)) {
			if(function_exists("nocache_headers")) { nocache_headers(); }
			echo '<!DOCTYPE html><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta name="viewport" content="width=device-width"><title>Cardinal &rsaquo; Debug</title><meta name="robots" content="noindex,follow"></head><style>html { background: #f1f1f1; } body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif; } div.container { background: #fff; color: #444; margin: 2em auto; padding: 0em 2em 1em; max-width: 700px; -webkit-box-shadow: 0 1px 3px rgba(0,0,0,0.13); box-shadow: 0 1px 3px rgba(0,0,0,0.13); } div.info { border-bottom: 1px solid #dadada; clear: both; color: #666; background: #fff; word-break: break-all; max-width: 100%; font-size: 19px; padding: 1em 0px 7px; font-family: \'Roboto\'; margin-bottom: 1.5rem; } pre { text-align: left; margin: 0px 0px 1em; font-family: Consolas, Monaco, monospace; font-size: 12px; word-break: break-word; max-width: 100%; white-space: pre-wrap; line-height: 20px; }</style><body>';
			if(function_exists("addEvent")) { addEvent("shutdownCardinal", function() { echo "</body></html>"; }, "", 999999999); }
		}
		$executionFile = self::getExecutionFileStack($backtrace);
		if(!defined("IS_CLI")) {
			echo PHP_EOL.'<div class="container"><div class="info">'. "<b>Called:</b><span> ".(isset($executionFile['event']) ? "<span style='color:green'>(".$executionFile['event'].")</span> " : "").(defined("ROOT_PATH") ? str_replace(ROOT_PATH, DS, $executionFile['file']) : $executionFile['file'])." [".$executionFile['line']."]".(file_exists($executionFile['file']) ? "&nbsp;<i>".date("d-m-Y H:i:s", filectime($executionFile['file']))."</i>" : "")."</span><br></div>";
			echo PHP_EOL.'<pre>';
		} else {
			echo "Called: ".(isset($executionFile['event']) ? "(".$executionFile['event'].") " : "").$executionFile['file']." [".$executionFile['line']."]".(file_exists($executionFile['file']) ? " ".date("d-m-Y H:i:s", filectime($executionFile['file'])) : "")." ".PHP_EOL.PHP_EOL;
		}
		if(sizeof($list)>0) {
			ob_start();
			call_user_func_array("var_dump", $list);
			$t = ob_get_clean();
			if(!defined("IS_CLI")) {
				echo htmlspecialchars($t);
			} else {
				echo $t;
			}
		}
		if(!defined("IS_CLI")) {
			echo '</pre></div>'.PHP_EOL;
		} else {
			echo PHP_EOL.PHP_EOL;
		}
		$printedVdump = true;
	}
	
	
	private static $breakpoint = array();
	
	final public static function StartBreakPoint($name) {
		$back = debug_backtrace();
		$time = microtime(true);
		self::$breakpoint[$name] = array("relative_start" => $time, "start" => $time-SYSTEM_MICROTIME, "duration" => $time, "file" => isset($back[0]) ? $back[0] : array());
	}
	
	final public static function StopBreakPoint($name, $data = "") {
		if(!isset(self::$breakpoint[$name])) {
			return;
		}
		$time = microtime(true);
		self::$breakpoint[$name]["duration"] = $time-self::$breakpoint[$name]["duration"];
		self::$breakpoint[$name]["end"] = $time-SYSTEM_MICROTIME;
		self::$breakpoint[$name]["relative_end"] = $time;
		if($data!=="") {
			self::$breakpoint[$name]['dataSend'] = $data;
		}
	}

	final public static function breakpoint($name, $data = "") {
		if(!isset(self::$breakpoint[$name])) {
			return self::StartBreakPoint($name);
		} else {
			return self::StopBreakPoint($name, $data);
		}
	}

	final public static function getCaller() {
		$listener = debug_backtrace();
		$file = $listener[0]['file'];
		$exp = explode(DS, $file);
		$end = end($exp);
		return $end;
	}

	const LOG_LEVEL_NONE = 0;
	const LOG_LEVEL_INFO = 1;
	const LOG_LEVEL_WARNING = 2;
	const LOG_LEVEL_ERROR = 3;
	public static function getLevelError($val) {
		switch ($val) {
			case self::LOG_LEVEL_NONE:
				$ret = "NONE";
				break;
			case self::LOG_LEVEL_INFO:
				$ret = "INFO";
				break;
			case self::LOG_LEVEL_WARNING:
				$ret = "WARNING";
				break;
			case self::LOG_LEVEL_ERROR:
				$ret = "ERROR";
				break;
			default:
				$ret = "UNDEFINED";
				break;
		}
		return $ret;
	}

	final public static function Log($mess, $level = self::LOG_LEVEL_INFO, $file = "debug_log.txt") {
		$debug = debug_backtrace();
		$mess = "{".date("H:i:s d-m-Y")."} [".self::getLevelError($level)."] ".$mess." - ".str_replace(ROOT_PATH, "", $debug[0]['file'])." [".$debug[0]['line']."]";
		if(defined("PATH_LOGS") && is_writable(PATH_LOGS)) {
			file_put_contents(PATH_LOGS.$file, $mess.PHP_EOL, FILE_APPEND);
		}
	}

}