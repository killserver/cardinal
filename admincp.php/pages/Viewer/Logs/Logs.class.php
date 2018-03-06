<?php
/*
*
* Version Engine: 1.25.5b1
* Version File: 3
*
* 3.1
* fix admin templates
* 3.2
* fix view errors
*
*/
class Logs extends Core {
	
	function Delete() {
		cardinal::RegAction("Очистка логов ошибок");
		if(file_exists(PATH_CACHE_SYSTEM."php_log.txt")) {
			unlink(PATH_CACHE_SYSTEM."php_log.txt");
		}
		if(!defined("WITHOUT_DB") && db::connected()) {
			$list = db::doquery("SELECT `id` FROM {{error_log}}", true);
			while($l = db::fetch_assoc($list)) {
				db::doquery("DELETE FROM {{error_log}} WHERE id = ".$l['id']);
			}
		}
	}

	function __construct() {
		if(isset($_GET['delete'])) {
			$this->Delete();
			cardinal::RegAction("Очистка логов ошибок на сервере");
			location("./?pages=Logs");
			die();
		}
		if((defined("WITHOUT_DB") || config::Select('logs')==ERROR_FILE) && file_exists(PATH_CACHE_SYSTEM."php_log.txt")) {
			$logs = file(PATH_CACHE_SYSTEM."php_log.txt");
			$log_el = array();
			for($i=(sizeof($logs)-1);$i>=0;$i--) {
				$log = json_decode(trim($logs[$i]));
				$at = unserialize(str_replace('\\"', "\"", htmlspecialchars_decode($log->request_state)));
				templates::assign_vars(array(
					"time" => date("d-m-Y H:i:s", $log->times),
					"errorno" => cardinalError::FriendlyErrorType($log->exception_type),
					"error" => htmlspecialchars($log->message),
					"path" => htmlspecialchars($log->filename),
					"line" => $log->line,
					"ip" => $log->ip,
					"descr" => nl2br($log->trace_string."\n".var_export($at, true)),
				), "logs", $log->filename.$log->line);
			}
		} elseif(!defined("WITHOUT_DB") && db::connected()) {
			db::doquery("SELECT * FROM {{error_log}} ORDER BY `id` DESC", true);
			while($log = db::fetch_assoc()) {
				templates::assign_vars(array(
					"time" => date("d-m-Y H:i:s", $log['times']),
					"errorno" => cardinalError::FriendlyErrorType($log['exception_type']),
					"error" => htmlspecialchars($log['message']),
					"path" => htmlspecialchars($log['filename']),
					"line" => $log['line'],
					"ip" => $log['ip'],
					"descr" => nl2br($log['trace_string']."\n"),
				), "logs", $log['filename'].$log['line']);
			}
		}
		$this->ParseLang();
		$tmp = templates::completed_assign_vars("errors", null);
		templates::clean();
		$this->Prints($tmp, true);
	}

}

?>