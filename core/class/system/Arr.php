<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

class Arr {
	
	private static $array = array();
	private static $empty = true;
	private static $separ = "";
	
	public function __construct(array $arr) {
		self::$array = $arr;
	}
	
	final public static function allowEmpty($empty) {
		self::$empty = $empty;
	}
	
	final public static function getByKey($arr, $keys = array()) {
		$list = func_get_args();
		if(sizeof($keys)>0) {
			return call_user_func_array(__CLASS__."::getByKeyArr", $list);
		} else {
			return call_user_func_array(__CLASS__."::getByKeySelf", $list);
		}
	}
	
	final public static function getByKeyArr() {
		$arr = func_get_args();
		$arrK = $arr[0];
		$arrKF = array_keys($arrK);
		$arrF = $arr[1];
		$ret = false;
		for($i=0;$i<sizeof($arrKF);$i++) {
			for($z=0;$z<sizeof($arrF);$z++) {
				if(stripos($arrKF[$i], $arrF[$z]) !== false) {
					$ret = $arrK[$arrKF[$i]];
					$newSearch = array_slice($arrF, $z+1, sizeof($arrF)+1);
					if(is_array($ret) && sizeof($newSearch)>0) {
						$ret = self::getByKeyArr($ret, $newSearch);
					}
				}
				if(!empty($ret)) {
					break;
				}
			}
			if(!empty($ret)) {
				break;
			}
		}
		return $ret;
	}
	
	final public static function getByKeySelf() {
		$arr = func_get_args();
		$arrK = self::$array;
		$arrKF = array_keys($arrK);
		$arrF = $arr[0];
		$ret = false;
		for($i=0;$i<sizeof($arrKF);$i++) {
			for($z=0;$z<sizeof($arrF);$z++) {
				if(stripos($arrKF[$i], $arrF[$z]) !== false) {
					$ret = $arrK[$arrKF[$i]];
					$newSearch = array_slice($arrF, $z+1, sizeof($arrF)+1);
					if(is_array($ret) && sizeof($newSearch)>0) {
						$ret = self::getByKeyArr($ret, $newSearch);
					}
				}
				if(!empty($ret)) {
					break;
				}
			}
			if(!empty($ret)) {
				break;
			}
		}
		return $ret;
	}
	
	final public static function get($arr, $key = "", $default = "") {
		$list = func_get_args();
		if(is_array($arr)) {
			return call_user_func_array(__CLASS__."::getArr", $list);
		} else {
			return call_user_func_array(__CLASS__."::getSelf", $list);
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
		$list = func_get_args();
		if(is_array($arr)) {
			return call_user_func_array(__CLASS__."::foundArr", $list);
		} else {
			return call_user_func_array(__CLASS__."::foundSelf", $list);
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
		$list = func_get_args();
		if(is_array($arr)) {
			return call_user_func_array(__CLASS__."::pushArr", $list);
		} else {
			return call_user_func_array(__CLASS__."::pushSelf", $list);
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
	
    final public static function divide($array) {
		$list = func_get_args();
		if(is_array($array)) {
			return call_user_func_array(__CLASS__."::divideArr", $list);
		} else {
			return call_user_func_array(__CLASS__."::divideSelf", $list);
		}
    }
	
	final private static function divideArr($arr) {
		$arrs = array();
		$arrs[] = array_keys($arr);
		$arrs[] = array_values($arr);
		return $arrs;
	}
	
	final private static function divideSelf() {
		$arrs = array();
		$arrs[] = array_keys(self::$array);
		$arrs[] = array_values(self::$array);
		return $arrs;
	}
	
	final public static function pull($arr, $mixed = "", $default = "") {
		$list = func_get_args();
		if(is_array($arr)) {
			return call_user_func_array(__CLASS__."::pullArr", $list);
		} else {
			return call_user_func_array(__CLASS__."::pullSelf", $list);
		}
	}
	
	final private static function pullArr($arr, $mixed, $default = "") {
		$val = false;
		if(self::get($arr, $mixed, $default)) {
			$val = self::$array[$mixed];
			unset(self::$array[$mixed]);
		}
		return $val;
	}
	
	final private static function pullSelf($mixed, $default = "") {
		$val = false;
		if(self::get($mixed, $default)) {
			$val = self::$array[$mixed];
			unset(self::$array[$mixed]);
		}
		return $val;
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
		$list = func_get_args();
		if(is_array($array)) {
			return call_user_func_array(__CLASS__."::mapArr", $list);
		} else {
			return call_user_func_array(__CLASS__."::mapSelf", $list);
		}
	}
	
	final private static function mapSelf($callback) {
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
	
	final public static function filter($array, $callback = "") {
		$list = func_get_args();
		if(is_array($array)) {
			return call_user_func_array(__CLASS__."::filterArr", $list);
		} else {
			return call_user_func_array(__CLASS__."::filterSelf", $list);
		}
	}
	
	final private static function filterSelf($callback) {
		foreach(self::$array as $key => $val) {
			if(is_array($val)) {
				self::$array[$key] = self::filterSelf($callback, $val);
			} else {
				self::$array[$key] = call_user_func_array("array_filter", array($val, $callback));
			}
		}
		return self::$array;
	}
	
	final private static function filterArr($array, $callback) {
		foreach($array as $key => $val) {
			if(is_array($val)) {
				$array[$key] = self::filterArr($val, $callback);
			} else {
				$array[$key] = call_user_func_array("array_filter", array($val, $callback));
			}
		}
		return $array;
	}
	
	final public static function wrap($array) {
		return (!is_array($array) ? array($array) : $array);
	}
	
	final public static function GetAll() {
		return self::$array;
	}
	
	final public static function GetList() {
		$elem = func_get_args();
		$ret = false;
		if(isset($elem[0]) && is_array($elem[0])) {
			$arr    = $elem[0];
			$next   = $elem[1];
			$iStart = 1;
		} else {
			$arr    = self::$array;
			$next   = $elem[0];
			$iStart = 1;
		}
		for($i=$iStart;$i<(sizeof($elem)+1);$i++) {
			if((isset($arr[$next]) && !empty($arr[$next]))) {
				if(is_array($arr[$next])) {
					$arr = $arr[$next];
					if(isset($elem[$i+1])) {
						$next = $elem[$i+1];
					}
				} else {
					$ret = $arr[$next];
					continue;
				}
			}
		}
		return $ret;
	}
	
	final public static function CheckList() {
		$list = func_get_args();
		$ret = call_user_func_array("Arr::GetList", $list);
		return ($ret === false ? false : true);
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