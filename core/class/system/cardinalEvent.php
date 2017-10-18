<?php

class cardinalEvent {
	
	private static $pageNow = "";
	private static $sendData = "";
	private static $collection = array();
	
	public static function addListener($page, $func) {
		if(is_array($func) && is_callable($func)) {
			if(!isset(self::$collection[$page])) {
				self::$collection[$page] = array();
			}
			self::$collection[$page][] = $func;
		}
		if(!is_array($func) && is_callable($func)) {
			if(!isset(self::$collection[$page])) {
				self::$collection[$page] = array();
			}
			self::$collection[$page][] = $func;
		}
		if(is_array($page)) {
			for($i=0;$i<sizeof($page);$i++) {
				if(is_array($func) && is_callable($func)) {
					if(!isset(self::$collection[$page[$i]])) {
						self::$collection[$page[$i]] = array();
					}
					self::$collection[$page[$i]][] = $func;
				}
			}
		}
	}
	
	public static function removeListener($page) {
		if(is_array($page)) {
			for($i=0;$i<sizeof($page);$i++) {
				if(isset(self::$collection[$page[$i]])) {
					unset(self::$collection[$page[$i]]);
				}
			}
		} else {
			if(isset(self::$collection[$page])) {
				unset(self::$collection[$page]);
			}
		}
	}
	
	public static function setPage($page) {
		if(is_object($page)) {
			$page = get_class($page);
		}
		self::$pageNow = $page;
	}
	
	public static function setData($data) {
		self::$sendData = $data;
	}
	
	public static function execute($page = "", $return = "") {
		if(!empty($page)) {
			self::$pageNow = $page;
		}
		if(!empty(self::$pageNow) && isset(self::$collection[self::$pageNow])) {
			foreach(self::$collection[self::$pageNow] as $v) {
				$ret = call_user_func_array($v, array(self::$sendData, $return));
				if(!empty($ret)) {
					$return = $ret;
				}
			}
			return $return;
		} else {
			return $return;
		}
	}
	
}