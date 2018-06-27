<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

function CheckCanGzip() {
	if(!function_exists('ob_gzhandler') || ini_get('zlib.output_compression')!=="" || HTTP::getServer('HTTP_ACCEPT_ENCODING', false)===false) {
		return false;
	}
	if(strpos(HTTP::getServer('HTTP_ACCEPT_ENCODING'), 'x-gzip') !== false) {
		return "x-gzip";
	}
	if(strpos(HTTP::getServer('HTTP_ACCEPT_ENCODING'), 'gzip') !== false) {
		return "gzip";
	}
	return false;
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
	if($debug) {
		$s = "\n<!-- Время выполнения скрипта ".($Timers>0 ? $Timers : 0)." секунд -->\n".
		"<!-- Время затраченное на компиляцию шаблонов ".($tmp>0 ? $tmp : 0)." секунд -->\n".
		"<!-- Время затраченное на выполнение MySQL запросов: ".($dbs>0 ? $dbs : 0)." секунд -->\n".
		"<!-- Общее количество MySQL запросов ".db::$num." -->";
	}
	if($debug AND function_exists("memory_get_peak_usage")) {
		$s .="\n<!-- Затрачено оперативной памяти ".round((memory_get_peak_usage()-MEMORY_GET)/(1024*1024),2)." MB -->";
	}
	header("Last-Modified: " . date('r', time()) ." GMT");
	if(!preg_match('%^(http:|https:)?//(www.)?(webvisor.com)%', $_SERVER['HTTP_REFERER'])) {
		@header("X-Frame-Options: SAMEORIGIN");
	}
	if((function_exists("ob_get_length") && ob_get_length()>0) && (config::Select('gzip') != "yes" || isset($manifest['gzip']) && !$manifest['gzip'])) {
		if($debug) {
			echo $s;
		}
		ob_end_flush();
		return;
	}
	$ENCODING = CheckCanGzip();
	if($ENCODING) {
		$s .= "\n<!-- Для вывода использовалось сжатие ".$ENCODING." -->\n"; 
		$Contents = ob_get_clean(); 
		if($debug) {
			$s .= "<!-- Общий размер файла: ".strlen($Contents)." байт ".
				"После сжатия: ".strlen(gzencode($Contents, 9, FORCE_GZIP))." байт -->"; 
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