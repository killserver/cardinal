<?php

/**
 * Class JSONHelper
 */
class JSONHelper {

    /**
     * JSONHelper constructor.
     * @param string $str Needed string for json decode
     * @throws Exception If first parameters is not string
     */
    function __construct($str) {
		if(!is_string($str)) {
			errorHeader();
			throw new Exception('First parameter is not correct', 6);
		}
		$str = json_decode($str, true);
		if(null === $str) {
			errorHeader();
			throw new Exception(json_last_error(), 6);
		}
		$list = get_object_vars($this);
		if(sizeof($list)==0) {
			$list = $str;
		}
		foreach($str as $k => $v) {
			if(array_key_exists($k, $list)) {
				$this->{$k} = $v;
			}
		}
	}

    /**
     * Block var_dump and print_r
     * @return array Empty array
     */
    final public function __debugInfo() {
		return array();
	}

    /**
     * Try get element in json array
     * @param mixed $k Needed element in json
     * @return mixed Result element in json
     * @throws Exception If element not found in json
     */
    final public function __get($k) {
		if(!isset($this->{$k})) {
			errorHeader();
			throw new Exception('Not Found', 6);
		}
		return $this->{$k};
	}

    /**
     * Try set element in json
     * @param mixed $k Key element in json
     * @param mixed $v Value element in json
     */
    final public function __set($k, $v) {
		$this->{$k} = $v;
	}

    /**
     * Save all elements in object to string
     * @return string This object to string
     */
    final public function save($normal = false) {
    	$data = CardinalJSON::save(get_object_vars($this));
    	if($normal) {
    		$data = self::normalizer($data);
    	}
		return $data;
	}

	final public function encode() {
		return call_user_func_array(array($this, "save"), func_get_args());
	}
	
	final public static function normalizer($data) {
		$arr = array();
		$tab = 1;
		$d = false;
		for($f=0;$f<strlen($data);$f++) {
			$bytes = $data[$f];
			if($d && $bytes === $d) {
				$data[$f - 1] !== "\\" && ($d = !1);
			} else if(!$d && ($bytes === '"' || $bytes === "'")) {
				$d = $bytes;
			} else if(!$d && ($bytes === " " || $bytes === "\t")) {
				$bytes = "";
			} else if(!$d && $bytes === ":") {
				$bytes = $bytes." ";
			} else if(!$d && $bytes === ",") {
				$bytes = $bytes."\n";
				$bytes = str_pad($bytes, ($tab * 2), " ");
			} else if(!$d && ($bytes === "[" || $bytes === "{")) {
				$tab++;
				$bytes .= "\n";
				$bytes = str_pad($bytes, ($tab * 2), " ");
			} else if(!$d && ($bytes === "]" || $bytes === "}")) {
				$tab--;
				$bytes = str_pad("\n", ($tab * 2), " ").$bytes;
			}
			array_push($arr, $bytes);
		}
		return implode("", $arr);
	}
	
}

?>