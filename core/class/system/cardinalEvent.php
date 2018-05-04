<?php

class cardinalEvent {
	
	private static $collection = array();
	
	public static function addListener($action, $callback, $params = "", $priority = 0) {
		if(is_array($callback) && is_callable($callback)) {
			if(!isset(self::$collection[$action])) {
				self::$collection[$action] = array();
			}
			if(isset(self::$collection[$action][$priority])) {
				foreach(self::$collection[$action] as $pr => $datas) {
					unset(self::$collection[$action][$pr]);
					$pr++;
					self::$collection[$action][$pr] = $datas;
				}
			}
			self::$collection[$action][$priority] = array("fn" => $callback, "data" => $params);
		}
		if(!is_array($callback) && is_callable($callback)) {
			if(!isset(self::$collection[$action])) {
				self::$collection[$action] = array();
			}
			if(isset(self::$collection[$action][$priority])) {
				foreach(self::$collection[$action] as $pr => $datas) {
					unset(self::$collection[$action][$pr]);
					$pr++;
					self::$collection[$action][$pr] = $datas;
				}
			}
			self::$collection[$action][$priority] = array("fn" => $callback, "data" => $params);
		}
		if(is_array($action)) {
			for($i=0;$i<sizeof($action);$i++) {
				if(is_array($callback) && is_callable($callback)) {
					if(!isset(self::$collection[$action[$i]])) {
						self::$collection[$action[$i]] = array();
					}
					if(isset(self::$collection[$action][$priority])) {
						foreach(self::$collection[$action] as $pr => $datas) {
							unset(self::$collection[$action][$pr]);
							$pr++;
							self::$collection[$action][$pr] = $datas;
						}
					}
					self::$collection[$action[$i]][$priority] = array("fn" => $callback, "data" => $params);
				}
			}
		}
	}
	
	public static function removeListener($action) {
		if(is_array($action)) {
			for($i=0;$i<sizeof($action);$i++) {
				if(isset(self::$collection[$action[$i]])) {
					unset(self::$collection[$action[$i]]);
				}
			}
		} else {
			if(isset(self::$collection[$action])) {
				unset(self::$collection[$action]);
			}
		}
	}
	
	public static function execute($action, $return = false) {
		if(!empty($action) && isset(self::$collection[$action])) {
			ksort(self::$collection[$action]);
			foreach(self::$collection[$action] as $v) {
				$ret = call_user_func_array($v['fn'], array($v['data'], $return));
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