<?php
/*
*
* Version Engine: 1.25.5b1
* Version File: 3
*
* 3.1
* fix admin templates
*
*/
class Logs extends Core {
	
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
					"errorno" => Error::FriendlyErrorType($log->exception_type),
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
					"errorno" => Error::FriendlyErrorType($log['exception_type']),
					"error" => htmlspecialchars($log['message']),
					"path" => htmlspecialchars($log['filename']),
					"line" => $log['line'],
					"ip" => $log['ip'],
					"descr" => nl2br($log['trace_string']."\n"),
				), "logs", $log['filename'].$log['line']);
			}
		}
		$tmp = templates::complited_assing_vars("errors", null);
		templates::clean();
		$this->Prints($tmp, true);
	}

}

?>