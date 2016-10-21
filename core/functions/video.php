<?php
/*
*
* Version Engine: 1.25.3
* Version File: 2
*
* 2.0
* redirect video function parser_host in function parsers(maybe video.php delete?)
*
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}


function others_video($video, $type="current", &$season = 1, &$seriya = 1) {
global $lang;
//var_dump(strtolowers($video));die();
	if(strpos($video, "#") !== false) {
		$ex = explode("#", $video);
		return trim($type($ex));
	}
	if(strpos($video, $lang['num_sim']) !== false) {
		$ex = explode($lang['num_sim'], $video);
		return trim($type($ex));
	}
	if(preg_match("/(.*)([0-9\.|]+) ".$lang['season']."(.*)/iu", strtolowers($video), $all)) {
		$season = $all[2];
		return preg_replace("/(.*?)([0-9\.|]+) ".$lang['season']."(.*)/iu", "$1", strtolowers($video));
	}
	if(preg_match("/(.*)([0-9\.|]+) (".$lang['seriya']."|".$lang['seriya_ua'].")(.*)/iu", strtolowers($video), $all)) {
		$seriya = $all[2];
		return preg_replace("/(.*?)([0-9\.|]+) (".$lang['seriya']."|".$lang['seriya_ua'].")(.*)/iu", "$1", strtolowers($video));
	}
	if(preg_match("/(.*)([0-9\.|]+) ".$lang['part']."(.*)/iu", strtolowers($video))) {
		return preg_replace("/(.*?)([0-9\.|]+) ".$lang['part']."(.*)/iu", "$1", strtolowers($video));
	}
	if(stripos(strtolowers($video), $lang['season']) !== false) {
		$ex = explode($lang['season'], strtolowers($video));
		return trim($type($ex));
	}
	if(stripos(strtolowers($video), $lang['seriya']) !== false) {
		$ex = explode($lang['seriya'], strtolowers($video));
		return trim($type($ex));
	}
	if(stripos(strtolowers($video), $lang['seriya_ua']) !== false) {
		$ex = explode($lang['seriya_ua'], strtolowers($video));
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