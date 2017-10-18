<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

function CheckCanGzip() {
	if((function_exists("headers_sent") && headers_sent()) || (function_exists("connection_aborted") && connection_aborted()) || !function_exists('ob_gzhandler') || ini_get('zlib.output_compression')!=="" || HTTP::getServer('HTTP_ACCEPT_ENCODING', false)===false) {
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
function GzipOut($debug = false, $exit = false) {
global $config, $Timer, $manifest, $session;
	if($exit) {
		if(isset($manifest['session_destroy']) && is_bool($manifest['session_destroy']) && $manifest['session_destroy']===true && is_bool($session)) {
			session_destroy();
		}
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
	$ENCODING = CheckCanGzip();
	$Contents = ob_get_clean();

	//@header("Last-Modified: " . date('r', time()) ." GMT");
	if(isset($config['gzip']) && $config['gzip'] != "yes" && isset($manifest['gzip']) && $manifest['gzip'] !== true) {
		echo $Contents;
		if($debug && !Validate::json($Contents)) {
			echo $s;
		}
		if(function_exists("ob_get_length") && ob_get_length()>0) {
			ob_end_flush();
		}
		if(isset($manifest['session_destroy']) && is_bool($manifest['session_destroy']) && $manifest['session_destroy']===true && is_bool($session)) {
			session_destroy();
		}
		return;
	}
	if($ENCODING!==false) {
		$s .= "\n<!-- Для вывода использовалось сжатие ".$ENCODING." -->\n";
		header("Content-Encoding: ".$ENCODING); 
		$ContentEn = gzencode($Contents, 9, FORCE_GZIP);
		if($debug && !Validate::json($Contents)) {
			$s .= "<!-- Общий размер файла: ".strlen($Contents)." байт ".
				"После сжатия: ".strlen($ContentEn)." байт -->";
			$ContentEn .= $s; 
		}
		echo $ContentEn;
		if((function_exists("ob_get_length") && ob_get_length()>0) && (isset($config["activeCache"]) && $config["activeCache"]===false)) {
			ob_end_flush();
		}
		if(isset($manifest['session_destroy']) && is_bool($manifest['session_destroy']) && $manifest['session_destroy']===true && is_bool($session)) {
			session_destroy();
		}
        die();
	} else {
		echo $Contents;
		if($debug && !Validate::json($Contents)) {
			echo $s;
		}
		if((function_exists("ob_get_length") && ob_get_length()>0) && (isset($config["activeCache"]) && $config["activeCache"]===false)) {
			ob_end_flush();
		}
		if(isset($manifest['session_destroy']) && is_bool($manifest['session_destroy']) && $manifest['session_destroy']===true && is_bool($session)) {
			session_destroy();
		}
		exit();
	}
}
?>