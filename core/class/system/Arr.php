<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

class Arr {
	
	private static $array = array();
	private static $empty = false;
	private static $separ = "";
	
	public function __construct(array $arr) {
		self::$array = $arr;
	}
	
	final public static function allowEmpty($empty) {
		self::$empty = $empty;
	}
	
	final public static function get($arr, $key = "", $default = "") {
		if(is_array($arr)) {
			return call_user_func_array(__CLASS__."::getArr", func_get_args());
		} else {
			return call_user_func_array(__CLASS__."::getSelf", func_get_args());
		}
	}
	
	final private static function getArr(array $arr, $key, $default = "") {
		if(self::$empty) {
			return isset($arr[$key]) && !empty($arr[$key]) ? $arr[$key] : $default;
		} else {
			return isset($arr[$key]) ? $arr[$key] : $default;
		}
	}
	
	final private static function getSelf($key, $default = "") {
		if(self::$empty) {
			return isset(self::$array[$key]) && !empty(self::$array[$key]) ? self::$array[$key] : $default;
		} else {
			return isset(self::$array[$key]) ? self::$array[$key] : $default;
		}
	}
	
	final public static function found($arr, $keys = "", $default = "") {
		if(is_array($arr)) {
			return call_user_func_array(__CLASS__."::foundArr", func_get_args());
		} else {
			return call_user_func_array(__CLASS__."::foundSelf", func_get_args());
		}
	}
	
	final private static function foundArr(array $arr, array $keys, $default = "") {
		$found = array();
		foreach($keys as $key) {
			if(self::$empty) {
				$found[$key] = isset($arr[$key]) && !empty($arr[$key]) ? $arr[$key] : $default;
			} else {
				$found[$key] = isset($arr[$key]) ? $arr[$key] : $default;
			}
		}
		return $found;
	}
	
	final private static function foundSelf(array $keys, $default = "") {
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
	
	final public static function push($arr, $mixed = "") {
		if(is_array($arr)) {
			return call_user_func_array(__CLASS__."::pushArr", func_get_args());
		} else {
			return call_user_func_array(__CLASS__."::pushSelf", func_get_args());
		}
	}
	
	final private static function pushArr($arr, $mixed) {
		array_push($arr, $mixed);
		return $arr;
	}
	
	final private static function pushSelf($mixed) {
		array_push(self::$array, $mixed);
		return self::$array;
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
	
	final public static function map($array, $callback = "") {
		if(is_array($array)) {
			return call_user_func_array(__CLASS__."::mapArr", func_get_args());
		} else {
			return call_user_func_array(__CLASS__."::mapSelf", func_get_args());
		}
	}
	
	final private static function mapSelf($callback, $array = array()) {
		foreach(self::$array as $key => $val) {
			if(is_array($val)) {
				self::$array[$key] = self::mapSelf($callback, $val);
			} else {
				self::$array[$key] = call_user_func($callback, $val);
			}
		}
		return self::$array;
	}
	
	final private static function mapArr($array, $callback) {
		foreach($array as $key => $val) {
			if(is_array($val)) {
				$array[$key] = self::mapArr($val, $callback);
			} else {
				$array[$key] = call_user_func($callback, $val);
			}
		}
		return $array;
	}
	
	final public static function GetAll() {
		return self::$array;
	}
	
	final public static function Separator($string = "") {
		if(empty($string)) {
			return self::$separ;
		} else {
			self::$separ = $string;
			return true;
		}
	}
	
	final public static function Gets() {
		$arr = func_get_args();
		$view = $arr[0];
		unset($arr[0]);
		$arr = array_values($arr);
		$tt = "";
		for($i=0;$i<sizeof($arr);$i++) {
			if(isset($view[$arr[$i]])) {
				$tt .= $view[$arr[$i]].self::$separ;
			}
		}
		$len = 0;
		$len -= strlen(self::$separ);
		return substr($tt, 0, $len);
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