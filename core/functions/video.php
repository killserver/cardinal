<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

function parser_host($url) {
	/*$host = strtr($url, array("youtu.be/" => "youtube.com/watch?v=", "my1.imgsmail.ru" => "video.mail.ru", "vkontakte.ru" => "vk.com"));
	$host = parse_url($host, PHP_URL_HOST);
	$loc = str_replace(array(".com", ".ru", ".at.ua", ".ua", "www.", "."), "", $host);*/
	$server = strtr($url, array("youtu.be/" => "youtube.com/watch?v=", "my1.imgsmail.ru" => "video.mail.ru", "vkontakte.ru" => "vk.com", "kwimg.kz" => "kiwi.kz"));
	$host1 = explode("/", strtr($server, array("http://" => "", "https://" => "")));
	$server = current($host1);
	preg_match('/[a-zA-Z0-9]+\.[a-zA-Z0-9]{1,3}\.[a-zA-Z0-9]{2,4}$|[a-zA-Z0-9]{4,}\.[a-zA-Z0-9]{2,4}$/', $server, $host);
	if(isset($host[0]) && !empty($host[0])) {
		$loc = $host[0];
	} else {
		$loc = $server;
	}
	$ret = str_replace(array(".com", ".ru", ".at.ua", ".ua", ".kz", ".tv", ".pro", ".to", ".net", "www.", "."), "", $loc);
return $ret;
}



function others_video($video, $type="current") {
global $lang, $user;
//var_dump(strtolowers($video));die();
	if(strpos($video, "#") !== false) {
		$ex = explode("#", $video);
		return trim($type($ex));
	}
	if(strpos($video, $lang['num_sim']) !== false) {
		$ex = explode($lang['num_sim'], $video);
		return trim($type($ex));
	}
	if(preg_match("/(.*)([0-9]+) ".$lang['season']."(.*)/iu", strtolowers($video))) {
		return preg_replace("/(.*?)([0-9]+) ".$lang['season']."(.*)/iu", "$1", strtolowers($video));
	}
	if(preg_match("/(.*)([0-9]+) ".$lang['seriya']."(.*)/iu", strtolowers($video))) {
		return preg_replace("/(.*?)([0-9]+) ".$lang['seriya']."(.*)/iu", "$1", strtolowers($video));
	}
	if(preg_match("/(.*)([0-9]+) ".$lang['part']."(.*)/iu", strtolowers($video))) {
		return preg_replace("/(.*?)([0-9]+) ".$lang['part']."(.*)/iu", "$1", strtolowers($video));
	}
	if(stripos(strtolowers($video), $lang['season']) !== false) {
		$ex = explode($lang['season'], strtolowers($video));
		return trim($type($ex));
	}
	if(stripos(strtolowers($video), $lang['seriya']) !== false) {
		$ex = explode($lang['seriya'], strtolowers($video));
		return trim($type($ex));
	}
	if(stripos($video, $lang['part_b']) !== false) {
		$ex = explode($lang['part_b'], $video);
		return trim($type($ex));
	}
	if(stripos($video, $lang['part']) !== false) {
		$ex = explode($lang['part'], $video);
		return trim($type($ex));
	}
}

?>