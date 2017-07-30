<?php
/*
 *
 * @version 1.25.7-a5
 * @copyright 2014-2015 KilleR for Cardinal Engine
 *
 * Version Engine: 1.25.7-a5
 * Version File: 4
 *
 * 4.1
 * add support mb_detect_encoding without library mb_* on server
 * 4.2
 * add support cut before start match on text
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}


/**
 * Replacement cut text between start and end, start 0 to start and add text in end
 * @param string $text Original text
 * @param int $start Start or end text
 * @param int|string $end End text or added after cut
 * @param string $add Added after cut
 * @return string Cut text
 */
function cut($text, $start, $end = "", $add = ""){return function_call('cut', array($text, $start, $end, $add));}

/**
 * Cut text between start and end, start 0 to start and add text in end
 * @param string $text Original text
 * @param int $start Start or end text
 * @param int|string $end End text or added after cut
 * @param string $add Added after cut
 * @return string Cut text
 */
function or_cut($text, $start, $end = "", $add = "") {
	if(empty($end) && is_string($start)) {
		$add = "";
		$start = nstrpos($text, $start)+nstrlen($start);
		$end = nstrlen($text);
	} else {
		if(empty($add)) {
			$add = $end;
			$end = $start;
			$start = 0;
		}
		if(nstrlen($text)<=$end) {
			$add = "";
		}
	}
	return nsubstr($text, $start, $end).$add;
}


/**
 * Replacement detect charset
 * @param string $string Text for detected
 * @param int $pattern_size Part pattern
 * @return string Result detected charset
 */
function iconv_charset($string, $pattern_size = 50){ return function_call('iconv_charset', array($string, $pattern_size)); }

/**
 * Detect charset
 * @param string $string Text for detected
 * @param int $pattern_size Part pattern
 * @return string Result detected charset
 */
function or_iconv_charset($string, $pattern_size = 50) {
	if(function_exists("mb_detect_encoding") && defined('MB_OVERLOAD_STRING') && ini_get('mbstring.func_overload')!==false && MB_OVERLOAD_STRING) {
		return mb_detect_encoding($string, array('UTF-8', 'Windows-1251', 'KOI8-R', 'ISO-8859-5'));
	} else {
		$first2 = substr($string, 0, 2);
	    $first3 = substr($string, 0, 3);
	    $first4 = substr($string, 0, 3);
	    
	    $UTF32_BIG_ENDIAN_BOM = chr(0x00).chr(0x00).chr(0xFE).chr(0xFF);
		$UTF32_LITTLE_ENDIAN_BOM = chr(0xFF).chr(0xFE).chr(0x00).chr(0x00);
		$UTF16_BIG_ENDIAN_BOM = chr(0xFE).chr(0xFF);
		$UTF16_LITTLE_ENDIAN_BOM = chr(0xFF).chr(0xFE);
		$UTF8_BOM = chr(0xEF).chr(0xBB).chr(0xBF);
	    
	    if($first3 == $UTF8_BOM) {
			return 'UTF-8';
	    } elseif($first4==$UTF32_BIG_ENDIAN_BOM) {
			return 'UTF-32';
		} elseif($first4==$UTF32_LITTLE_ENDIAN_BOM) {
			return 'UTF-32';
		} elseif($first2==$UTF16_BIG_ENDIAN_BOM) {
			return 'UTF-16';
		} elseif($first2==$UTF16_LITTLE_ENDIAN_BOM) {
			return 'UTF-16';
		}
	    
	    $list = array('CP1251', 'UTF-8', 'ASCII', '855', 'KOI8R', 'ISO-IR-111', 'CP866', 'KOI8U');
	    $c = strlen($string);
	    if($c>$pattern_size) {
	        $string = substr($string, floor(($c-$pattern_size)/2), $pattern_size);
	        $c = $pattern_size;
	    }
	
	    $reg1 = '/(\xE0|\xE5|\xE8|\xEE|\xF3|\xFB|\xFD|\xFE|\xFF)/i';
	    $reg2 = '/(\xE1|\xE2|\xE3|\xE4|\xE6|\xE7|\xE9|\xEA|\xEB|\xEC|\xED|\xEF|\xF0|\xF1|\xF2|\xF4|\xF5|\xF6|\xF7|\xF8|\xF9|\xFA|\xFC)/i';
	
	    $mk = 10000;
	    $enc = 'UTF-8';
	    foreach($list as $item) {
	        $sample1 = @iconv($item, 'cp1251', $string);
	        $gl = @preg_match_all($reg1, $sample1, $arr);
	        $sl = @preg_match_all($reg2, $sample1, $arr);
	        if(!$gl || !$sl) {
				continue;
			}
	        $k = abs(3-($sl/$gl));
	        $k += $c-$gl-$sl;
	        if($k<$mk) {
	            $enc = $item;
	            $mk = $k;
	        }
	    }
	    return $enc;
	}
}

/**
 * Get chmod for path
 * @param string $path Path for detect chmod
 * @return string Chmod directory
 */
function get_chmod($path) {
	return substr(sprintf('%o', fileperms($path)), -4);
}

function isoTOint($data) {
	$datetime = new DateTime('@0');
	$datetime->add(new DateInterval($data));
	return $datetime->format('U');
}

/**
 * Substring text in charset engine
 * @param string $text Needed text
 * @param int $start Start cut
 * @param int $end End cut
 * @return string Part text
 */
function nsubstr($text, $start, $end = "") {
	if(empty($end)) {
		$end = nstrlen($text);
	}
	if(function_exists("mb_substr") && defined('MB_OVERLOAD_STRING') && ini_get('mbstring.func_overload')!==false && MB_OVERLOAD_STRING) {
		return mb_substr($text, $start, $end, config::Select('charset'));
	} elseif(function_exists("iconv_substr")) {
		return iconv_substr($text, $start, $end, config::Select('charset'));
	} else {
		return substr($text, $start, $end);
	}
}

/**
 * String length in charset engine
 * @param string $text Needed text
 * @return int Length text
 */
function nstrlen($text) {
	if(function_exists("mb_strlen") && defined('MB_OVERLOAD_STRING') && ini_get('mbstring.func_overload')!==false && MB_OVERLOAD_STRING) {
		return mb_strlen($text, config::Select('charset'));
	} elseif(function_exists("iconv_strlen")) {
		return iconv_strlen($text, config::Select('charset'));
	} else {
		return strlen($text);
	}
}

/**
 * Added need length to competed string
 * @param string $str Needed text
 * @param int $pad_len Needed length
 * @param string $pad_str Needed completed
 * @param int $dir Where added text
 * @return string Result pad text
 */
function nstr_pad($str, $pad_len, $pad_str = ' ', $dir = STR_PAD_RIGHT) {
   return str_pad($str, strlen($str)-nstrlen($str)+$pad_len, $pad_str, $dir); 
}

function nstr_padv2($str, $pad_len, $pad_str = ' ', $dir = STR_PAD_RIGHT) {
    $encoding = iconv_charset($str);
    $padBefore = $dir === STR_PAD_BOTH || $dir === STR_PAD_LEFT;
    $padAfter = $dir === STR_PAD_BOTH || $dir === STR_PAD_RIGHT;
    $pad_len -= nstrlen($str, $encoding);
    $targetLen = $padBefore && $padAfter ? $pad_len / 2 : $pad_len;
    $strToRepeatLen = nstrlen($pad_str, $encoding);
    $repeatTimes = ceil($targetLen / $strToRepeatLen);
    $repeatedString = str_repeat($pad_str, max(0, $repeatTimes)); // safe if used with valid utf-8 strings
    $before = $padBefore ? nsubstr($repeatedString, 0, floor($targetLen), $encoding) : '';
    $after = $padAfter ? nsubstr($repeatedString, 0, ceil($targetLen), $encoding) : '';
    return $before . $str . $after;
}

function is_infinites($val){ return function_call('is_infinites', array($val)); }
function or_is_infinites($val) {
	return (is_float($val) && (defined("INF") ? ($val==INF || $val==(-(INF))) : (strval($val)=='INF' || strval($val)=='-INF')));
}

function int_pad($str, $pad_len, $pad_str = 0, $dir = STR_PAD_RIGHT){ return function_call('int_pad', array($str, $pad_len, $pad_str, $dir)); }
function or_int_pad($str, $pad_len, $pad_str = 0, $dir = STR_PAD_RIGHT) {
	$str = str_pad($str, $pad_len, $pad_str, $dir);
	return intval($str);
}


function del_in_file($file, $row_number) {
	if(file_exists($file)) {
		$file_out = file($file);
		if(isset($file_out[$row_number])) {
			unset($file_out[$row_number]);
		}
		unlink($file);
		file_put_contents($file, implode("", $file_out));
		return true;
	} else {
		return false;
	}
}

function nstrpos($text, $search, $pos = 0) {
	if(function_exists("mb_strpos") && defined('MB_OVERLOAD_STRING') && ini_get('mbstring.func_overload')!==false && MB_OVERLOAD_STRING) {
		return mb_strpos($text, $search, $pos, config::Select('charset'));
	} elseif(function_exists("iconv_strpos")) {
		return iconv_strpos($text, $search, $pos, config::Select('charset'));
	} else {
		return strpos($text, $search, $pos);
	}
}

function nucfirst($text, $all = false) {
	$fc = strtouppers(nsubstr($text, 0, 1));
	if(!$all) {
		$fc .= nsubstr($text, 1);
	} else {
		$fc .= strtolowers(nsubstr($text, 1));
	}
	return $fc;
}

function nlcfirst($text, $all = false) {
	$fc = strtolowers(nsubstr($text, 0, 1));
	if(!$all) {
		$fc .= nsubstr($text, 1);
	} else {
		$fc .= strtolowers(nsubstr($text, 1));
	}
	return $fc;
}

/*
 New on version 6.3
*/
function is_ascii($str) {
	if(is_array($str)) {
		$str = implode($str);
	}
	return !preg_match('/[^\x00-\x7F]/S', $str);
}

/*
 New on version 6.3
*/
function nltrim($str, $charlist = NULL) {
	if($charlist === NULL) {
		return ltrim($str);
	}
	if(is_ascii($str)) {
		return ltrim($str, $charlist);
	} else {
		$charlist = preg_replace('#[-\[\]:\\\\^/]#', '\\\\$0', $charlist);
		return preg_replace('/^['.$charlist.']+/u', '', $str);
	}
}

/*
 New on version 6.3
*/
function nrtrim($str, $charlist = NULL) {
	if($charlist === NULL) {
		return rtrim($str);
	}
	if(is_ascii($str)) {
		return rtrim($str, $charlist);
	} else {
		$charlist = preg_replace('#[-\[\]:\\\\^/]#', '\\\\$0', $charlist);
		return preg_replace('/['.$charlist.']++$/uD', '', $str);
	}
}

function saves($text, $db = false, $ddb = false){ return function_call('saves', array($text, $db, $ddb)); }

function or_saves($text, $db = false, $ddb = false) {
return Saves::SaveOld($text, $db, $ddb);
}

function strtouppers($text) {
	if(function_exists("mb_strtoupper") && defined('MB_OVERLOAD_STRING') && ini_get('mbstring.func_overload')!==false && MB_OVERLOAD_STRING) {
		return mb_strtoupper($text, config::Select('charset'));
	} else {
		return strtoupper($text);
	}
}

function strtolowers($text) {
	if(function_exists("mb_strtolower") && defined('MB_OVERLOAD_STRING') && ini_get('mbstring.func_overload')!==false && MB_OVERLOAD_STRING) {
		return mb_strtolower($text, config::Select('charset'));
	} else {
		return strtolower($text);
	}
}

function comp_search($text = "", $finds = array()) {
	if(empty($text)) {
		return "";
	}
	$find = $finds['find'];
	if(strpos($text, $find) !== false) {
		$total_length = strlen($text);
		$length_before = strlen($text) - strlen(strstr($text, $find));
		$length_after = strlen(strstr(strstr($text, $find), "\n"));
		$before = substr($text, 0, $length_before);
		$after = strstr(strstr($text, $find), "\n");
		$match = substr($text, $length_before+strlen($find), $total_length-$length_before-$length_after-strlen($find));

		$matches = explode(",", $match);
		$return = ($before)."\n";
		$i = 0;
		$return .= $finds['bbview'];
		$actors = array();
		foreach($matches as $s) {
			$actors[] = ($i ? ' ' : '').'[b][url="'.$finds['link'].trim($s).'"]'.trim($s).'[/url][/b]';
			$i++;
		}
		$return .= implode(",", $actors);
		$return .= "\n".($after)."\n";
	return $return;
	} else {
		return ($text);
	}
}

function charcode($text, $code = "", $rev = false) {
	if(!empty($code)) {
		if(!$rev) {
			return iconv($code, config::Select('charset'), $text);
		} else {
			return iconv(config::Select('charset'), $code, $text);
		}
	} else {
		if(!$rev) {
			return iconv(iconv_charset($text), config::Select('charset'), $text);
		} else {
			return iconv(config::Select('charset'), iconv_charset($text), $text);
		}
	}
}

function ToTranslit($var, $rep = false, $norm = false) {
	if(empty($var)) {
		return "";
	}
	$translate = lang::get_lang("translate");
	if(!is_array($translate)) {
		$translate = array();
	}
	$var = strtolowers($var);
	$var = html_entity_decode($var);
	if($rep) {
		$lang = lang::get_lang('translate_en');
		if(!is_array($lang)) {
			$lang = array();
		}
		if($norm) {
			$lang = array_flip($lang);
		}
		$translate = array_merge($lang, array("\\" => "", "/" => "", "$" => "", "#" => "", "@" => "", "!" => "", "%" => "", "^" => "", "&quot;" => "", "&" => "", "*" => "", "(" => "", ")" => "", "?" => "", ":" => "", "=" => "", "+" => "", "\"" => "'", "№" => ""));
	} else {
		$translate = array_merge($translate, array(" " => "_", "\\" => "", "/" => "", "'" => "", "$" => "", "#" => "", "@" => "", "!" => "", "%" => "", "^" => "", "&quot;" => "", "&" => "", "*" => "", "(" => "", ")" => "", "," => "", "." => "", "?" => "", ":" => "", "=" => "", "+" => "", "\"" => "'", "№" => ""));
	}
return strtr($var, $translate);
}

function plural_form($arr) {
	if(!is_array($arr) || !isset($arr[1]) || !isset($arr[2]) || !is_numeric($arr[1]) || empty($arr[2])) {
		return false;
	}
	$del = " ";
	$n = $arr[1];
	$forms = $arr[2];
	if(strpos($forms, "|")===false) {
		$forms = array($forms);
	} else {
		$forms = explode("|", $forms);
	}
	if(isset($arr[4])) {
		$del = $arr[4];
	}
	if(sizeof($forms)==1) {
		$forms[1] = $forms[0];
		$forms[2] = $forms[1];
	} else if(sizeof($forms)==2) {
		$forms[2] = $forms[1];
	}
	return $n.$del.($n%10==1&&$n%100!=11?$forms[0]:($n%10>=2&&$n%10<=4&&($n%100<10||$n%100>=20)?$forms[1]:$forms[2]));
}

function _e() {
	$ret = func_get_args();
	$rets = modules::applyParam($ret, 'before', __FUNCTION__);
	$ret = call_user_func_array("modules::get_lang", $rets);
	if(is_bool($ret)) {
		$ret = $rets;
	}
	return $ret;
}

function check_invalid_utf8($string) {
	if(!is_string($string) || strlen($string)===0) {
		return '';
	}
	static $is_utf8 = null;
	if(!isset($is_utf8)) {
		$is_utf8 = in_array(config::Select("charset"), array('utf8', 'utf-8', 'UTF8', 'UTF-8'));
	}
	if(!$is_utf8) {
		return $string;
	}
	static $utf8_pcre = null;
	if(!isset($utf8_pcre)) {
		$utf8_pcre = @preg_match('/^./u', 'a');
	}
	if(!$utf8_pcre) {
		return $string;
	}
	if(1 === @preg_match('/^./us', $string)) {
		return $string;
	}
	return '';
}

function sanitize_callback($matches) {
    if(strpos($matches[0], '>')===false) {
		$safe_text = check_invalid_utf8($matches[0]);
		$safe_text = htmlspecialchars($safe_text, ENT_QUOTES);
		return $safe_text;
	}
    return $matches[0];
}

function sanitize_text($str, $keep_newlines = false) {
    $nstr = check_invalid_utf8($str);
    if(strpos($nstr, '<') !== false) {
        $nstr = preg_replace_callback('%<[^>]*?((?=<)|>|$)%', 'sanitize_callback', $nstr);
		$nstr = preg_replace('@<(script|style)[^>]*?>.*?</\\1>@si', '', $nstr);
		$nstr = strip_tags($nstr);
		$nstr = trim($nstr);
        $nstr = str_replace("<\n", "&lt;\n", $nstr);
    }
    if(!$keep_newlines) {
        $nstr = preg_replace('/[\r\n\t ]+/', ' ', $nstr);
    }
    $nstr = trim($nstr);
    $found = false;
    while(preg_match('/%[a-f0-9]{2}/i', $nstr, $match)) {
        $nstr = str_replace($match[0], '', $nstr);
        $found = true;
    }
    if($found) {
		$nstr = preg_replace('/ +/', ' ', $nstr);
        $nstr = trim($nstr);
    }
    return $nstr;
}

?>