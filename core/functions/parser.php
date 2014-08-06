<?php

if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

function parser_url($url, $referer = null, $header=false, $coo=false, $coopath=null, $proxy=null, $error=false, $gzip=false) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; rv:14.0) Gecko/20100101 Firefox/14.0.1");
//Установите эту опцию в ненулевое значение, если вы хотите, чтобы PHP завершал работу скрыто, если возвращаемый HTTP-код имеет значение выше 300. По умолчанию страница возвращается нормально с игнорированием кода.
	//curl_setopt($ch, CURLOPT_FAILONERROR, 1);
	//Устанавливаем значение referer - адрес последней активной страницы
	if(is_bool($coo) && $coo) {
		curl_setopt($ch, CURLOPT_COOKIEJAR, ROOT_PATH."core/cache/parser_video/".$coopath.".txt");
		curl_setopt($ch, CURLOPT_COOKIEFILE, ROOT_PATH."core/cache/parser_video/".$coopath.".txt");
	}
	if(!is_bool($coo) && !empty($coo)) {
		if(is_array($coo)) {
			$nam = array_keys($coo);
			$val = array_values($coo);
			$coo = array();
			for($i=0;$i<sizeof($nam);$i++) {
				$coo .= $nam[$i]."=".$val[$i]."; ";
			}
			unset($nam, $val);
		}
		curl_setopt($ch, CURLOPT_COOKIE, $coo);
	}
	if(!empty($referer)) {
		curl_setopt($ch, CURLOPT_REFERER, $referer);
	} else {
		curl_setopt($ch, CURLOPT_REFERER, $url);
	}
	curl_setopt($ch, CURLOPT_TIMEOUT, 3);
	if(!$header) {
		curl_setopt($ch, CURLOPT_HEADER, 0);
	} else {
		curl_setopt($ch, CURLOPT_HEADER, 1);
	}
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	if($gzip) {
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
	}
	if(!empty($proxy)) {
		curl_setopt($ch, CURLOPT_PROXY, $proxy);
	}
	if(($html = curl_exec($ch)) === false) {
		$html = curl_error($ch);
	}
	$errors = curl_error($ch);
	curl_close($ch);
if($error) {
return array("html" => $html, "error" => $errors);
} else {
return $html;
}
}

function parser_video($content, $start, $end){
	$pos = strpos($content, $start);
	$content = substr($content, $pos);
	$pos = strpos($content, $end);
	$content = substr($content, 0, $pos);
	$content = str_replace($start, "", $content);
return $content;
}

?>