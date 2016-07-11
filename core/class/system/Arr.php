<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

final class Arr {
	
	private static $array = array();
	private static $empty = false;
	
	public function __construct(array $arr) {
		self::$array = $arr;
	}
	
	final public static function get($key, $default = "") {
		if(self::$empty) {
			return isset(self::$array[$key]) && !empty(self::$array[$key]) ? self::$array[$key] : $default;
		} else {
			return isset(self::$array[$key]) ? self::$array[$key] : $default;
		}
	}
	
	final public static function found(array $keys, $default = "") {
		$found = array();
		foreach($keys as $key) {
			if(self::$empty) {
				$found[$key] = isset(self::$array[$key]) && !empty(self::$array[$key]) ? self::$array[$key] : $default;
			} else {
				$found[$key] = isset(self::$array[$key]) ? self::$array[$key] : $default;
			}
		}
		return $found;
	}
	
	final public static function foundValues($key, $array = array()) {
		$values = array();
		if(sizeof($array)>0) {
			foreach($array as $row) {
				if(!self::$empty && isset($row[$key])) {
					$values[] = $row[$key];
				} else if(isset($row[$key]) && !empty($row[$key])) {
					$values[] = $row[$key];
				}
			}
		} else {
			foreach(self::$array as $row) {
				if(!self::$empty && isset($row[$key])) {
					$values[] = $row[$key];
				} else if(isset($row[$key]) && !empty($row[$key])) {
					$values[] = $row[$key];
				}
			}
		}
		return $values;
	}
	
	
	final public static function map($callback, $array = array()) {
		if(sizeof($array)>0) {
			foreach(self::$array as $key => $val) {
				if(is_array($val)) {
					self::$array[$key] = self::map($callback, $val);
				} else {
					self::$array[$key] = call_user_func($callback, $val);
				}
			}
			return self::array;
		} else {
			foreach($array as $key => $val) {
				if(is_array($val)) {
					$array[$key] = self::map($callback, $val);
				} else {
					$array[$key] = call_user_func($callback, $val);
				}
			}
			return $array;
		}
	}
	
	final public static function GetAll() {
		return self::$array;
	}

	/**
	 * Call function as object method
	 * @access public
	 * @param string $name Name method for static call
	 * @param array $params Params for static call
	 * @return mixed Result work static method
     */
	final public function __call($name, array $params) {
		$new = __METHOD__;
		return self::$new($name, $params);
	}

	/**
	 * Call function as static method
	 * @access public
	 * @param string $name Name method for static call
	 * @param array $params Params for static call
	 * @return mixed Result work static method
     */
	final public static function __callStatic($name, array $params) {
		$new = __METHOD__;
		return self::$new($name, $params);
	}

	/**
	 * Safe template from clone
	 * @access private
	 * @return bool Ban from clone class
	 */
	final private function __clone() {
		return false;
	}
	
	final public function __get($name) {
		if(self::$empty) {
			return (isset($this->array[$name]) && !empty($this->array[$name]) ? $this->array[$name] : "");
		} else {
			return (isset($this->array[$name]) ? $this->array[$name] : "");
		}
	}
	
	final public function __set($n, $v) {
		$this->array[$n] = $v;
	}
	
}

?>