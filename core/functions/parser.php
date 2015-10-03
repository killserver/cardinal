<?php
/*
*
* Version Engine: 1.25.3
* Version File: 2
*
* 2.0
* redirect video function parser_host in function parsers
* 2.1
* fix added start on numbers
*
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

//ToDo: а нафиг мне тогда эта функция, если есть класс Parser?!
function parser_url($url, $referer = null, $header=false, $coo=false, $coopath=null, $proxy=null, $error=false, $gzip=false, $uagent=null, $timeout=3) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	if(!empty($uagent)) {
		curl_setopt($ch, CURLOPT_USERAGENT, $uagent);
	} else {
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; rv:14.0) Gecko/20100101 Firefox/14.0.1");
	}
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
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	if(!$header) {
		curl_setopt($ch, CURLOPT_HEADER, 0);
	} else {
		curl_setopt($ch, CURLOPT_HEADER, 1);
	}
	if (strtolower(substr($url,0,5))=='https'){
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	}
	if(!config::Select("hosting")) {
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	}
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


function parser_host($url) {
	/*$host = strtr($url, array("youtu.be/" => "youtube.com/watch?v=", "my1.imgsmail.ru" => "video.mail.ru", "vkontakte.ru" => "vk.com"));
	$host = parse_url($host, PHP_URL_HOST);
	$loc = str_replace(array(".com", ".ru", ".at.ua", ".ua", "www.", "."), "", $host);*/
	$server = strtr($url, array("youtu.be/" => "youtube.com/watch?v=", "my1.imgsmail.ru" => "video.mail.ru", "vkontakte.ru" => "vk.com", "kwimg.kz" => "kiwi.kz", "-" => "", "video.rutube.ru" => "rutube.ru", "video.meta.ua" => "video.metas.ua"));
	$host1 = explode("/", strtr($server, array("http://" => "", "https://" => "")));
	$server = current($host1);
	preg_match('/[a-zA-Z0-9]+\.[a-zA-Z0-9]{1,3}\.[a-zA-Z0-9]{2,4}$|[a-zA-Z0-9]{4,}\.[a-zA-Z0-9]{2,4}$/', $server, $host);
	if(isset($host[0]) && !empty($host[0])) {
		$loc = $host[0];
	} else {
		$loc = $server;
	}
	$ret = str_replace(array(".com", ".ru", ".at.ua", ".ua", ".kz", ".tv", ".pro", ".to", ".net", ".uz", "www.", ".info", "."), "", $loc);
	$ret = str_replace("mail", "mailru", $ret);
	if(preg_match("#([0-9]+)#", $ret)) {
		$ret = "v".$ret;
	}
return $ret;
}

?>