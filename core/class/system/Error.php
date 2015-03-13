<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

final class Error {

	protected static $_handlePhpError = true;
	protected static $_debug = false;
	public static $_echo = true;

	function Error() {
		
	}
	
	public static function SetEcho() {
		self::$_echo = false;
	}
	
	public static function FriendlyErrorType($type) {
		if($type==E_ERROR) { // 1 // 
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
	
	public static function handleException(Exception $e) {
		self::logException($e);
	}
	
	public static function handleFatalError() {
		$error = @error_get_last();
		if (!$error) {
			return;
		}
		if (empty($error['type']) || !($error['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR))) {
			return;
		}
		try {
			$e = new ErrorException("Fatal Error: " . $error['message'], $error['type'], 1, $error['file'], $error['line']);
			self::logException($e);
		}
		catch(Exception $e) {}
	}
	
	public static function logException(Exception $e, $rollbackTransactions = true, $messagePrefix = '') {
		try {
			$file = str_replace(ROOT_PATH, "", $e->getFile());
			$request = array(
				'url' => "http://".getenv('SERVER_NAME').getenv('REQUEST_URI'),
				'_GET' => $_GET,
				'_POST' => $_POST
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

			if(modules::get_config('logs')=="file") {
				file_put_contents(ROOT_PATH."core/cache/system/php_log.txt", json_encode(array("times" => time(), "ip" => HTTP::getip(), "exception_type" => self::FriendlyErrorType($e->getCode()), "message" => self::saves($messagePrefix . $e->getMessage()), "filename" => self::saves($file), "line" => $e->getLine(), "trace_string" => self::saves($e->getTraceAsString()), "request_state" => self::saves(serialize($request), true)))."\n", FILE_APPEND);
			} else {
				$db = modules::init_db();
				$db->doquery("INSERT INTO `error_log`(`times`, `ip`, `exception_type`, `message`, `filename`, `line`, `trace_string`, `request_state`) VALUES(UNIX_TIMESTAMP(), \"".HTTP::getip()."\", \"".self::FriendlyErrorType($e->getCode())."\", \"".self::saves($messagePrefix . $e->getMessage())."\", \"".self::saves($file)."\", \"".$e->getLine()."\", \"".self::saves($e->getTraceAsString())."\", \"".self::saves(serialize($request), true)."\")");
			}
			if(self::$_echo) {
				echo "<div style=\"text-decoration:underline;\"><b>[".self::FriendlyErrorType($e->getCode())."]</b></div><br />\n<span style=\"border: 2px dotted black;\">".nl2br(self::saves($e->getTraceAsString()))."</span><br />\n<div style=\"padding-top: 10px;text-transform: uppercase;\">".self::saves($file)." (".$e->getLine().")</div>";
			}
		}
		catch (Exception $e) {}
	}
	
	private static function saves($data, $save=false) {
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
	
	public static function debugMode() {
		return self::$_debug;
	}
	
	public static function handlePhpError($errorType = null, $errorString = null, $file = null, $line = null) {
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
				} else if($errorType & E_NOTICE || $errorType & E_USER_NOTICE || $errorType & E_STRICT) {
					$trigger = false;
					$e = new ErrorException($errorString, 0, $errorType, $file, $line);
					self::logException($e, false);
				}
			}
			if ($trigger) {
				throw new ErrorException($errorString, 0, $errorType, $file, $line);
			}
		}
	}

}

?>