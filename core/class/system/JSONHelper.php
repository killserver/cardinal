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
			throw new Exception('First parameter is not correct', 6);
		}
		$str = json_decode($str, true);
		if(null === $result) {
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
    final public function save() {
		return json_encode(get_object_vars($this));
	}
	
}

?>