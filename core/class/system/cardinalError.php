<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

class cardinalError {

	protected static $_handlePhpError = true;
	protected static $_debug = false;
	private static $_debugHandler = false;
	public static $_echo = true;

	final function __construct() {
		
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
		return Debug::TplDebug($type, $echo);
	}
	
	final public static function Debug($type = "", $echo = false) {
		return Debug::DebugAll($type, $echo);
	}
	
	final private static function FileLine($file) {
		return Debug::FileLine($file);
	}
	
	final public static function SetEcho($echo = false) {
		self::$_echo = $echo;
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
		return Debug::viewOnPage($data);
	}
	
	final public static function handleException($e) {
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
	
	final public static function logException($e, $rollbackTransactions = true, $messagePrefix = '') {
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
			$db = false;
			if(defined("WITHOUT_DB") || config::Select('logs')==ERROR_FILE) {
				if(is_writable(ROOT_PATH."core".DS."cache".DS."system".DS)) {
					file_put_contents(ROOT_PATH."core".DS."cache".DS."system".DS."php_log.txt", json_encode(array("times" => time(), "ip" => self::getip(), "exception_type" => self::FriendlyErrorType($e->getCode()), "message" => self::saves($messagePrefix . $e->getMessage()), "filename" => self::saves($file), "line" => $e->getLine(), "trace_string" => self::saves($e->getTraceAsString()), "request_state" => self::saves(serialize($request), true)))."\n", FILE_APPEND);
				}
			} else {
				$db = modules::init_db();
				$db->doquery("INSERT INTO `error_log`(`times`, `ip`, `exception_type`, `message`, `filename`, `line`, `trace_string`, `request_state`) VALUES(UNIX_TIMESTAMP(), \"".self::getip()."\", \"".self::FriendlyErrorType($e->getCode())."\", \"".self::saves($messagePrefix . $e->getMessage())."\", \"".self::saves($file)."\", \"".$e->getLine()."\", \"".self::saves($e->getTraceAsString())."\", \"".self::saves(serialize($request), true)."\")");
			}
			if(self::$_echo) {
				if(!defined("ERROR_VIEW")) {
					self::viewOnPage("<div style=\"text-decoration:underline;\"><div style=\"padding-top: 10px;text-transform: uppercase;\"><h1>Error!</h1> <b>[" . self::FriendlyErrorType($e->getCode()) . "]</b> level error. Error code <h2 style=\"display:inline-block;\">(" . self::NextId($db) . ")</h2>. Please, report developer</div></div>");
				} else {
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
						self::viewOnPage("<div style=\"text-decoration:underline;\"><div style=\"padding-top: 10px;text-transform: uppercase;\">[" . self::FriendlyErrorType($e->getCode()) . "] " . $e->getMessage() . " - " . self::saves($file) . " (" . $e->getLine() . ")</div><br />\n<b>[" . self::FriendlyErrorType($e->getCode()) . "]</b></div><br />\n<span style=\"border: 2px dotted black;\">" . nl2br(self::saves(self::getExceptionTraceAsString($e))) . "</span></div>");
					}
				}
			}
			die();
		}
		catch (Exception $e) {}
	}
	
	final private static function NextId($db = false) {
		if(defined("WITHOUT_DB") || config::Select('logs')==ERROR_FILE) {
			if(!file_exists(ROOT_PATH."core".DS."cache".DS."system".DS."php_log.txt") || !is_readable(ROOT_PATH."core".DS."cache".DS."system".DS."php_log.txt")) {
				return 0;
			}
			$handle = fopen(ROOT_PATH."core".DS."cache".DS."system".DS."php_log.txt", "rb");
			$id = 1;
			while(($c = fgetc($handle))!==false) {
				if($c==="\n") {
					$id++;
				}
			}
			fclose($handle);
			$id = $id-1;
		} else {
			try {
				$id = $db->last_id("error_log");
				$id = $id-1;
			} catch(Exception $ex) {
				$id = 0;
			}
		}
		return $id;
	}
	
	final private static function getExceptionTraceAsString($exception) {
		$rtn = "";
		$count = 0;
		foreach ($exception->getTrace() as $frame) {
			$args = "";
			if(isset($frame['args'])) {
				$args = array();
				foreach($frame['args'] as $arg) {
					if(is_string($arg)) {
						$args[] = "'" . $arg . "'";
					} elseif(is_array($arg)) {
						$args[] = "Array";
					} elseif(is_null($arg)) {
						$args[] = 'NULL';
					} elseif(is_bool($arg)) {
						$args[] = ($arg) ? "true" : "false";
					} elseif(is_object($arg)) {
						$args[] = get_class($arg);
					} elseif(is_resource($arg)) {
						$args[] = get_resource_type($arg);
					} else {
						$args[] = $arg;
					}
				}
				$args = join(", ", $args);
			}
			$rtn .= sprintf("#%s %s(%s): %s%s(%s)\n", $count,
			//~$frame['file'],
			isset($frame['file']) ? $frame['file'] : '',
			//~$frame['line'],
			isset($frame['line']) ? $frame['line'] : '', isset($frame['class']) ? $frame['class'] . '->' : '', $frame['function'], $args );
			$count++;
		}
		return $rtn;
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
	
	final public static function debugMode($debug = "") {
		if($debug!=="") {
			self::$_debug = $debug;
		} else {
			return self::$_debug;
		}
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