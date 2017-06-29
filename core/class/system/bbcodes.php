<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die;
}

class bbcodes {

	private static $bbcodes = array("replace" => array("&amp;laquo;" => "&laquo;", "&amp;raquo;" => "&raquo;", "&amp;nbsp;" => "&nbsp;", "&amp;rsquo;" => "&rsquo;"));
	private static $clear_codes = array();
	private static $eregH2B = array();

	public static function add_code($array, $type) {
		if(isset(self::$bbcodes[$type])) {
			self::$bbcodes[$type] = array_merge(self::$bbcodes[$type], $array);
		} else {
			self::$bbcodes[$type] = $array;
		}
	}

	public static function add_delcode($array, $type) {
		if(isset(self::$clear_codes[$type])) {
			self::$clear_codes[$type] = array_merge(self::$clear_codes[$type], $array);
		} else {
			self::$clear_codes[$type] = $array;
		}
	}

	public static function colorit($text) {
	global $manifest;
		$bbcodes = array(
			'\[(color|font)=(.+)\]' => "<font color=\"$2\">",
			'\[/(color|font)\]' => "</font>",
			'\[bg=(.+)\]' => "<font style=\"background-color:$1\">",
			'\[/bg\]' => "</font>",
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
			"\[img\](.*?)smiles/(.+?)\.(.*?)\|(.*?)\[/img\]" => "<img src=\"{C_default_http_host}core/media/smiles/$2.$3\">",
		);
		if(isset(self::$bbcodes['preg']) && sizeof(self::$bbcodes['preg'])>0) {
			$bbcodes = array_merge($bbcodes, self::$bbcodes['preg']);
		}
		if(isset(self::$bbcodes['replace']) && sizeof(self::$bbcodes['replace'])>0) {
			$codes = self::$bbcodes['replace'];
			$key = array_keys($codes);
			$text = str_replace($key, $codes, $text);
		}
		foreach($bbcodes as $key => $html) {
			$ereg = "isU";
			if(is_array($html)) {
				$ereg = $html['param'];
				$html = $html['val'];
			}
			$text = preg_replace("~".$key."~".$ereg, $html, $text);
		}
		if(isset(self::$bbcodes['call']) && sizeof(self::$bbcodes['call'])>0) {
			foreach(self::$bbcodes['call'] as $key => $html) {
				$ereg = "isU";
				if(is_array($html)) {
					$ereg = $html['param'];
					$html = $html['val'];
				}
				$text = self::CallEreg("#".$key."#".$ereg, $html, $text);
			}
		}
		//$text = str_replace("&quot;", "\"", $text);
	return nl2br($text);
	}

	public static function clear_bbcode($text) {
		$bbcodes = array(
			//"\[(color|font)=(.+)\](.+)\[/(color|font)\]" => "$3",
			"\[url=(\"|)(.+)(\"|)\](.*)\[/url\]" => "<u>$4</u>",
			"\[url\](.*)\[/url\]" => "<u>$1</u>",
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
			$ereg = "isU";
			if(is_array($html)) {
				$ereg = $html['param'];
				$html = $html['val'];
			}
			$text = preg_replace("#".$key."#".$ereg, $html, $text);
		}
		if(isset(self::$clear_codes['call']) && sizeof(self::$clear_codes['call'])>0) {
			foreach(self::$clear_codes['call'] as $key => $html) {
				$ereg = "isU";
				if(is_array($html)) {
					$ereg = $html['param'];
					$html = $html['val'];
				}
				$text = self::CallEreg("#".$key."#".$ereg, $html, $text);
			}
		}
		//$text = str_replace("&quot;", "\"", $text);
	return nl2br($text);
	}
	
	public static function SetHtml2BBCode($ereg, $func) {
		if(!is_array(self::$eregH2B)) {
			self::$eregH2B = array();
		}
		self::$eregH2B[$ereg] = $func;
	}
	
	public static function CallEreg($ereg, $func, $orig) {
		if(function_exists("preg_replace_callback_array")) {
			return preg_replace_callback_array(array($ereg => $func), $orig);
		} else {
			return preg_replace_callback($ereg, $func, $orig);
		}
	}
	
	private static function CallStyleImg($array) {
		if(sizeof($array)!=3) {
			return false;
		}
		$style = array();
		$exp = explode(";", $array[2]);
		for($i=0;$i<sizeof($exp);$i++) {
			$ex = explode(":", trim($exp[$i]));
			$style[$ex[0]] = $ex[1];
		}
		return "[img]".$array[1]."|".base64_encode(serialize($style))."[/img]";
	}

	public static function html2bbcode($text, $activ = array("year" => true,"genre" => true,"descr" => true)) {
	global $lang;
		$text=preg_replace("#<h([0-9]+)>(.+?)</h([0-9]+)>#is", "$2", $text);
		$text=preg_replace("#<em>(.+?)</em>#", "[i]$1[/i]", $text);
		$text=preg_replace("#<i>(.+?)</i>#", "[i]$1[/i]", $text);
		$text=preg_replace("#<u>(.+?)</u>#", "[u]$1[/u]", $text);
		$text=preg_replace("#<b>(.+?)</b>#", "[b]$1[/b]", $text);
		//$text=preg_replace("#[b](.+) [/b]#isU", "[b]$1[/b]", $text);
		$text=preg_Replace("#<font.*?color=['\"](.+?)['\"].*?>(.+?)</font>#", "[color=$1]$2[/color]", $text);
		$text=preg_Replace("#<span.*?style=['\"]background-color:(.+?)['\"].*?>(.+?)</span>#", "[bg=$1]$2[/bg]", $text);
		$text=preg_Replace("#<span.*?style=['\"]font-size:(.+?)(px|pt|em)['\"].*?>(.+?)</span>#", "[size=$1]$3[/size]", $text);
		$text=preg_Replace("#<span.*?style=['\"]color:(.+?)['\"].*?>(.+?)</span>#", "[color=$1]$2[/color]", $text);
		$text=str_replace(array("<br>", "<br />"), "", $text);

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
			$text = strtr($text, array("[/b]:[/b]" => "[/b]", "[b][b]" => "[b]"));
		}
		
		foreach(self::$eregH2B as $ereg => $func) {
			$text = self::CallEreg($ereg, $func, $text);
		}
		
		$text = self::CallEreg("#<img.*?src=['\"](.+?)['\"].*?style=\"(.+?)\".*?>#", "self::CallStyleImg", $text);
		$text = preg_replace("#<img.*?src=['\"](.+?)['\"].*?>#", "[img]$1[/img]", $text);
		$text = preg_replace("#<p style=['\"]text-align:(.+?)[|\;]['\"]>(.+?)</p>#", "[center]$2[/center]", $text);
		$b = array(
			"<strong>" => "[b]",
			"</strong>" => "[/b]",
			"<b>" => "[b]",
			"</b>" => "[/b]",
			"&quot;" => "\"",
			"&amp;nbsp;" => "&nbsp;",
		);
		$text = str_replace(array_keys($b), array_values($b), $text);
		
		$text = preg_replace("#\[img\](.*?)/smiles/(.+?)\[/img\]#", ":$2:", $text);
		
		$text = preg_replace("#<div class=\"title_spoiler\"><a href=\"javascript:ShowOrHide\('(.+?)'\)\"><!--spoiler_title-->(.+?)<!--spoiler_title_end--></a></div>#", "\n\n[spoiler=$2]", $text);
		$text = preg_replace('#<!--spoiler_text_end--></div>#', "[/spoiler]", $text);
		$text = preg_replace('#<a(.*?)href=(\'|")(.+?)(\'|")(.*?)>(.+?)</a>#', "[url=$3]$6[/url]", $text);
	//http://www.animespirit.ru/anime/rs/series-rus/10238-the-testament-of-sister-new-devil-po-veleniyu.html
		$text = trim(strip_tags($text));
		return $text;
	}

}

?>