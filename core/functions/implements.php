<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

if(!function_exists('getallheaders')) {
	function getallheaders() {
		$headers = array();
		foreach($_SERVER as $name => $value) {
			if(substr($name, 0, 5) == 'HTTP_') {
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			} else {
				$headers[$name] = $value;
			}
		}
		return $headers; 
	} 
}

if(!function_exists("RandomCompat_strlen")) {
	function RandomCompat_strlen($binary_string) {
		if(!is_string($binary_string)) {
			throw new TypeError('RandomCompat_strlen() expects a string');
		}
		if(function_exists('mb_strlen')) {
			return mb_strlen($binary_string, '8bit');
		}
		return strlen($binary_string);
	}
}

if(!function_exists('random_bytes')) {
    function random_bytes($bytes) {
		if(!function_exists("mcrypt_create_iv")) {
			throw new Exception('Mcrypt is not installed');
		}
        try {
			if(is_numeric($bytes)) {
				$bytes += 0;
			}
			if(is_float($bytes) && $bytes > ~PHP_INT_MAX && $bytes < PHP_INT_MAX) {
				$bytes = (int) $bytes;
			}
        } catch(Exception $ex) {
            throw new Exception('random_bytes(): $bytes must be an integer');
        }
        if($bytes < 1) {
            throw new Exception('Length must be greater than 0');
        }
        $buf = mcrypt_create_iv($bytes, MCRYPT_DEV_URANDOM);
        if($buf !== false && RandomCompat_strlen($buf) === $bytes) {
            return $buf;
        }
        throw new Exception('Could not gather sufficient random data');
    }
}

if(!function_exists("boolval")) {
	function boolval($val) {
		return (bool) $val;
	}
}

if(!function_exists('iterable_to_array')) {
	/**
	 * Copy the iterable into an array. If the iterable is already an array, return it.
	 *
	 * @param  array|\Traversable $iterable
	 * @return array
	 */
	function iterable_to_array($iterable) {
		return (is_array($iterable) ? $iterable : iterator_to_array($iterable));
	}
}
if(!function_exists('iterable_to_traversable')) {
	/**
	 * If the iterable is not intance of \Traversable, it is an array => convert it to an ArrayIterator.
	 *
	 * @param  $iterable
	 * @return \Traversable
	 */
	function iterable_to_traversable($iterable) {
		if($iterable instanceof Traversable) {
			return $iterable;
		} elseif(is_array($iterable)) {
			return new ArrayIterator($iterable);
		} else {
			throw new \InvalidArgumentException(sprintf('Expected array or \\Traversable, got %s', (is_object($iterable) ? get_class($iterable) : gettype($iterable))));
		}
	}
}

if(!function_exists("hex2bin")) {
	function hex2bin($hexstr) {
		$n = strlen($hexstr);
		$sbin = "";
		$i = 0;
		while($i<$n) {
			$a = substr($hexstr, $i, 2);
			$c = pack("H*",$a);
			if($i==0) {
				$sbin = $c;
			} else {
				$sbin .= $c;
			}
			$i+=2;
		}
		return $sbin;
	}
}

if(!function_exists('is_countable')) {
    function is_countable($var) {
    	return Validate::is_countable($var);
    }
}

if(!function_exists('array_key_first')) {
    function array_key_first(array $array) {
    	return key($array);
    }
}
if(!function_exists('array_key_last')) {
    function array_key_last(array $array) {
    	end($array);
    	return key($array);
    }
}

if(!function_exists('is_iterable')) {
	/**
	 * Check wether or not a variable is iterable (i.e array or \Traversable)
	 *
	 * @param  array|\Traversable $iterable
	 * @return bool
	 */
	function is_iterable($iterable) {
		return (is_array($iterable) || $iterable instanceof \Traversable);
	}
}

if(!function_exists("hrtime")) {
	$startAt = 1533462603;
	function hrtime($asNum = false) {
		global $startAt;
		$ns = microtime(false);
		$s = substr($ns, 11) - $startAt;
		$ns = 1E9 * (float) $ns;
		if($asNum) {
			$ns += $s * 1E9;
			return \PHP_INT_SIZE === 4 ? $ns : (int) $ns;
		}
		return array($s, (int) $ns);
	}
}

if(version_compare(PHP_VERSION, '7.4.0', '<')) {
	function password_algos()  {
		$algos = array(PASSWORD_BCRYPT);
		if(defined('PASSWORD_ARGON2I')) {
			$algos[] = PASSWORD_ARGON2I;
		}
		if(defined('PASSWORD_ARGON2ID')) {
			$algos[] = PASSWORD_ARGON2ID;
		}
		return $algos;
	}
}

if(!function_exists("array_is_list")) {
	function array_is_list($array) {
		if ([] === $array || $array === array_values($array)) {
			return true;
		}
		$nextKey = -1;
		foreach ($array as $k => $v) {
			if ($k !== ++$nextKey) {
				return false;
			}
		}
		return true;
	}
}