<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

/**
 * Class Saves
 */
class Saves {

    /**
     * Save as text
     */
    const SAVEText = 1;
    /**
     * Save as integer
     */
    const SAVEInt = 2;
    /**
     * Save as float
     */
    const SAVEFloat = 3;
    /**
     * Save as html
     */
    const SAVEHtml = 4;
    /**
     * Save as string for database
     */
    const SAVEEscape = 5;

    /**
     * @var array Set blocking elements
     */
    private static $blockAllowed = array('javascript\s*:', '(document|(document\.)?window)\.(location|on\w*)', 'expression\s*(\(|&\#40;)', 'vbscript\s*:', 'wscript\s*:', 'jscript\s*:', 'vbs\s*:', 'Redirect\s+30\d', "([\"'])?data\s*:[^\\1]*?base64[^\\1]*?,[^\\1]*?\\1?");

    /**
     * Old version saves
     * @param string $text Old version saves
     * @param bool $db Save as string for database
     * @param bool $ddb Save as string for database double
     * @return string Saved string
     */
    final public static function SaveOld($text, $db = false, $ddb = false) {
		if(is_array($text)) {
			$ret = array();
			foreach($text as $k => $v) {
				$ret[$k] = self::SaveOld($v, $db, $ddb);
			}
			return $ret;
		}
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

    /**
     * Remove invisible characters
     * @param string $str Needed string
     * @param bool $url_encoded Save links
     * @return string Removed invisible characters
     */
    final public static function remove_invisible_characters($str, $url_encoded = true) {
		if(is_array($str)) {
			$ret = array();
			foreach($str as $k => $v) {
				$ret[$k] = self::remove_invisible_characters($v, $url_encoded);
			}
			return $ret;
		}
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

    /**
     * Save html special chars
     * @param string|array $var Needed string
     * @param bool $double_encode Double encoded. See official documentation
     * @param string $charset Needed charset
     * @return array|string Save html special chars
     */
    final public static function html_escape($var, $double_encode = true, $charset = "") {
		if(empty($var)) {
			return $var;
		}
		if(is_array($var)) {
			$arr = array_keys($var);
			foreach($arr as $key) {
				$var[$key] = self::html_escape($var[$key], $double_encode, $charset);
			}
			return $var;
		}
		return htmlspecialchars($var, ENT_QUOTES, $charset, $double_encode);
	}

    /**
     * Save string for alternative name
     * @param string $uri Link for save
     * @return string Saved link
     */
    final public static function SaveAltName($uri) {
    	if(is_array($uri)) {
    		$ret = array();
    		foreach($uri as $k => $v) {
    			$ret[$k] = self::SaveAltName($v);
    		}
    		return $ret;
    	}
		$uri = preg_replace("|[^\d\w ]+|i", "", $uri);
        $uri = strip_tags($uri);
		$uri = html_entity_decode($uri, ENT_QUOTES, 'UTF-8');
		$uri = preg_replace("#[\.;:\]\}\[\{\+\)\(\*&\^\$\#@\!±`%~']#iu", '', $uri);
		$uri = preg_replace("#[\"\']#", '', $uri);
		$uri = preg_replace("#[\’]#", '-', $uri);
		$uri = preg_replace("#[/_|+ -]+#u", "-", $uri);
		$uri = trim($uri, "-");
		$uri = htmlspecialchars($uri, ENT_QUOTES, 'ISO-8859-1');
		$uri = self::SaveOld($uri, true);
		return $uri;
	}

    /**
     * Save text check type data
     * @param string $val Needed string
     * @param bool $double_encode Double encoded. See official documentation for htmlspecialchars
     * @param string $charset If value is string saves data in needed charset
     * @return array|bool|float|int|string Saved data
     */
    final public static function SaveAuto($val, $double_encode = true, $charset = "") {
    	if(is_array($val)) {
    		$ret = array();
    		foreach($val as $k => $v) {
    			$ret[$k] = self::SaveAuto($v, $double_encode, $charset);
    		}
    		return $ret;
    	}
		$ret = "";
		if(is_bool($val)) {
			$ret = self::SaveBool($val);
		} elseif(is_numeric($val) && is_int($val)) {
			$ret = self::SaveInt($val);
		} elseif(is_numeric($val) && is_float($val)) {
			$ret = self::SaveFloat($val);
		} elseif(is_array($val)) {
			$ret = array_map("Saves::SaveAuto", $val);
		} elseif(is_string($val)) {
            $ret = self::SaveText($val, $double_encode, $charset);
        }
		return $ret;
	}

    /**
     * Remove all suspects symbols
     * @param string $val Needed string
     * @param bool $double_encode Double encoded. See official documentation for htmlspecialchars
     * @param string $charset Saves data in needed charset
     * @return string Saved data
     */
    final public static function SaveText($val, $double_encode = true, $charset = "") {
    	if(is_array($val)) {
    		$ret = array();
    		foreach($val as $k => $v) {
    			$ret[$k] = self::SaveText($v, $double_encode, $charset);
    		}
    		return $ret;
    	}
		$val = preg_replace('#<script[^>]*>.*?</script>#is', "", $val);
		$val = strip_tags($val);
		$val = self::html_escape($val, $double_encode, $charset);
		$val = self::remove_invisible_characters($val, false);
		$val = preg_replace('#('.implode("|", array_keys(self::$blockAllowed)).')#is', '[removed]', $val);
		return $val;
	}

    /**
     * Save data as integer
     * @param mixed $val Needed save
     * @return int Saved data
     */
    final public static function SaveInt($val) {
		if(is_array($val)) {
			$ret = array();
			foreach($val as $k => $v) {
				$ret[$k] = self::SaveInt($v);
			}
			return $ret;
		}
		return intval($val);
	}

    /**
     * Save data as boolean
     * @param mixed $val Needed save
     * @return bool Saved data
     */
    final public static function SaveBool($val) {
		if(is_array($val)) {
			$ret = array();
			foreach($val as $k => $v) {
				$ret[$k] = self::SaveBool($v);
			}
			return $ret;
		}
		return boolval($val);
	}

    /**
     * Save data as float
     * @param mixed $val Needed save
     * @return float Saved data
     */
	final public static function SaveFloat($val) {
		if(is_array($val)) {
			$ret = array();
			foreach($val as $k => $v) {
				$ret[$k] = self::SaveFloat($v);
			}
			return $ret;
		}
		return floatval($val);
	}

    /**
     * Save all info in html
     * @param string $val Needed saves
     * @param bool $delete Remove "<p>" and replace "<strong>" on "<b>"
     * @return string Saved data
     */
    final public static function SaveHtml($val, $delete = false) {
		if(is_array($val)) {
			$ret = array();
			foreach($val as $k => $v) {
				$ret[$k] = self::SaveHtml($v, $delete);
			}
			return $ret;
		}
		$val = str_replace("\r\n", "\n", $val);
		$val = str_replace(array("\"", "<p>&nbsp;</p>", "&nbsp;"), array("\\\"", "<br />", " "), $val);
		$val = preg_replace('#<p(.+?)> </p>#i', "<br />", $val);
		if($delete) {
			$val = str_replace(array("<strong>", "</strong>", "<p>", "</p>"), array("<b>", "</b>", "", ""), $val);
		}
		$val = self::remove_invisible_characters($val, false);
		return $val;
	}

    /**
     * Save data as prepared data for database
     * @param string $val Needed saves
     * @return string Saved data
     */
    final public static function SaveEscape($val) {
		if(is_array($val)) {
			$ret = array();
			foreach($val as $k => $v) {
				$ret[$k] = self::SaveEscape($v);
			}
			return $ret;
		}
		return str_replace(array('\x00', '\n', '\r', '\\', "'", '"', '\x1a'), array('\\x00', '\\n', '\\r', '\\\\', "\'", '\"', '\\x1a'), $val);
	}

    /**
     * Save as neatly selected type
     * @param array|float|int|string $val Needed saves
     * @param int $type Type saves
     * @return array|float|int|string Saved data
     */
    final public static function SaveType($val, $type = Saves::SAVEText) {
		if(is_array($val)) {
			$ret = array();
			foreach($val as $k => $v) {
				$ret[$k] = self::SaveType($v, $type);
			}
			return $ret;
		}
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