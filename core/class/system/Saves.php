<?php

class Saves {
	
	const SAVEText = 1;
	const SAVEInt = 2;
	const SAVEFloat = 3;
	const SAVEHtml = 4;
	const SAVEEscape = 5;
	
	private static $blockAllowed = array('javascript\s*:', '(document|(document\.)?window)\.(location|on\w*)', 'expression\s*(\(|&\#40;)', 'vbscript\s*:', 'wscript\s*:', 'jscript\s*:', 'vbs\s*:', 'Redirect\s+30\d', "([\"'])?data\s*:[^\\1]*?base64[^\\1]*?,[^\\1]*?\\1?");
	
	final public static function SaveOld($text, $db = false, $ddb = false) {
		if($ddb) {
			$text = str_replace('"', '\\\\"', $text);
		} elseif($db) {
			$text = str_replace("\\", "\\\\", $text);
			$text = str_replace('"', '\\"', $text);
		} else {
			$text = str_replace("&quot;", "\\\"", $text);
		}
		$text = preg_replace('#<script[^>]*>.*?</script>#is', "", $text);
		$text = strip_tags($text);
		$text = htmlspecialchars($text);
		if($db) {
			$text = str_replace("&quot;", '"', $text);
		}
	return $text;
	}
	
	final public static function remove_invisible_characters($str, $url_encoded = true) {
		$non_displayables = array();
		// every control character except newline (dec 10),
		// carriage return (dec 13) and horizontal tab (dec 09)
		if($url_encoded) {
			$non_displayables[] = '/%0[0-8bcef]/';	// url encoded 00-08, 11, 12, 14, 15
			$non_displayables[] = '/%1[0-9a-f]/';	// url encoded 16-31
		}
		$non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127
		do {
			$str = preg_replace($non_displayables, '', $str, -1, $count);
		} while ($count);
		return $str;
	}
	
	final public static function html_escape($var, $double_encode = true, $chatset = "") {
		if(empty($var)) {
			return $var;
		}
		if(is_array($var)) {
			$arr = array_keys($var);
			foreach($arr as $key) {
				$var[$key] = self::html_escape($var[$key], $double_encode, $chatset);
			}
			return $var;
		}
		return htmlspecialchars($var, ENT_QUOTES, $chatset, $double_encode);
	}
	
	final public static function SaveAltName($uri) {
		$uri = preg_replace("|[^\d\w ]+|i", "", $uri);
		$uri = htmlspecialchars($uri, ENT_QUOTES, 'ISO-8859-1');
		$uri = self::SaveOld($uri, true);
		return $uri;
	}
	
	final public static function SaveAuto($val, $double_encode = true, $charset = "") {
		$ret = "";
		if(is_string($val)) {
			$ret = self::SaveText($val, $double_encode, $charset);
		} elseif(is_bool($val)) {
			$ret = self::SaveBool($val);
		} elseif(is_numeric($val) && is_int($val)) {
			$ret = self::SaveInt($val);
		} elseif(is_numeric($val) && is_float($val)) {
			$ret = self::SaveFloat($val);
		} elseif(is_array($val)) {
			$ret = array_map("Saves::SaveAuto", $val);
		}
		return $ret;
	}
	
	final public static function SaveText($val, $double_encode = true, $charset = "") {
		$val = preg_replace('#<script[^>]*>.*?</script>#is', "", $val);
		$val = strip_tags($val);
		$val = self::html_escape($val, $double_encode, $charset);
		$val = self::remove_invisible_characters($val, false);
		$val = preg_replace('#('.implode("|", array_keys(self::$blockAllowed)).')#is', '[removed]', $val);
		return $val;
	}
	
	final public static function SaveInt($val) {
		return intval($val);
	}
	
	final public static function SaveBool($val) {
		return boolval($val);
	}
	
	final public static function SaveFloat($val) {
		return floatval($val);
	}
	
	final public static function SaveHtml($val) {
		$val = str_replace("\r\n", "\n", $val);
		$val = str_replace(array("\"", "<p>&nbsp;</p>", "&nbsp;"), array("\\\"", "<br />", " "), $val);
		$val = preg_replace('#<p(.+?)> </p>#i', "<br />", $val);
		return $val;
	}
	
	final public static function SaveEscape($val) {
		return str_replace(array('\x00', '\n', '\r', '\\', "'", '"', '\x1a'), array('\\x00', '\\n', '\\r', '\\\\', "\'", '\"', '\\x1a'), $val);
	}
	
	final public static function SaveType($val, $type = Saves::SAVEText) {
		$ret = "";
		switch($type) {
			case self::SAVEText:
				$ret = self::SaveText($val);
			break;
			case self::SAVEInt:
				$ret = self::SaveInt($val);
			break;
			case self::SAVEFloat:
				$ret = self::SaveFloat($val);
			break;
			case self::SAVEHtml:
				$ret = self::SaveHtml($val);
			break;
			case self::SAVEEscape:
				$ret = self::SaveEscape($val);
			break;
		}
		return $ret;
	}
	
}

?>