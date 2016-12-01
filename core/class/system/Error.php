<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

class Error {

	protected static $_handlePhpError = true;
	protected static $_debug = false;
	private static $_debugHandler = false;
	public static $_echo = true;

	final function Error() {
		
	}
	
	final public static function DebugHandler($func = "") {
		
	}
	
	final public static function Log($log) {
		if(!self::$_debugHandler) {
			return false;
		}
		return call_user_func_array(self::$_debugHandler, array($log));
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
	
	final public static function Debug($type = "", $echo = false) {
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
						$size = sprintf("%u", filesize($tmp_files[$i]['file']));
						$files[$num]['size'] = ($size ? round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i] : '0 Bytes');
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
	
	final private static function FileLine($file) {
		$lines = 0;
		$fh = fopen($file, "r");
		while(fgets($fh) !== false) {
			$lines++;
		}
		fclose($fh);
		return $lines;
	}
	
	final public static function SetEcho() {
		self::$_echo = false;
	}
	
	final public static function FriendlyErrorType($type) {
		if($type==0) { // 0 // 
			return 'E_CORE'; 
		} else if($type==E_ERROR) { // 1 // 
			return 'E_ERROR'; 
		} else if($type==E_WARNING) { // 2 // 
			return 'E_WARNING'; 
		} else if($type==E_PARSE) { // 4 // 
			return 'E_PARSE'; 
		} else if($type==E_NOTICE) { // 8 // 
			return 'E_NOTICE'; 
		} else if($type==E_CORE_ERROR) { // 16 // 
			return 'E_CORE_ERROR'; 
		} else if($type==E_CORE_WARNING) { // 32 // 
			return 'E_CORE_WARNING'; 
		} else if($type==E_COMPILE_ERROR) { // 64 // 
			return 'E_COMPILE_ERROR'; 
		} else if($type==E_COMPILE_WARNING) { // 128 // 
			return 'E_COMPILE_WARNING'; 
		} else if($type==E_USER_ERROR) { // 256 // 
			return 'E_USER_ERROR'; 
		} else if($type==E_USER_WARNING) { // 512 // 
			return 'E_USER_WARNING'; 
		} else if($type==E_USER_NOTICE) { // 1024 // 
			return 'E_USER_NOTICE'; 
		} else if($type==E_STRICT) { // 2048 // 
			return 'E_STRICT'; 
		} else if($type==E_RECOVERABLE_ERROR) { // 4096 // 
			return 'E_RECOVERABLE_ERROR'; 
		} else if($type==E_DEPRECATED) { // 8192 // 
			return 'E_DEPRECATED';
		} else if($type==E_USER_DEPRECATED) { // 16384 //
			return 'E_USER_DEPRECATED';
		} else {
			return $type;
		}
	}
	
	final private static function viewOnPage($data) {
		echo $data;
	}
	
	final public static function handleException(Exception $e) {
		self::logException($e);
	}
	
	final public static function handleFatalError() {
		$error = @error_get_last();
		if(!$error) {
			return false;
		}
		if(empty($error['type']) || !($error['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR))) {
			return false;
		}
		try {
			$e = new ErrorException("Fatal Error: " . $error['message'], $error['type'], 1, $error['file'], $error['line']);
			self::logException($e);
			return true;
		}
		catch(Exception $e) {return false;}
	}
	
	final public static function logException(Exception $e, $rollbackTransactions = true, $messagePrefix = '') {
		try {
			$file = str_replace(ROOT_PATH, "", $e->getFile());
			$request = array(
				'url' => "http://".getenv('SERVER_NAME').getenv('REQUEST_URI'),
				'_GET' => $_GET,
				'_POST' => $_POST,
				'_SERVER' => array("HTTP_REFERER" => getenv("HTTP_REFERER"), "HTTP_USER_AGENT" => getenv("HTTP_USER_AGENT")),
			);
/*
CREATE TABLE `error_log` (
`id` int(11) auto_increment not null,
`times` int(11) not null,
`ip` varchar(255) not null,
`exception_type` varchar(255) not null,
`message` varchar(255) not null,
`filename` varchar(255) not null,
`line` int(11) not null,
`trace_string` longtext not null,
`request_state` longtext not null,
primary key `id`(`id`)
) ENGINE=MyISAM;
*/

			if(defined("WITHOUT_DB") || config::Select('logs')==ERROR_FILE) {
				if(is_writable(ROOT_PATH."core".DS."cache".DS."system".DS."php_log.txt"))
				file_put_contents(ROOT_PATH."core".DS."cache".DS."system".DS."php_log.txt", json_encode(array("times" => time(), "ip" => HTTP::getip(), "exception_type" => self::FriendlyErrorType($e->getCode()), "message" => self::saves($messagePrefix . $e->getMessage()), "filename" => self::saves($file), "line" => $e->getLine(), "trace_string" => self::saves($e->getTraceAsString()), "request_state" => self::saves(serialize($request), true)))."\n", FILE_APPEND);
			} else {
				$db = modules::init_db();
				$db->doquery("INSERT INTO `error_log`(`times`, `ip`, `exception_type`, `message`, `filename`, `line`, `trace_string`, `request_state`) VALUES(UNIX_TIMESTAMP(), \"".HTTP::getip()."\", \"".self::FriendlyErrorType($e->getCode())."\", \"".self::saves($messagePrefix . $e->getMessage())."\", \"".self::saves($file)."\", \"".$e->getLine()."\", \"".self::saves($e->getTraceAsString())."\", \"".self::saves(serialize($request), true)."\")");
			}
			if(self::$_echo) {
				if(file_exists(ROOT_PATH."skins".DS."phpError.tpl")) {
					$file = file_get_contents(ROOT_PATH."skins".DS."phpError.tpl");
					$file = str_replace(array(
							"{code}",
							"{message}",
							"{file}",
							"{line}",
							"{trace}"
					), array(
							self::FriendlyErrorType($e->getCode()),
							$e->getMessage(),
							self::saves($file),
							$e->getLine(),
							nl2br(self::saves($e->getTraceAsString()))
					), $file);
					self::viewOnPage($file);
				} else {
					self::viewOnPage("<div style=\"text-decoration:underline;\"><div style=\"padding-top: 10px;text-transform: uppercase;\">[" . self::FriendlyErrorType($e->getCode()) . "] " . $e->getMessage() . " - " . self::saves($file) . " (" . $e->getLine() . ")</div><br />\n<b>[" . self::FriendlyErrorType($e->getCode()) . "]</b></div><br />\n<span style=\"border: 2px dotted black;\">" . nl2br(self::saves($e->getTraceAsString())) . "</span></div>");
				}
			}
		}
		catch (Exception $e) {}
	}
	
	final private static function saves($data, $save = false) {
		if(is_string($data)) {
			if($save) {
				$data = str_replace("\"", '\"', $data);
			}
			return htmlspecialchars($data);
		} else {
			$datas = (string) $data;
			if($save) {
				$datas = str_replace("\"", '\"', $datas);
			}
			return htmlspecialchars($datas);
		}
	}
	
	final public static function debugMode() {
		return self::$_debug;
	}
	
	final public static function handlePhpError($errorType = "", $errorString = "", $file = "", $line = "") {
		if (!self::$_handlePhpError) {
			return false;
		}
		if ($errorType & error_reporting()) {
			$trigger = true;
			if (!self::debugMode()) {
				if ((defined('E_DEPRECATED') && $errorType & E_DEPRECATED) || (defined('E_USER_DEPRECATED') && $errorType & E_USER_DEPRECATED)) {
					$trigger = false;
					$e = new ErrorException($errorString, 0, $errorType, $file, $line);
					self::logException($e, false);
					return true;
				} else if($errorType & E_NOTICE || $errorType & E_USER_NOTICE || $errorType & E_STRICT) {
					$trigger = false;
					$e = new ErrorException($errorString, 0, $errorType, $file, $line);
					self::logException($e, false);
					return true;
				}
			}
			if ($trigger) {
				throw new ErrorException($errorString, 0, $errorType, $file, $line);
			}
		}
		return false;
	}

}

?>