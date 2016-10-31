<?php

class JSONHelper {
	
	function __construct($str) {
		if(!is_string($str)) {
			throw new Exception('First parameter is not correct', 6);
		}
		$str = json_decode($str, true);
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
	
	final public function __debugInfo() {
		return array();
	}
	
	final public function __get($k) {
		if(!isset($this->{$k})) {
			throw new Exception('Not Found', 6);
		}
		return $this->{$k};
	}
	
	final public function __set($k, $v) {
		$this->{$k} = $v;
	}
	
	final public function save() {
		return json_encode(get_object_vars($this));
	}
	
}

?>