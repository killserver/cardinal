<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

function CheckCanGzip() {
	return BrowserSupport::gzip();
}

// ToDo: Языковую панель на это дело надо вешать!
function GzipOut() {
global $config, $manifest, $session;
	$time = microtime_float();
	$Timers = $time-SYSTEM_TIME_START_FLOAT;
	$debug = templates::$gzip;
	$exit = templates::$gzipActive;
	if($exit) {
		if(isset($manifest['session_destroy']) && is_bool($manifest['session_destroy']) && $manifest['session_destroy']===true && is_bool($session)) {
			session_destroy();
		}
		return;
	}
	$s = "";
	$tmp = round(templates::$time, 5);
	$dbs = round(db::$time, 5);
	$Timers += $tmp;
	$Timers += $dbs;
	$cli = defined("IS_CLI");
	if($debug) {
		$s = "\n".($cli===false ? "<!-- " : "")."Время выполнения скрипта ".($Timers>0 ? $Timers : 0)." секунд".($cli===false ? " -->" : "")."\n".
		"".($cli===false ? "<!-- " : "")."Время затраченное на компиляцию шаблонов ".($tmp>0 ? $tmp : 0)." секунд".($cli===false ? " -->" : "")."\n".
		"".($cli===false ? "<!-- " : "")."Время затраченное на выполнение MySQL запросов: ".($dbs>0 ? $dbs : 0)." секунд".($cli===false ? " -->" : "")."\n".
		"".($cli===false ? "<!-- " : "")."Общее количество MySQL запросов ".db::$num."".($cli===false ? " -->" : "")."";
	}
	if($debug AND function_exists("memory_get_peak_usage")) {
		$s .="\n".($cli===false ? "<!-- " : "")."Затрачено оперативной памяти ".round((memory_get_peak_usage()-MEMORY_GET)/(1024*1024),2)." MB".($cli===false ? " -->" : "")."";
	}
	header("Last-Modified: " . date('r', time()) ." GMT");
	if(isset($_SERVER['HTTP_REFERER']) && !preg_match('%^(http:|https:)?//(www.)?(webvisor.com)%', $_SERVER['HTTP_REFERER'])) {
		@header("X-Frame-Options: SAMEORIGIN");
	}
	if((function_exists("ob_get_length") && ob_get_length()>0) && (config::Select('gzip') != "yes" || isset($manifest['gzip']) && !$manifest['gzip'])) {
		if($debug) {
			echo $s;
		}
		ob_end_flush();
		return;
	}
	$ENCODING = BrowserSupport::gzip();
	if($ENCODING) {
		$s .= "\n".($cli===false ? "<!-- " : "")."Для вывода использовалось сжатие ".$ENCODING."".($cli===false ? " -->" : "")."\n"; 
		$Contents = ob_get_clean(); 
		if($debug) {
			$s .= "".($cli===false ? "<!-- " : "")."Общий размер файла: ".strlen($Contents)." байт ".
				"После сжатия: ".strlen(gzencode($Contents, 9, FORCE_GZIP))." байт".($cli===false ? " -->" : "").""; 
			$Contents .= $s; 
		}
		header("Content-Encoding: ".$ENCODING); 
		$Contents = gzencode($Contents, 9, FORCE_GZIP);
		echo $Contents;
		if((function_exists("ob_get_length") && ob_get_length()>0) && (isset($config["activeCache"]) && !$config["activeCache"])) {
			ob_end_flush();
		}
		if(isset($manifest['session_destroy']) && is_bool($manifest['session_destroy']) && $manifest['session_destroy']===true && is_bool($session)) {
			session_destroy();
		}
        die();
	} else {
		if(isset($config["activeCache"]) && !$config["activeCache"]) {
			ob_end_flush();
		}
		if(isset($manifest['session_destroy']) && is_bool($manifest['session_destroy']) && $manifest['session_destroy']===true && is_bool($session)) {
			session_destroy();
		}
		exit();
	}
}
?>