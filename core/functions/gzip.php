<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

function CheckCanGzip() {
	if((function_exists("headers_sent") && headers_sent()) || (function_exists("connection_aborted") && connection_aborted()) || !function_exists('ob_gzhandler') || ini_get('zlib.output_compression') || !isset($_SERVER['HTTP_ACCEPT_ENCODING'])) { 
		return false;
	}
	if(strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false) {
		return "x-gzip";
	}
	if(strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
		return "gzip";
	}
	return false; 
}

// ToDo: Языковую панель на это дело надо вешать!
function GzipOut($debug = false, $exit = false) {
global $config, $Timer, $manifest;
	if($exit) {
		session_destroy();
		return;
	}
	$s = "";
	$tmp = round(templates::$time, 5);
	$dbs = round(db::$time, 5);
	if($debug) {
		$s = "\n<!-- Время выполнения скрипта ".($Timer>0 ? $Timer : 0)." секунд -->\n".
		"<!-- Время затраченное на компиляцию шаблонов ".($tmp>0 ? $tmp : 0)." секунд -->\n".
		"<!-- Время затраченное на выполнение MySQL запросов: ".($dbs>0 ? $dbs : 0)." секунд -->\n".
		"<!-- Общее количество MySQL запросов ".db::$num." -->";
	}

	if($debug AND function_exists("memory_get_peak_usage")) {
		$s .="\n<!-- Затрачено оперативной памяти ".round((memory_get_peak_usage()-MEMORY_GET)/(1024*1024),2)." MB -->";
	}

	//@header("Last-Modified: " . date('r', time()) ." GMT");

	if(isset($config['gzip']) && $config['gzip'] != "yes" && isset($manifest['gzip']) && $manifest['gzip'] != true) {
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
		if(isset($config["activeCache"]) && !$config["activeCache"]) {
			ob_end_flush();
		}
		session_destroy();
        die();
	} else {
		if(isset($config["activeCache"]) && !$config["activeCache"]) {
			ob_end_flush();
		}
		session_destroy();
		exit();
	}
}
?>