<?php
if(!defined("IS_CORE")) {
	die();
}
if(!defined("DEBUG_MODE_ONLY_DEBUG")) {
	define("DEBUG_MODE_ONLY_DEBUG", 1);
}
// Debug
if(!defined("DEBUG_MEMORY")) {
	define("DEBUG_MEMORY", 1);
}
if(!defined("DEBUG_TIME")) {
	define("DEBUG_TIME", 2);
}
if(!defined("DEBUG_FILES")) {
	define("DEBUG_FILES", 3);
}
if(!defined("DEBUG_INCLUDE")) {
	define("DEBUG_INCLUDE", 4);
}
if(!defined("DEBUG_DB")) {
	define("DEBUG_DB", 5);
}
if(!defined("DEBUG_TEMPLATE")) {
	define("DEBUG_TEMPLATE", 6);
}
if(!defined("DEBUG_FILE")) {
	define("DEBUG_FILE", 12);
}
if(!defined("DEBUG_CORE")) {
	define("DEBUG_CORE", 24);
}
if(!defined("DEBUG_DBTEMP")) {
	define("DEBUG_DBTEMP", 30);
}
if(!defined("DEBUG_ALL")) {
	define("DEBUG_ALL", 720);
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
			register_shutdown_function("Debug::DebugAll", $mode, $echo);
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
		/* Start File */
		$size = $lines = 0;
		templates::assign_var("count_file", sizeof($arr['use_files']));
		for($i=0;$i<sizeof($arr['use_files']);$i++) {
			templates::assign_vars(array(
				"file" => $arr['use_files'][$i]['file'],
				"size" => $arr['use_files'][$i]['size'],
				"line" => $arr['use_files'][$i]['lines'],
			), "files", "file".$i);
			$lines += $arr['use_files'][$i]['lines'];
			$size += $arr['use_files'][$i]['sizeNum'];
		}
		$size = sprintf("%u", $size);
		$size = ($size ? round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i] : '0 Bytes');
		templates::assign_var("total_fileline", $lines);
		templates::assign_var("total_filesize", $size);
		/* End File */
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
		templates::assign_var("count_get", sizeof($_GET));
		$i = 0;
		foreach($_GET as $k => $v) {
			templates::assign_vars(array("key" => $k, "val" => $v), "gets", "get".$i);
			$i++;
		}
		/* End GET */
		/* Start POST */
		templates::assign_var("count_post", sizeof($_POST));
		$i = 0;
		foreach($_POST as $k => $v) {
			templates::assign_vars(array("key" => $k, "val" => $v), "posts", "posts".$i);
			$i++;
		}
		/* End POST */
		/* Start COOKIE */
		templates::assign_var("count_cookie", sizeof($_COOKIE));
		$i = 0;
		foreach($_COOKIE as $k => $v) {
			templates::assign_vars(array("key" => $k, "val" => $v), "cookies", "cookie".$i);
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
		templates::dir_skins("skins");
		templates::set_skins("");
		$tpl = templates::complited_assing_vars("debug_panel", null);
		return templates::view($tpl);
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
	
	final public static function DebugAll($type = "", $echo = false) {
		if(empty($type)) {
			$type = DEBUG_MEMORY * DEBUG_TIME * DEBUG_FILES * DEBUG_INCLUDE * DEBUG_DB * DEBUG_TEMPLATE;
		}
		$incl_files = $files = $db_querys = $include = array();
		$memory = $memoryNum = $time = $incl_filesize = $filesize = $db_time = $db_num = $tmp = 0;
		$filesizename = array(" Bytes", " Kb", " Mb", " Gb", " Tb", " Pb", " Eb", " Zb", " Yb");
		switch($type) {
			case DEBUG_MEMORY:
				$size = sprintf("%u", memory_get_peak_usage()-MEMORY_GET);
				$memoryNum = $size;
				$memory = $size ? round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i] : '0 Bytes';
				unset($size, $filesizename, $i);
			break;
			case DEBUG_TIME:
			global $Timer;
				$time = microtime()-$Timer;
				unset($Timer);
			break;
			case DEBUG_FILES:
				$tmp_files = debug_backtrace();
				$num = 0;
				for($i=0;$i<sizeof($tmp_files);$i++) {
					if(isset($tmp_files[$i]['file']) && file_exists($tmp_files[$i]['file'])) {
						$files[$num]['file'] = $tmp_files[$i]['file'];
						$files[$num]['lines'] = self::FileLine($tmp_files[$i]['file']);
						$files[$num]['size'] = filesize($tmp_files[$i]['file']);
						$files[$num]['sizeNum'] = filesize($tmp_files[$i]['file']);
						$num++;
					}
				}
				unset($tmp_files, $filesizename, $i);
			break;
			case DEBUG_INCLUDE:
				$num = 0;
				$incl_files = get_included_files();
				foreach($incl_files as $f) {
					if(file_exists($f)) {
						$include[$num]['file'] = $f;
						$include[$num]['lines'] = self::FileLine($f);
						$size = sprintf("%u", filesize($f));
						$include[$num]['sizeNum'] = filesize($f);
						$include[$num]['size'] = ($size ? round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i] : '0 Bytes');
						$num++;
					}
				}
				unset($size, $i);
			break;
			case DEBUG_DB:
				$db_time = db::$time;
				$db_num = db::$num;
				$db_querys = db::$querys;
			break;
			case DEBUG_TEMPLATE:
				$tmp = templates::$time;
			break;
			case DEBUG_MEMORY * DEBUG_TIME * DEBUG_FILES * DEBUG_INCLUDE:
			case DEBUG_CORE:
				$num = 0;
				$incl_files = get_included_files();
				foreach($incl_files as $f) {
					if(file_exists($f)) {
						$include[$num]['file'] = $f;
						$include[$num]['lines'] = self::FileLine($f);
						$size = sprintf("%u", filesize($f));
						$include[$num]['sizeNum'] = filesize($f);
						$include[$num]['size'] = ($size ? round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i] : '0 Bytes');
						$num++;
					}
				}
				unset($size, $i);
				$tmp_files = debug_backtrace();
				$num = 0;
				for($i=0;$i<sizeof($tmp_files);$i++) {
					if(isset($tmp_files[$i]['file']) && file_exists($tmp_files[$i]['file'])) {
						$files[$num]['file'] = $tmp_files[$i]['file'];
						$files[$num]['lines'] = self::FileLine($tmp_files[$i]['file']);
						$files[$num]['size'] = filesize($tmp_files[$i]['file']);
						$files[$num]['sizeNum'] = filesize($tmp_files[$i]['file']);
						$num++;
					}
				}
				unset($tmp_files, $i);
				$size = sprintf("%u", memory_get_peak_usage()-MEMORY_GET);
				$memoryNum = $size;
				$memory = $size ? round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i] : '0 Bytes';
				unset($size, $filesizename, $i);
				global $Timer;
				$time = microtime()-$Timer;
				unset($Timer);
			break;
			case DEBUG_FILES * DEBUG_INCLUDE:
			case DEBUG_FILE:
				$num = 0;
				$incl_files = get_included_files();
				foreach($incl_files as $f) {
					if(file_exists($f)) {
						$include[$num]['file'] = $f;
						$include[$num]['lines'] = self::FileLine($f);
						$size = sprintf("%u", filesize($f));
						$include[$num]['sizeNum'] = filesize($f);
						$include[$num]['size'] = ($size ? round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i] : '0 Bytes');
						$num++;
					}
				}
				unset($size, $i);
				$tmp_files = debug_backtrace();
				$num = 0;
				for($i=0;$i<sizeof($tmp_files);$i++) {
					if(isset($tmp_files[$i]['file']) && file_exists($tmp_files[$i]['file'])) {
						$files[$num]['file'] = $tmp_files[$i]['file'];
						$files[$num]['lines'] = self::FileLine($tmp_files[$i]['file']);
						$files[$num]['sizeNum'] = filesize($tmp_files[$i]['file']);
						$files[$num]['size'] = filesize($tmp_files[$i]['file']);
						$num++;
					}
				}
				unset($tmp_files, $filesizename, $i);
			break;
			case DEBUG_DB * DEBUG_TEMPLATE:
			case DEBUG_DBTEMP:
				$db_time = db::$time;
				$db_num = db::$num;
				$db_querys = db::$querys;
				$tmp = templates::$time;
			break;
			case DEBUG_ALL:
			default:
				$tmp = templates::$time;
				$db_time = db::$time;
				$db_num = db::$num;
				$db_querys = db::$querys;
				$num = 0;
				$incl_files = get_included_files();
				foreach($incl_files as $f) {
					if(file_exists($f)) {
						$include[$num]['file'] = $f;
						$include[$num]['lines'] = self::FileLine($f);
						$size = sprintf("%u", filesize($f));
						$include[$num]['sizeNum'] = filesize($f);
						$include[$num]['size'] = ($size ? round($size / pow(1024, ($isize = floor(log($size, 1024)))), 2) . $filesizename[$isize] : '0 Bytes');
						$num++;
					}
				}
				unset($size, $isize);
				$tmp_files = debug_backtrace();
				$num = 0;
				for($i=0;$i<sizeof($tmp_files);$i++) {
					if(isset($tmp_files[$i]['file']) && file_exists($tmp_files[$i]['file'])) {
						$files[$num]['file'] = $tmp_files[$i]['file'];
						$files[$num]['lines'] = self::FileLine($tmp_files[$i]['file']);
						$files[$num]['sizeNum'] = filesize($tmp_files[$i]['file']);
						$size = sprintf("%u", filesize($tmp_files[$i]['file']));
						$files[$num]['size'] = ($size ? round($size / pow(1024, ($isize = floor(log($size, 1024)))), 2) . $filesizename[$isize] : '0 Bytes');
						$num++;
					}
				}
				unset($tmp_files, $isize);
				$size = sprintf("%u", memory_get_peak_usage()-MEMORY_GET);
				$memoryNum = $size;
				$memory = $size ? round($size / pow(1024, ($isize = floor(log($size, 1024)))), 2) . $filesizename[$isize] : '0 Bytes';
				unset($size, $filesizename, $isize);
				global $Timer;
				$time = microtime()-$Timer;
				unset($Timer);
			break;
		}
		$arr = array("memory" => $memory, "memoryNum" => $memoryNum, "time_work" => $time, "included_files" => $include, "use_files" => $files, "work_template" => $tmp, "db" => array("time" => $db_time, "num" => $db_num, "list" => $db_querys));
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