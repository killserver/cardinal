<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

/**
 * Class Validate
 */
class Validate {

	/**
	 * @var string Validated host
	 */
	public static $host = "";

	/**
	 * Check color in hex
	 * @param string $str Needed color
	 * @return bool Result checking
	 */
	final public static function color($str) {
		return (bool) preg_match('/^#?+[0-9a-f]{3}(?:[0-9a-f]{3})?$/iD', $str);
	}

	/**
	 * Check element in array equals value and type
	 * @param array $array Needed array
	 * @param string $field Check fields in array
	 * @param string $match Check fields in array
	 * @return bool Result checking
	 */
	final public static function matches($array, $field, $match) {
		return (isset($array[$field]) && isset($array[$match])) && ($array[$field] === $array[$match]);
	}

	/**
	 * Check if number range inside min or max
	 * @param int $number Needed check number
	 * @param int $min Need minimal value
	 * @param int $max Need maximal value
	 * @return bool Result checking
	 */
	final public static function range($number, $min, $max) {
		return ($number >= $min && $number <= $max);
	}

	/**
	 * Check element of type
	 * @param mixed $val Needed element for checking
	 * @param string $type Type for checking
	 * @return bool Result checking
	 */
	final public static function CheckType($val, $type) {
		$types = gettype($val);
		if(self::equals($types, $type)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Check element if numeric
	 * @param mixed $str Needed numeric
	 * @return bool Result checking
	 */
	final public static function numeric($str) {
		$arr = array_values(localeconv());
		return (bool) preg_match('/^-?+(?=.*[0-9])[0-9]*+'.preg_quote($arr[0]).'?+[0-9]*+$/D', (string) $str);
	}

	/**
	 * Checking if element date
	 * @param string $str Needed string
	 * @return bool Result checking
	 */
	final public static function date($str) {
		return (strtotime($str) !== false);
	}

	/**
	 * Checking file
	 * @param array $file Array $_FILES
	 * @param string $type Checked type upload file
	 * @return bool Result checking
	 */
	final public static function typeFile($file, $type) {
		if(!is_array($file) || !isset($file['type']) || (isset($file['error']) && $file['error'] != 0)) {
			return false;
		}
		if((isset($file['error']) && $file['error'] != 0) || !isset($file['name']) || !isset($file['type']) || !isset($file['tmp_name']) || !isset($file['size'])) {
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

	/**
	 * Checking ID session
	 * @param string $session_id Session ID
	 * @return bool Result checking
	 */
	final public static function session_valid_id($session_id) {
		return preg_match('/^[-,a-zA-Z0-9]{1,128}$/', $session_id) > 0;
	}

	/**
	 * Checking redirect equals local domain
	 * @param string $location Location for checking
	 * @param bool $default If return error try return default
	 * @return bool Result checking
	 */
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

	/**
	 * Checking phone
	 * @param string $number Needed number for checking
	 * @param array $lengths Set needed length number
	 * @return bool Result checking
	 */
	final public static function phone($number, $lengths = array()) {
		if(!is_array($lengths) && sizeof($lengths)==0) {
			$lengths = array(7, 10, 11);
		}
		$number = preg_replace('/\D+/', '', $number);
		return in_array(strlen($number), $lengths);
	}

	/**
	 * Checking IP address
	 * @param string $ip Needed string for find IP address
	 * @param bool $allow_private Allow private range
	 * @return bool Result checking
	 */
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

	/**
	 * Validate IP address(old version)
	 * @param string $ip Checking IP
	 * @return bool Result checking
	 */
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

	/**
	 * Needed link
	 * @param string $url Checking link
	 * @return bool Result checking
	 */
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

	/**
	 * Checking email address
	 * @param string $email Needed checking email
	 * @param bool $strict Check structure
	 * @return bool Result checking
	 */
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

	/**
	 * Check email if used address exists on domain
	 * @param string $email Checking email address
	 * @return bool Result checking
	 */
	final public static function email_domain($email) {
		return (bool) checkdnsrr(preg_replace('/^[^@]++@/', '', $email), 'MX');
	}

	/**
	 * Check two elements on equals
	 * @param mixed $value First element for checking
	 * @param mixed $required Second element for checking
	 * @return bool Result checking
	 */
	final public static function equals($value, $required) {
		return ($value === $required);
	}

	/**
	 * Checking if length in string not min or equal length
	 * @param string $value Element for checking
	 * @param int $length Min length
	 * @return bool Result checking
	 */
	final public static function min_length($value, $length) {
		return strlen($value) >= $length;
	}

	/**
	 * Checking if length in string not max or equal length
	 * @param string $value Element for checking
	 * @param int $length Max length
	 * @return bool Result checking
	 */
	final public static function max_length($value, $length) {
		return strlen($value) <= $length;
	}

	/**
	 * Checking on empty
	 * @param mixed $value Needed value
	 * @return bool Result checking
	 */
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

	/**
	 * Validates a number according to Luhn check algorithm
	 *
	 * This function checks given number according Luhn check
	 * algorithm. It is published on several places. See links for details.
	 *
	 * @param string $number number to check
	 *
	 * @link http://www.webopedia.com/TERM/L/Luhn_formula.html
	 * @link http://www.merriampark.com/anatomycc.htm
	 * @link http://hysteria.sk/prielom/prielom-12.html#3 (Slovak language)
	 * @link http://www.speech.cs.cmu.edu/~sburke/pub/luhn_lib.html (Perl lib)
	 *
	 * @return bool    TRUE if number is valid, FALSE otherwise
	 * @access public
	 * @static
	 */
	final public static function CreditCardLuhn($number) {
		$len_number = strlen($number);
		$sum = 0;
		for($k=$len_number%2;$k<$len_number;$k+=2) {
			if((intval($number[$k]) * 2) > 9) {
				$sum += (intval($number[$k])*2)-9;
			} else {
				$sum += intval($number[$k])*2;
			}
		}
		for($k =($len_number%2)^1;$k<$len_number;$k +=2) {
			$sum += intval($number[$k]);
		}
		return ($sum % 10) ? false : true;
	}
	
	/**
	 * Validates a credit card number
	 *
	 * If a type is passed, the card will be checked against it.
	 * This method only checks the number locally. No banks or payment
	 * gateways are involved.
	 * This method doesn't guarantee that the card is legitimate. It merely
	 * checks the card number passes a mathematical algorithm.
	 *
	 * @param string $creditCard number (spaces and dashes tolerated)
	 * @param string $cardType   type/brand of card (case insensitive)
	 *               "MasterCard", "Visa", "AMEX", "AmericanExpress",
	 *               "American Express", "Diners", "DinersClub", "Diners Club",
	 *               "CarteBlanche", "Carte Blanche", "Discover", "JCB",
	 *               "EnRoute", "Eurocard", "Eurocard/MasterCard".
	 *
	 * @return bool   TRUE if number is valid, FALSE otherwise
	 * @access public
	 * @static
	 * @see Luhn()
	 */
	final public static function CreditCardNumber($creditCard, $cardType = "") {
		$cc = str_replace(array('-', ' '), '', $creditCard);
		if((($len = strlen($cc)) < 13) || (strspn($cc, '0123456789') != $len)) {
			return false;
		}
		// Only apply the Luhn algorithm for cards other than enRoute
		// So check if we have a enRoute card now
		if((strlen($cc) != 15) || ((substr($cc, 0, 4) != '2014') &&  (substr($cc, 0, 4) != '2149'))) {
			if(!self::CreditCardLuhn($cc)) {
				return false;
			}
		}
		if(is_string($cardType)) {
			return self::CreditCardType($cc, $cardType);
		}
		return true;
	}

	/**
	 * Validates the credit card number against a type
	 *
	 * This method only checks for the type marker. It doesn't
	 * validate the card number. Some card "brands" share the same
	 * numbering system, so checking the card type against any of the
	 * sister brand will return the same result.
	 *
	 * For instance, if a $card is a MasterCard, type($card, 'EuroCard')
	 * will also return true.
	 *
	 * @param string $creditCard number (spaces and dashes tolerated)
	 * @param string $cardType   type/brand of card (case insensitive)
	 *               "MasterCard", "Visa", "AMEX", "AmericanExpress",
	 *               "American Express", "Diners", "DinersClub", "Diners Club",
	 *               "CarteBlanche", "Carte Blanche", "Discover", "JCB",
	 *               "EnRoute", "Eurocard", "Eurocard/MasterCard".
	 *
	 * @return bool   TRUE is type matches, FALSE otherwise
	 * @access public
	 * @static
	 * @link http://www.beachnet.com/~hstiles/cardtype.html
	 */
	final public static function CreditCardType($creditCard, $cardType) {
		switch(strtoupper($cardType)) {
			case 'MASTERCARD':
			case 'EUROCARD':
			case 'EUROCARD/MASTERCARD':
				$regex = '^(5[1-5]\d{4}|677189)\d{10}$|^2(?:2(?:2[1-9]|[3-9]\d)|[3-6]\d\d|7(?:[01]\d|20))\d{12}';
				break;
			case 'VISA':
				$regex = '4([0-9]{12}|[0-9]{15})';
				break;
			case 'AMEX':
			case 'AMERICANEXPRESS':
			case 'AMERICAN EXPRESS':
				$regex = '3[47][0-9]{13}';
				break;
			case 'DINERS':
			case 'DINERSCLUB':
			case 'DINERS CLUB':
			case 'CARTEBLANCHE':
			case 'CARTE BLANCHE':
				$regex = '3(0[0-5][0-9]{11}|[68][0-9]{12})';
				break;
			case 'DISCOVER':
				$regex = '6011[0-9]{12}';
				break;
			case 'JCB':
				$regex = '(3[0-9]{15}|(2131|1800)[0-9]{11})';
				break;
			case 'ENROUTE':
				$regex = '2(014|149)[0-9]{11}';
				break;
			default:
				return false;
		}
		$regex = '/^' . $regex . '$/';
		$cc = str_replace(array('-', ' '), '', $creditCard);
		return (bool)preg_match($regex, $cc);
	}

	/**
	 * Validates a card verification value format
	 *
	 * This method only checks for the format. It doesn't
	 * validate that the value is the one on the card.
	 *
	 * CVV is also known as
	 *  - CVV2 Card Validation Value 2 (Visa)
	 *  - CVC  Card Validation Code (MasterCard)
	 *  - CID  Card Identification (American Express and Discover)
	 *  - CIN  Card Identification Number
	 *  - CSC  Card Security Code
	 *
	 * Important information regarding CVV:
	 *    If you happen to have to store credit card information, you must
	 *    NOT retain the CVV after transaction is complete. Usually this
	 *    means you cannot store it in a database, not even in an encrypted
	 *    form.
	 *
	 * This method returns FALSE for card types that don't support CVV.
	 *
	 * @param string $cvv      value to verify
	 * @param string $cardType type/brand of card (case insensitive)
	 *               "MasterCard", "Visa", "AMEX", "AmericanExpress",
	 *               "American Express", "Discover", "Eurocard/MasterCard",
	 *               "Eurocard"
	 *
	 * @return bool   TRUE if format is correct, FALSE otherwise
	 * @access public
	 * @static
	 */
	final public static function CreditCardTypeCvv($cvv, $cardType) {
		switch(strtoupper($cardType)) {
			case 'MASTERCARD':
			case 'EUROCARD':
			case 'EUROCARD/MASTERCARD':
			case 'VISA':
			case 'DISCOVER':
				$digits = 3;
			break;
			case 'AMEX':
			case 'AMERICANEXPRESS':
			case 'AMERICAN EXPRESS':
				$digits = 4;
			break;
			default:
				return false;
		}
		if((strlen($cvv) == $digits) && (strspn($cvv, '0123456789') == $digits)) {
			return true;
		}
		return false;
	}
	
	/**
	 * Validates a car registration number
	 *
	 * @param string $reg the registration number
	 *
	 * @return bool
	 * @see http://pl.wikipedia.org/wiki/Polskie_tablice_rejestracyjne
	 */
	final public static function carReg($reg) {
		$pregs = array(
			// 2 letter district
			"[a-z]{2}\d{5}",
			"[a-z]{2}\d{4}[a-z]{1}",
			"[a-z]{2}\d{3}[a-z]{2}",
			"[a-z]{2}\d{1}[a-z]{1}\d{3}",
			"[a-z]{2}\d{1}[a-z]{2}\d{2}",
			// 3 letter district
			"[a-z]{3}[a-z]{1}\d{3}",
			"[a-z]{3}\d{2}[a-z]{2}",
			"[a-z]{3}\d{1}[a-z]{1}\d{2}",
			"[a-z]{3}\d{2}[a-z]{1}\d{1}",
			"[a-z]{3}\d{1}[a-z]{2}\d{1}",
			"[a-z]{3}[a-z]{2}\d{2}",
			"[a-z]{3}\d{5}",
			"[a-z]{3}\d{4}[a-z]{1}",
			"[a-z]{3}\d{3}[a-z]{2}",
			"[a-z]{3}[a-z]{1}\d{2}[a-z]{1}",
			"[a-z]{3}[a-z]{1}\d{1}[a-z]{2}",
			// bikes
			"[a-z]{2}\d{4}",
			"[a-z]{2}\d{3}[a-z]{1}",
			"[a-z]{3}[a-z]{1}\d{3}", // deprecated
			// temporaty
			"[a-z]{1}\d{1}\d{4}",
			"[a-z]{1}\d{1}\d{3}B",
			// individual
			"[a-z]{1}\d{1}[a-z]{3}[a-z0-9]{0,2}",
			// classic
			"[a-z]{2}\d{2}[a-z]{1}",
			"[a-z]{2}\d{3}",
			"[a-z]{3}\d{1}[a-z]{1}",
			"[a-z]{3}\d{2}",
			"[a-z]{3}[a-z]{1}\d{1}",
			// diplomatic
			"W\d{6}",
			// military
			"U[abcdegijk]\d{4,5}T?",
			// special services
			"H[apmwkbcsn][a-z][a-z]{1}\d{3}",
			"H[apmwkbcsn][a-z]\d{2}[a-z]{2}");
		foreach($pregs as $preg) {
			if(preg_match('/^'.$preg.'$/i', $reg)) {
				return true;
			}
		}
		return false;
	}
	
	final public static function json($str) {
		return $str === '""' || $str === '[]' || $str === '{}' || $str[0] === '"' && substr($str, -1) === '"' || $str[0] === '[' && substr($str, -1) === ']' || $str[0] === '{' && substr($str, -1) === '}';
	}
	
	final public static function is_serialized($data) {
		if(!is_string($data)) {
			return false;
		}
		$data = trim($data);
		if('N;' == $data) {
			return true;
		}
		if(!preg_match('/^([adObis]):/', $data, $badions)) {
			return false;
		}
		switch($badions[1]) {
			case 'a':
			case 'O':
			case 's':
				if (preg_match("/^".$badions[1].":[0-9]+:.*[;}]\$/s", $data)) {
					return true;
				}
			break;
			case 'b':
			case 'i':
			case 'd':
				if(preg_match("/^".$badions[1].":[0-9.E-]+;\$/", $data)) {
					return true;
				}
			break;
		}
		return false;
	}

	final public static function is_xml($string) {
		if(!defined('LIBXML_COMPACT')) {
			new Exception('libxml is required to use is_xml()');
			die();
		}
		$internal_errors = libxml_use_internal_errors();
		libxml_use_internal_errors(true);
		$result = simplexml_load_string($string) !== false;
		libxml_use_internal_errors($internal_errors);
		return $result;
	}

	final public static function is_html($string) {
		return strlen(strip_tags($string)) < strlen($string);
	}

	final public static function is_uuid4($uuid) {
		if(!is_string($uuid)) {
			return false;
		}
		return (bool) preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $uuid);
	}

	final private static function mbstring_binary_safe_encoding($reset = false) {
	    static $encodings = array();
	    static $overloaded = null;
	    if(is_null($overloaded)) {
	        $overloaded = function_exists('mb_internal_encoding') && (ini_get('mbstring.func_overload') & 2);
	    }
	    if($overloaded===false) {
	        return;
	    }
	    if(!$reset) {
	        $encoding = mb_internal_encoding();
	        array_push($encodings, $encoding);
	        mb_internal_encoding('ISO-8859-1');
	    }
	    if($reset && $encodings) {
	        $encoding = array_pop($encodings);
	        mb_internal_encoding($encoding);
	    }
	}

	final public static function is_utf8($str) {
		self::mbstring_binary_safe_encoding();
		$length = strlen($str);
		self::mbstring_binary_safe_encoding(true);
		for($i=0;$i<$length;$i++) {
			$c = ord($str[$i]);
			if($c < 0x80) { // 0bbbbbbb
				$n = 0;
			} else if(($c & 0xE0) == 0xC0) { // 110bbbbb
				$n = 1;
			} else if(($c & 0xF0) == 0xE0) { // 1110bbbb
				$n = 2;
			} else if(($c & 0xF8) == 0xF0) { // 11110bbb
				$n = 3;
			} else if(($c & 0xFC) == 0xF8) { // 111110bb
				$n = 4;
			} else if(($c & 0xFE) == 0xFC) { // 1111110b
				$n = 5;
			} else { // Does not match any model
				return false;
			}
			for($j=0;$j<$n;$j++) { // n bytes matching 10bbbbbb follow ?
				if((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80)) {
					return false;
				}
			}
		}
		return true;
	}
	
	final public static function is_ascii($str) {
		if(is_array($str)) {
			$str = implode($str);
		}
		return !preg_match('/[^\x00-\x7F]/S', $str);
	}
	
	final public static function is_countable($var) {
		return is_array($var) || $var instanceof Countable || $var instanceof ResourceBundle || $var instanceof SimpleXmlElement;
	}
	
}

?>