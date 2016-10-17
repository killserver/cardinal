<?php

class Validate {
	
	final public static function color($str) {
		return (bool) preg_match('/^#?+[0-9a-f]{3}(?:[0-9a-f]{3})?$/iD', $str);
	}
	
	final public static function matches($array, $field, $match) {
		return ($array[$field] === $array[$match]);
	}
	
	final public static function range($number, $min, $max) {
		return ($number >= $min && $number <= $max);
	}
	
	final public static function CheckType($val, $type) {
		$types = gettype($val);
		if(self::equals($types, $type)) {
			return true;
		} else {
			return false;
		}
	}
	
	final public static function numeric($str) {
		$arr = array_values(localeconv());
		return (bool) preg_match('/^-?+(?=.*[0-9])[0-9]*+'.preg_quote($arr[0]).'?+[0-9]*+$/D', (string) $str);
	}
	
	final public static function date($str) {
		return (strtotime($str) !== false);
	}
	
	final public static function typeFile($file, $type) {
		if(!is_array($file) || !isset($file['type']) || !isset($file['error']) || $file['error'] != 0) {
			return false;
		}
		if(!isset($file['error']) || !isset($file['name']) || !isset($file['type']) || !isset($file['tmp_name']) || !isset($file['size'])) {
			return false;
		}
		$exp = explode("/", $file['type']);
		$rt = current($exp);
		$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
		if(is_string($type)) {
			return (($rt === $type) || ($ext === $type));
		} elseif(is_array($type)) {
			return (in_array($rt, $type) || in_array($ext, $type));
		}
	}
	
	final public static function phone($number, $lengths = "") {
		if(!is_array($lengths)) {
			$lengths = array(7, 10, 11);
		}
		$number = preg_replace('/\D+/', '', $number);
		return in_array(strlen($number), $lengths);
	}
	
	final public static function ip($ip, $allow_private = true) {
		$flags = FILTER_FLAG_NO_RES_RANGE;
		if($allow_private === false) {
			$flags = $flags | FILTER_FLAG_NO_PRIV_RANGE;
		}
		return (bool) filter_var($ip, FILTER_VALIDATE_IP, $flags);
	}
	
	final public static function url($url) {
		// Based on http://www.apps.ietf.org/rfc/rfc1738.html#sec-5
		if(!preg_match('~^[-a-z0-9+.]++://(?:[-a-z0-9$_.+!*\'(),;?&=%]++(?::[-a-z0-9$_.+!*\'(),;?&=%]++)?@)?(?:\d{1,3}+(?:\.\d{1,3}+){3}+|((?!-)[-a-z0-9]{1,63}+(?<!-)(?:\.(?!-)[-a-z0-9]{1,63}+(?<!-)){0,126}+))(?::\d{1,5}+)?(?:/.*)?$~iDx', $url, $matches)) {
			return false;
		}
		if(!isset($matches[1])) {
			return true;
		}
		if(strlen($matches[1]) > 253) {
			return false;
		}
		$tld = ltrim(substr($matches[1], (int) strrpos($matches[1], '.')), '.');
		return ctype_alpha($tld[0]);
	}
	
	final public static function email($email, $strict = false) {
		if($strict === true) {
			$qtext = '[^\\x0d\\x22\\x5c\\x80-\\xff]';
			$dtext = '[^\\x0d\\x5b-\\x5d\\x80-\\xff]';
			$atom = '[^\\x00-\\x20\\x22\\x28\\x29\\x2c\\x2e\\x3a-\\x3c\\x3e\\x40\\x5b-\\x5d\\x7f-\\xff]+';
			$pair = '\\x5c[\\x00-\\x7f]';
			$domain_literal = "\\x5b(".$dtext."|".$pair.")*\\x5d";
			$quoted_string = "\\x22(".$qtext."|".$pair.")*\\x22";
			$sub_domain = "(".$atom."|".$domain_literal.")";
			$word = "(".$atom."|".$quoted_string.")";
			$domain = $sub_domain."(\\x2e".$sub_domain.")*";
			$local_part = $word."(\\x2e".$word.")*";
			$expression = "/^".$local_part."\\x40".$domain."$/D";
		} else {
			$expression = '/^[-_a-z0-9\'+*$^&%=~!?{}]++(?:\.[-_a-z0-9\'+*$^&%=~!?{}]+)*+@(?:(?![-.])[-a-z0-9.]+(?<![-.])\.[a-z]{2,6}|\d{1,3}(?:\.\d{1,3}){3})(?::\d++)?$/iD';
		}

		return (bool) preg_match($expression, (string) $email);
	}
	
	final public static function email_domain($email) {
		return (bool) checkdnsrr(preg_replace('/^[^@]++@/', '', $email), 'MX');
	}
	
	final public static function equals($value, $required) {
		return ($value === $required);
	}
	
	final public static function min_length($value, $length) {
		return strlen($value) >= $length;
	}

	final public static function max_length($value, $length) {
		return strlen($value) <= $length;
	}
	
	final public static function not_empty($value) {
		if(is_object($value) && $value instanceof ArrayObject) {
			$value = $value->getArrayCopy();
		}
		if(is_object($value) && $value instanceof DBObject) {
			$value = $value->getArray();
		}
		$null = array(null, false, '', array());
		return (!in_array($value, $null, true));
	}
	
}

?>