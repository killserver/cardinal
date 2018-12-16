<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

class Debug {
	
	private static $charset = "utf-8";
	private static $echoDebug = false;
	private static $limitOnView = 1024;
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

	final private static function switcher() {
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
	
	final public static function limitOnView($limit = "") {
		if($limit!=="") {
			self::$limitOnView = $limit;
		} else {
			return self::$limitOnView;
		}
	}
	
	final public static function vars() {
		if(func_num_args() === 0) {
			if(self::$echoDebug) {
				self::viewOnPage("");
			} else {
				return false;
			}
		}
		// Get all passed variables
		$variables = func_get_args();
		$output = array();
		foreach($variables as $var) {
			$output[] = self::_dump($var, self::$limitOnView);
		}
		if(self::$echoDebug) {
			self::viewOnPage('<pre class="debug">'.implode("\n", $output).'</pre>');
		} else {
			return '<pre class="debug">'.implode("\n", $output).'</pre>';
		}
	}
	
	final private static function nstrlen($text) {
		if(function_exists("mb_strlen") && defined('MB_OVERLOAD_STRING') && ini_get('mbstring.func_overload') & MB_OVERLOAD_STRING) {
			return mb_strlen($text, self::$charset);
		} elseif(function_exists("iconv_strlen")) {
			return iconv_strlen($text, self::$charset);
		} else {
			return strlen($text);
		}
	}
	
	final private static function nsubstr($text, $start, $end = "") {
		if(empty($end)) {
			$end = self::nstrlen($text);
		}
		if(function_exists("mb_substr") && defined('MB_OVERLOAD_STRING') && ini_get('mbstring.func_overload') & MB_OVERLOAD_STRING) {
			return mb_substr($text, $start, $end, self::$charset);
		} elseif(function_exists("iconv_substr")) {
			return iconv_substr($text, $start, $end, self::$charset);
		} else {
			return substr($text, $start, $end);
		}
	}
	
	final private static function strip_ascii_ctrl($str) {
		return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $str);
	}
	
	final private static function is_ascii($str) {
		if(is_array($str)) {
			$str = implode($str);
		}
		return !preg_match('/[^\x00-\x7F]/S', $str);
	}
	
	final private static function clean($var, $charset = "") {
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
	
	final public static function _dump(&$var, $length = 128, $limit = 10, $level = 0) {
		if($var===NULL) {
			return '<small style="color:green;font-weight:bold;">NULL</small>';
		} elseif(is_bool($var)) {
			return '<small style="color:green;font-weight:bold;">bool</small> '.($var ? 'TRUE' : 'FALSE');
		} elseif(is_float($var)) {
			return '<small style="color:green;font-weight:bold;">float</small> '.$var;
		} elseif(is_resource($var)) {
			if(($type = get_resource_type($var)) === 'stream' AND $meta = stream_get_meta_data($var)) {
				$meta = stream_get_meta_data($var);
				if(isset($meta['uri'])) {
					$file = $meta['uri'];
					return '<small style="color:green;font-weight:bold;">resource</small><span style="color:green;font-weight:bold;">('.$type.')</span> '.htmlspecialchars($file, ENT_NOQUOTES, self::$charset);
				}
			} else {
				return '<small style="color:green;font-weight:bold;">resource</small><span style="color:green;font-weight:bold;">('.$type.')</span>';
			}
		} elseif(is_string($var)) {
			// Clean invalid multibyte characters. iconv is only invoked
			// if there are non ASCII characters in the string, so this
			// isn't too much of a hit.
			$var = self::clean($var, self::$charset);
			if($length!=0 && self::nstrlen($var)>$length) {
				// Encode the truncated string
				$str = htmlspecialchars(self::nsubstr($var, 0, $length), ENT_NOQUOTES, self::$charset).'&nbsp;&hellip;';
			} else {
				// Encode the string
				$str = htmlspecialchars($var, ENT_NOQUOTES, self::$charset);
			}
			return '<small style="color:green;font-weight:bold;">string</small><span style="color:green;font-weight:bold;">('.self::nstrlen($var).')</span> "'.$str.'"';
		} elseif(is_array($var)) {
			$output = array();
			// Indentation for this variable
			$space = str_repeat($s = '    ', $level);
			// Make a unique marker - force it to be alphanumeric so that it is always treated as a string array key
			$marker = uniqid("\x00")."x";
			if(empty($var)) {
				// Do nothing
			} elseif(isset($var[$marker])) {
				$output[] = "(\n".$space.$s."*RECURSION*\n".$space.")";
			} elseif($level < $limit) {
				$output[] = "<span>(";
				$var[$marker] = TRUE;
				foreach($var as $key => & $val) {
					if($key === $marker) {
						continue;
					}
					if(!is_int($key)) {
						$key = '"'.htmlspecialchars($key, ENT_NOQUOTES, self::$charset).'"';
					}
					$output[] = $space.$s.$key." => ".self::_dump($val, $length, $limit, $level + 1);
				}
				unset($var[$marker]);
				$output[] = $space.")</span>";
			} else {
				// Depth too great
				$output[] = "(\n".$space.$s."...\n".$space.")";
			}
			return '<small style="color:green;font-weight:bold;">array</small><span style="color:green;font-weight:bold;">('.count($var).')</span> '.implode("\n", $output);
		} elseif(is_object($var)) {
			// Copy the object as an array
			$array = (array) $var;
			$output = array();
			// Indentation for this variable
			$space = str_repeat($s = '    ', $level);
			$hash = spl_object_hash($var);
			// Objects that are being dumped
			$objects = array();
			if(empty($var)) {
				// Do nothing
			} elseif(isset($objects[$hash])) {
				$output[] = "{\n".$space.$s."*RECURSION*\n".$space."}";
			} elseif($level < $limit) {
				$output[] = "<code>{";
				$objects[$hash] = true;
				foreach($array as $key => & $val) {
					if(isset($key[0]) && $key[0] === "\x00") {
						// Determine if the access is protected or protected
						$access = '<small style="color:purple;font-weight:bold;">'.(($key[1] === '*') ? 'protected' : 'private').'</small>';
						// Remove the access level from the variable name
						$key = substr($key, strrpos($key, "\x00") + 1);
					} else {
						$access = '<small style="color:purple;font-weight:bold;">public</small>';
					}
					$output[] = $space.$s.$access." \$".$key." => ".self::_dump($val, $length, $limit, $level + 1);
				}
				unset($objects[$hash]);
				$output[] = $space."}</code>";
			} else {
				// Depth too great
				$output[] = "{\n".$space.$s."...\n".$space."}";
			}
			return '<small>object</small> <span>'.get_class($var).'('.count($array).')</span> '.implode("\n", $output);
		} else {
			return '<small style="color:green;font-weight:bold;">'.gettype($var).'</small> '.htmlspecialchars(print_r($var, TRUE), ENT_NOQUOTES, self::$charset);
		}
	}
	
	
	private static $breakpoint;
	private static $breakpoint_start;
	private static $start_time;
	private static $stop_time;
	
	final private static function GetPartTime() {
		$part_time = explode(' ', microtime());
		return $part_time[1].substr($part_time[0], 1);
	}

	final private static function StartTime() {
		self::$start_time = self::GetPartTime();
	}

	final private static function EndTime() {
		self::$stop_time = self::GetPartTime();
	}
	
	final public static function StartBreakPoint() {
		$back = debug_backtrace();
		self::$breakpoint_start = array("time" => self::GetPartTime(), "start" => isset($back[0]) ? $back[0] : array());
	}
	
	final public static function StopBreakPoint($data = "") {
		$breakpoint_stop = self::GetPartTime();
		$back = debug_backtrace();
		$arr = array();
		$arr = array_merge($arr, self::$breakpoint_start);
		$arr = array_merge($arr, array("time" => bcsub($breakpoint_stop, self::$breakpoint_start["time"], 4), "data" => isset($back[0]) ? $back[0] : array()));
		if($data!=="") {
			$arr['dataSend'] = $data;
		}
		self::$breakpoint[] = $arr;
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
	
	public function __destruct() {
		self::EndTime();
		$time = bcsub(self::$stop_time, self::$start_time, 4);
		print("<div class=\"debug\" style=\"display: table; margin: 0px auto; padding: 1em; border: 0.1em dashed #333;\">");
		print("<p>Generation script - ". $time. " second</p>");
		if(sizeof(self::$breakpoint)>0) {
			for($i=0;$i<sizeof(self::$breakpoint);$i++) {
				$value = self::$breakpoint[$i];
				$file = str_replace(ROOT_PATH, "", $value['start']['file']);
				$endFile = str_replace(ROOT_PATH, "", $value['data']['file']);
				print("<p>".$file." [".$value['start']['line']."] ".($file!=$endFile ? "- ".$endFile : "~ "). " [".$value['data']['line']."] block - ". $value['time']. " second</p>");
				if(isset($value['dataSend'])) {
					print("<p>Data send:<pre>".self::_dump($value['dataSend'])."</pre></p>");
				}
			}
		}
		print("</div>");
	}
}