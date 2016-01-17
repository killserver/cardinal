<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

function CheckCanGzip() {
	if(headers_sent() || connection_aborted() || !function_exists('ob_gzhandler') || ini_get('zlib.output_compression')) { 
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


function GzipOut($debug = false, $exit = false) {
global $config, $Timer, $manifest;
	if($exit) {
		session_destroy();
		return;
	}
	$s = "";
	if($debug) {
		$s = "\n<!-- Время выполнения скрипта ".$Timer." секунд -->\n".
		"<!-- Время затраченное на компиляцию шаблонов ".round(templates::$time, 5)." секунд -->\n".
		"<!-- Время затраченное на выполнение MySQL запросов: ".round(db::$time, 5)." секунд -->\n".
		"<!-- Общее количество MySQL запросов ".db::$num." -->";
	}

	if($debug AND function_exists( "memory_get_peak_usage")) {
		$s .="\n<!-- Затрачено оперативной памяти ".round((memory_get_peak_usage()-MEMORY_GET)/(1024*1024),2)." MB -->";
	}

	//@header("Last-Modified: " . date('r', time()) ." GMT");

	if($config['gzip'] != "yes" && $manifest['gzip'] != true) {
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
		ob_end_flush();
		session_destroy();
        die();
	} else {
		ob_end_flush();
		session_destroy();
		exit();
	}
}
?>