<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

class Validate {
	
	public static $host = "";
	
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
	
	final public static function session_valid_id($session_id) {
		return preg_match('/^[-,a-zA-Z0-9]{1,128}$/', $session_id) > 0;
	}
	
	final public static function Redirect($location, $default = false) {
		if(empty(self::$host)) {
			return $default;
		}
		$location = trim($location);
		// browsers will assume 'http' is your protocol, and will obey a redirect to a URL starting with '//'
		if(substr($location, 0, 2) == '//') {
			$location = 'http:' . $location;
		}

		$orLocation = $location;

		// In php 5 parse_url may fail if the URL query part contains http://, bug #38143
		$cut = false;
		if(strpos($location, '?')!==false) {
			$cut = strpos($location, '?');
		}
		$test = ($cut ? substr($location, 0, $cut) : $location);

		try {
			$lp = parse_url($test);
		} catch(Exception $ex) {
			return $default;
		}

		// Give up if malformed URL
		if($lp === false) {
			return $default;
		}

		// Allow only http and https schemes. No data:, etc.
		if(isset($lp['scheme']) && !($lp['scheme']=='http' || $lp['scheme']=='https')) {
			return $default;
		}

		// Reject if certain components are set but host is not. This catches urls like https:host.com for which parse_url does not set the host field.
		if(!isset($lp['host']) && (isset($lp['scheme']) || isset($lp['user']) || isset($lp['pass']) || isset($lp['port']))) {
			return $default;
		}

		// Reject malformed components parse_url() can return on odd inputs.
		if((isset($lp['user']) && strpbrk($lp['user'], ':/?#@')) || (isset($lp['pass']) && strpbrk($lp['pass'], ':/?#@')) || (isset($lp['host']) && strpbrk($lp['host'], ':/?#@'))) {
			return $default;
		}

		$parses = parse_url(self::$host);
		$host = isset($lp['host']) ? $lp['host'] : '';
		$allowed_hosts = array($parses['host']);
		if(isset($lp['host']) && (!in_array($lp['host'], $allowed_hosts) && $lp['host'] != strtolower($parses['host']))) {
			$location = $default;
		}
		if($orLocation == $location) {
			return true;
		} else {
			return false;
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
		if(!defined("FILTER_FLAG_NO_RES_RANGE") || !defined("FILTER_FLAG_NO_PRIV_RANGE") || !defined("FILTER_VALIDATE_IP")) {
			return self::validIp($ip);
		}
		$flags = FILTER_FLAG_NO_RES_RANGE;
		if($allow_private === false) {
			$flags = $flags | FILTER_FLAG_NO_PRIV_RANGE;
		}
		return (bool) filter_var($ip, FILTER_VALIDATE_IP, $flags);
	}
	
	final public static function validIp($ip) {
		if(!ip2long($ip)) {//IPv6
			return true;
		}
		if(!empty($ip) && $ip==long2ip(ip2long($ip))) {
			// reserved IANA IPv4 addresses
			// http://www.iana.org/assignments/ipv4-address-space
			$reserved_ips = array(
				array('192.0.2.0', '192.0.2.255'),
				array('192.168.0.0', '192.168.255.255'),
				array('255.255.255.0', '255.255.255.255')
			);
			$ret = true;
			foreach($reserved_ips as $r) {
				$min = ip2long($r[0]);
				$max = ip2long($r[1]);
				if((ip2long($ip)>=$min) && (ip2long($ip)<=$max)) {
					$ret = false;
					break;
				}
			}
			return $ret;
		} else {
			return false;
		}
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