<?php
//[20-02-2015 23:17:09] 2048: Accessing+static+property+templates%3A%3A%24gzip+as+non+static in %2Fhome%2FKiller%2Fweb%2Fonline-killer.pp.ua%2Fpublic_html%2Fcore%2Fmodules%2Frating.class.php on line 53 (193.109.129.144)
class Logs extends Core {
	
	function FriendlyErrorType($type) {
		if($type=="0") { // 8 // 
			return 'E_NOTICE'; 
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
	
	function Delete() {
		if(file_exists(ROOT_PATH."core/cache/system/php_log.txt")) {
			unlink(ROOT_PATH."core/cache/system/php_log.txt");
		}
		$list = db::doquery("SELECT id FROM `error_log`", true);
		while($l = db::fetch_assoc($list)) {
			db::doquery("DELETE FROM `error_log` WHERE id = ".$l['id']);
		}
	}

	function Logs() {
		if(isset($_GET['delete'])) {
			$this->Delete();
			Header("Location: ./?pages=Logs");
			die();
		}
		if(config::Select('logs')=="file" && file_exists(ROOT_PATH."core/cache/system/php_log.txt")) {
			$logs = file(ROOT_PATH."core/cache/system/php_log.txt");
			$log_el = array();
			for($i=0;$i<sizeof($logs);$i++) {
				$log = json_decode(trim($logs[$i]));
				$at = unserialize(str_replace('\\"', "\"", htmlspecialchars_decode($log->request_state)));
				templates::assign_vars(array(
					"time" => date("d-m-Y H:i:s", $log->times),
					"errorno" => $this->FriendlyErrorType($log->exception_type),
					"error" => htmlspecialchars($log->message),
					"path" => htmlspecialchars($log->filename),
					"line" => $log->line,
					"ip" => $log->ip,
					"descr" => nl2br($log->trace_string."\n".var_export($at, true)),
				), "logs", $log->filename.$log->line);
			}
		} else {
			db::doquery("SELECT * FROM `error_log` ORDER BY `id` DESC", true);
			while($log = db::fetch_assoc()) {
				templates::assign_vars(array(
					"time" => date("d-m-Y H:i:s", $log['times']),
					"errorno" => $this->FriendlyErrorType($log['exception_type']),
					"error" => htmlspecialchars($log['message']),
					"path" => htmlspecialchars($log['filename']),
					"line" => $log['line'],
					"ip" => $log['ip'],
					"descr" => nl2br($log['trace_string']."\n"),
				), "logs", $log['filename'].$log['line']);
			}
		}
		$tmp = templates::complited_assing_vars("errors", "admin");
		templates::clean();
		$this->Prints($tmp, true);
	}

}

?>