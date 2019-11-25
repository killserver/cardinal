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
	private static $_isCli = false;

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
		return Debug::TplDebug($arr);
	}
	
	final public static function Debug($type = "", $echo = false) {
		return Debug::DebugAll($type, $echo);
	}
	
	final public static function SetEcho($echo = false) {
		self::$_echo = $echo;
	}
	
	final public static function FriendlyErrorType($type) {
		if($type==0) { // 0 // 
			return (self::$_isCli ? "\e[0;41m" : '').'E_CORE'.(self::$_isCli ? "\e[0m": '');
		} else if($type==E_ERROR) { // 1 // 
			return (self::$_isCli ? "\e[0;41m" : '').'E_ERROR'.(self::$_isCli ? "\e[0m": ''); 
		} else if($type==E_WARNING) { // 2 // 
			return (self::$_isCli ? "\e[0;33m" : '').'E_WARNING'.(self::$_isCli ? "\e[0m": ''); 
		} else if($type==E_PARSE) { // 4 // 
			return (self::$_isCli ? "\e[0;33m" : '').'E_PARSE'.(self::$_isCli ? "\e[0m": ''); 
		} else if($type==E_NOTICE) { // 8 // 
			return (self::$_isCli ? "\e[0;35m" : '').'E_NOTICE'.(self::$_isCli ? "\e[0m": ''); 
		} else if($type==E_CORE_ERROR) { // 16 // 
			return (self::$_isCli ? "\e[0;41m" : '').'E_CORE_ERROR'.(self::$_isCli ? "\e[0m": ''); 
		} else if($type==E_CORE_WARNING) { // 32 // 
			return (self::$_isCli ? "\e[0;33m" : '').'E_CORE_WARNING'.(self::$_isCli ? "\e[0m": ''); 
		} else if($type==E_COMPILE_ERROR) { // 64 // 
			return (self::$_isCli ? "\e[0;41m" : '').'E_COMPILE_ERROR'.(self::$_isCli ? "\e[0m": ''); 
		} else if($type==E_COMPILE_WARNING) { // 128 // 
			return (self::$_isCli ? "\e[0;33m" : '').'E_COMPILE_WARNING'.(self::$_isCli ? "\e[0m": ''); 
		} else if($type==E_USER_ERROR) { // 256 // 
			return (self::$_isCli ? "\e[0;41m" : '').'E_USER_ERROR'.(self::$_isCli ? "\e[0m": ''); 
		} else if($type==E_USER_WARNING) { // 512 // 
			return (self::$_isCli ? "\e[0;33m" : '').'E_USER_WARNING'.(self::$_isCli ? "\e[0m": ''); 
		} else if($type==E_USER_NOTICE) { // 1024 // 
			return (self::$_isCli ? "\e[0;36m" : '').'E_USER_NOTICE'.(self::$_isCli ? "\e[0m": ''); 
		} else if($type==E_STRICT) { // 2048 // 
			return (self::$_isCli ? "\e[0;34m" : '').'E_STRICT'.(self::$_isCli ? "\e[0m": ''); 
		} else if($type==E_RECOVERABLE_ERROR) { // 4096 // 
			return (self::$_isCli ? "\e[0;32m" : '').'E_RECOVERABLE_ERROR'.(self::$_isCli ? "\e[0m": ''); 
		} else if($type==E_DEPRECATED) { // 8192 // 
			return (self::$_isCli ? "\e[0;35m" : '').'E_DEPRECATED'.(self::$_isCli ? "\e[0m" : '');
		} else if($type==E_USER_DEPRECATED) { // 16384 //
			return (self::$_isCli ? "\e[0;35m" : '').'E_USER_DEPRECATED'.(self::$_isCli ? "\e[0m" : '');
		} else {
			return (self::$_isCli ? "\e[0;41m" : '').$type.(self::$_isCli ? "\e[0m" : '');
		}
	}
	
	final private static function viewOnPage($data) {
		errorHeader();
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

	final public static function is_cli() {
		if(defined('STDIN')) {
			return true;
		}
		if(empty($_SERVER['REMOTE_ADDR']) && !isset($_SERVER['HTTP_USER_AGENT']) && sizeof($_SERVER['argv'])>0) {
			return true;
		}
		if(!array_key_exists('REQUEST_METHOD', $_SERVER)) {
			return true;
		}
		return false;
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
			if(config::Select('logs')==ERROR_FILE && is_writable(PATH_LOGS)) {
				file_put_contents(PATH_LOGS."php_log.txt", json_encode(array("times" => time(), "ip" => self::getip(), "exception_type" => self::FriendlyErrorType($e->getCode()), "message" => self::saves($messagePrefix . $e->getMessage()), "filename" => self::saves($file), "line" => $e->getLine(), "trace_string" => self::saves(self::getExceptionTraceAsString($e)), "request_state" => self::saves(serialize($request), true)))."\n", FILE_APPEND);
			}
			if(self::$_echo) {
				self::$_isCli = self::is_cli();
				if(self::$_isCli) {
					echo "[" . self::FriendlyErrorType($e->getCode()) . "] " . $e->getMessage() . " - " . self::saves($file) . " (" . $e->getLine() . ")\n[" . self::FriendlyErrorType($e->getCode()) . "]\n" . self::getExceptionTraceAsString($e);
					echo "\n";
					die();
				}
				if(file_exists(PATH_SKINS."phpError.tpl")) {
					$file = file_get_contents(PATH_SKINS."phpError.tpl");
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
							nl2br(self::saves(self::getExceptionTraceAsString($e)))
					), $file);
					self::viewOnPage($file);
				} else if(file_exists(PATH_SKINS."core".DS."503.tpl")) {
					$tpl = file_get_contents(PATH_SKINS."core".DS."503.tpl");
					$show = "\n";
					$show .= "<span class=\"info\"><b>[" . self::FriendlyErrorType($e->getCode()) . "]</b> " . $e->getMessage() . " - " . self::saves($file) . " (" . $e->getLine() . ")</span>";
					$error = str_replace(ROOT_PATH, DS, nl2br(self::saves(self::getExceptionTraceAsString($e))));
					$show .= "\n".$error;
					$tpl = str_replace("{error}", $show, $tpl);
					self::viewOnPage($tpl);
				} else {
					self::viewOnPage("<div style=\"text-decoration:underline;\"><div style=\"padding-top: 10px; text-transform: uppercase;\">[" . self::FriendlyErrorType($e->getCode()) . "] " . $e->getMessage() . " - " . self::saves($file) . " (" . $e->getLine() . ")</div><br />\n<b>[" . self::FriendlyErrorType($e->getCode()) . "]</b></div><br />\n<span style=\"border:0.1em dotted black;padding:0.5em;display:block;\">" . nl2br(self::saves(self::getExceptionTraceAsString($e))) . "</span></div>");
				}
			}
			die();
		}
		catch (Exception $e) {}
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
		if(!self::$_handlePhpError) {
			return false;
		}
		if($errorType & error_reporting()) {
			$trigger = true;
			if(!self::debugMode()) {
				if((defined('E_DEPRECATED') && $errorType & E_DEPRECATED) || (defined('E_USER_DEPRECATED') && $errorType & E_USER_DEPRECATED)) {
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
			if($trigger) {
				$e = new ErrorException($errorString, 0, $errorType, $file, $line);
				self::logException($e, false);
				return true;
			}
		}
		return false;
	}

}

?>