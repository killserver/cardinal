<?php

class cardinalEvent {
	
	private static $collection = array();
	private static $events = array();

	private static function add($action, $callback, $params = "", $priority = false, $loader = false) {
		if(is_array($callback) && is_callable($callback)) {
			if(!isset(self::$collection[$action])) {
				self::$collection[$action] = array();
			}
			if($priority===false) {
				$priority = sizeof(self::$collection[$action]);
			}
			if(isset(self::$collection[$action][$priority])) {
				foreach(self::$collection[$action] as $pr => $datas) {
					unset(self::$collection[$action][$pr]);
					$pr++;
					self::$collection[$action][$pr] = $datas;
				}
			}
			self::$collection[$action][$priority] = array("fn" => $callback, "data" => $params, "loader" => $loader);
		}
		if(!is_array($callback) && is_callable($callback)) {
			if(!isset(self::$collection[$action])) {
				self::$collection[$action] = array();
			}
			if($priority===false) {
				$priority = sizeof(self::$collection[$action]);
			}
			if(isset(self::$collection[$action][$priority])) {
				foreach(self::$collection[$action] as $pr => $datas) {
					unset(self::$collection[$action][$pr]);
					$pr++;
					self::$collection[$action][$pr] = $datas;
				}
			}
			self::$collection[$action][$priority] = array("fn" => $callback, "data" => $params, "loader" => $loader);
		}
	}
	
	public static function addListener($action, $callback, $params = "", $priority = false) {
		$loader = debug_backtrace();
		if($loader[1]['function'] == 'addEvent') {
			$loader = $loader[1];
		} else if($loader[0]['function'] == 'addListener') {
			$loader = $loader[0];
		}
		if(is_array($action)) {
			for($i=0;$i<sizeof($action);$i++) {
				self::add($action[$i], $callback, $params, $priority, $loader);
			}
		} else {
			self::add($action, $callback, $params, $priority, $loader);
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

	public static function getListeners($action = "") {
		$listener = array();
		if($action!=="" && !isset(self::$collection[$action])) {
			return false;
		} else if($action!=="" && isset(self::$collection[$action])) {
			$a = array_keys(self::$collection[$action]);
			for($z=0;$z<sizeof($a);$z++) {
				$listener[] = self::$collection[$action][$a[$z]]['loader'];
			}
		} else {
			$arr = array_keys(self::$collection);
			for($i=0;$i<sizeof($arr);$i++) {
				$a = array_keys(self::$collection[$arr[$i]]);
				for($z=0;$z<sizeof($a);$z++) {
					$listener[] = self::$collection[$arr[$i]][$a[$z]]['loader'];
				}
			}
		}
		return $listener;
	}

	public static function getEvents() {
		self::addListener("core_ready", "cardinalEvent::getEventList");
	}

	public static function getEventList() {
		return self::$events;
	}
	
	public static function execute($action, $return = false) {
		if(!empty($action) && !isset(self::$events[$action])) {
			$loader = debug_backtrace();
			if($loader[1]['function'] == 'execEvent') {
				$loader = $loader[1];
			} else if($loader[0]['function'] == 'execute') {
				$loader = $loader[0];
			}
			self::$events[$action] = $loader;
		}
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