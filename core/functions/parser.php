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

//FIXME: а нафиг мне тогда эта функция, если есть класс Parser?!
/*function parser_url($url, $referer = "", $header=false, $coo=false, $coopath="", $proxy="", $error=false, $gzip=false, $uagent="", $timeout=3) {
	$p = new Parser($url);
	$p->agent($uagent);
	$p->cookie($coo, $coopath);
	if(!empty($referer)) {
		$p->referer($referer);
	}
	$p->timeout($timeout);
	$p->header($header);
	if($gzip) {
		$p->gzip($gzip);
	}
	if(!empty($proxy)) {
		$p->proxy($proxy);
	}
	if($error) {
		$p->error($error, $error);
		$p->init();
	}
	return $p->get();
}*/

function parser_video($content, $start, $end = "") {
	$pos = strpos($content, $start);
	$content = substr($content, $pos);
	if($end!=="") {
		$pos = strpos($content, $end);
	} else {
		$pos = strlen($content);
	}
	$content = substr($content, 0, $pos);
	$content = str_replace($start, "", $content);
return $content;
}


/*function parser_host($url) {
	//$host = strtr($url, array("youtu.be/" => "youtube.com/watch?v=", "my1.imgsmail.ru" => "video.mail.ru", "vkontakte.ru" => "vk.com"));
	//$host = parse_url($host, PHP_URL_HOST);
	//$loc = str_replace(array(".com", ".ru", ".at.ua", ".ua", "www.", "."), "", $host);
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
}*/

function closetags($html, $singleTagsAdd = array()) { return function_call('closetags', array($html, $singleTagsAdd)); }
function or_closetags($html, $singleTagsAdd = array()) {
	$single_tags = array('meta', 'img', 'br', 'link', 'area', 'input', 'hr', 'col', 'param', 'base');
	if(sizeof($singleTagsAdd)>0) {
		$single_tags = array_merge($single_tags, $singleTagsAdd);
	}
	preg_match_all('~<([a-z0-9]+)(?: .*)?(?<![/|/ ])>~iU', $html, $result);
	$openedtags = $result[1];
	preg_match_all('~</([a-z0-9]+)>~iU', $html, $result);
	$closedtags = $result[1];
	$len_opened = sizeof($openedtags);
	if(sizeof($closedtags) == $len_opened) {
		return $html;
	}
    $openedtags = array_reverse($openedtags);
	for($i=0;$i<$len_opened;$i++) {
		if(!in_array($openedtags[$i], $single_tags)) {
			if(($key = array_search($openedtags[$i], $closedtags)) !== false) {
				unset($closedtags[$key]);
			} else {
				$html .= '</'.$openedtags[$i].'>';
			}
		}
	}
	return $html;
}

?>