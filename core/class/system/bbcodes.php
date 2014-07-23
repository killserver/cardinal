<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die;
}

class bbcodes {

	static private $bbcodes = array();
	static private $clear_codes = array();

	function add_code($array, $type) {
		if(isset(self::$bbcodes[$type])) {
			self::$bbcodes[$type] = array_merge(self::$bbcodes[$type], $array);
		} else {
			self::$bbcodes[$type] = $array;
		}
	}

	function add_delcode($array, $type) {
		if(isset(self::$clear_codes[$type])) {
			self::$clear_codes[$type] = array_merge(self::$clear_codes[$type], $array);
		} else {
			self::$clear_codes[$type] = $array;
		}
	}

	function colorit($text) {
	global $manifest;
		$bbcodes = array(
			"\[(color|font)=(.+)\](.+)\[/(color|font)\]" => "<font color=\"$2\">$3</font>",
			"\[url=(\"|)(.+)(\"|)\](.*)\[/url\]" => "<a href=\"$2\" target=\"_blank\"><u>$4</u></a>",
			"\[url\](.*)\[/url\]" => "<a href=\"$1\" target=\"_blank\"><u>$1</u></a>",
	//		"\[url=(ft|https?://)(.+)\](.+)\[/url\]" => "<a href=\"$1$2\" target=\"_blank\"><u>$3</u></a>",
			"\[b\](.+)\[/b\]" => "<b>$1</b>",
			"\[i\](.+)\[/i\]" => "<i>$1</i>",
			"\[u\](.+)\[/u\]" => "<u>$1</u>",
			"\[s\](.+)\[/s\]" => "<strike>$1</strike>",
			"\[strike\](.+)\[/strike\]" => "<strike>$1</strike>",
			"\[c\](.*)\[/c\]" => "<center>$1</center>",
			"\[center\](.*)\[/center\]" => "<center>$1</center>",
			"\[hr\]" => "<hr />",
			"\[size\=([0-9]+)\](.*)\[/size\]" => "<span style=\"font-size: $1px;\">$2</span>",
			"\[style\=(.+)\](.*)\[/style\]" => "<span style=\"font-family: $1;\">$2</span>",
		);
		if(isset(self::$bbcodes['preg']) && sizeof(self::$bbcodes['preg'])>0) {
			$bbcodes = array_merge($bbcodes, self::$bbcodes['preg']);
		}

		foreach($bbcodes as $key => $html) {
			$text = preg_replace("#".$key."#isU", $html, $text);
		}
		if(isset(self::$bbcodes['call']) && sizeof(self::$bbcodes['call'])>0) {
			foreach(self::$bbcodes['call'] as $key => $html) {
				$text = preg_replace_callback("#".$key."#isU", $html, $text);
			}
		}
		//$text = str_replace("&quot;", "\"", $text);
	return nl2br($text);
	}

	function clear_bbcode($text) {
		$bbcodes = array(
			"\[(color|font)=(.+)\](.+)\[/(color|font)\]" => "$3",
			"\[url=(\"|)(.+)(\"|)\](.*)\[/url\]" => "$4",
			"\[url\](.+)\[/url\]" => "$1",
			"\[b\](.+)\[/b\]" => "$1",
			"\[i\](.+)\[/i\]" => "$1",
			"\[u\](.+)\[/u\]" => "$1",
			"\[s\](.+)\[/s\]" => "$1",
			"\[strike\](.+)\[/strike\]" => "$1",
			"\[c\](.+)\[/c\]" => "$1",
			"\[center\](.+)\[/center\]" => "$1",
			"\[size=([0-9]+)\](.+)\[/size\]" => "$2",
		);
		if(isset(self::$clear_codes['preg']) && sizeof(self::$clear_codes['preg'])>0) {
			$bbcodes = array_merge($bbcodes, self::$clear_codes['preg']);
		}
	
		foreach($bbcodes as $key => $html) {
			$text = preg_replace("#".$key."#isU", $html, $text);
		}
		if(isset(self::$clear_codes['call']) && sizeof(self::$clear_codes['call'])>0) {
			foreach(self::$clear_codes['call'] as $key => $html) {
				$text = preg_replace_callback("#".$key."#isU", $html, $text);
			}
		}
		//$text = str_replace("&quot;", "\"", $text);
	return nl2br($text);
	}

	function html2bbcode($text, $activ = array("year"=>true,"genre"=>true,"descr"=>true)) {
	global $lang;
		$text=preg_replace("#<h([0-9]+)>(.+)</h([0-9]+)>#isU", "$2", $text);
		$text=preg_replace("#<u>(.+)</u>#isU", "[u]$1[/u]", $text);
		$text=preg_replace("#<b>(.+)</b>#isU", "[b]$1[/b]", $text);
		//$text=preg_replace("#[b](.+) [/b]#isU", "[b]$1[/b]", $text);
		$text=preg_Replace("#<font color=('|\")(.+)('|\")>(.+)</font>#isU", "[color=$2]$4[/color]", $text);
		$text=str_replace(array("<br>", "<br />"), "\n", $text);

	if($activ['year']) {
	//Год выхода
		$text = strtr($text, array("<b>".$lang['year_view']."</b>" => "[b]".$lang['year']."[/b]"));
		$text = strtr($text, array($lang['year_view'] => "[b]".$lang['year']."[/b]"));
	//Год выпуска
		$text = strtr($text, array("<b>".$lang['year_start']."</b>" => "[b]".$lang['year']."[/b]"));
		$text = strtr($text, array($lang['year_start'] => "[b]".$lang['year']."[/b]"));
	//Год
		$text = strtr($text, array("<b>".$lang['year']."</b>" => "[b]".$lang['year']."[/b]"));
		$text = strtr($text, array($lang['year'] => "[b]".$lang['year']."[/b]"));
		$text = strtr($text, array("[b][b]".$lang['year']."[/b][/b]" => "[b]".$lang['year']."[/b]"));
	//Годы в единую систему
		$text = strtr($text, array("[b][b]".$lang['year']."[/b][/b]" => "[b]".$lang['year']."[/b]"));
		$text = strtr($text, array($lang['year_start'] => $lang['year'], $lang['year_view'] => $lang['year']));
	}

	if($activ['genre']) {
	//Жанр
		$text = strtr($text, array("<b>".$lang['genre']."</b>" => "[b]".$lang['genre']."[/b]"));
		$text = strtr($text, array($lang['genre'] => "[b]".$lang['genre']."[/b]"));
		$text = strtr($text, array("[b][b]".$lang['genre']."[/b][/b]" => "[b]".$lang['genre']."[/b]"));
	}

	if($activ['descr']) {
	//Описание
		$text = strtr($text, array("<b>".$lang['descr']."</b>" => "[b]".$lang['descr']."[/b]"));
		$text = strtr($text, array($lang['descr'] => "[b]".$lang['descr']."[/b]"));
		$text = strtr($text, array("[b][b]".$lang['descr']."[/b][/b]" => "[b]".$lang['descr']."[/b]"));
	}

		$text=strip_tags($text);
	return $text;
	}

}

?>