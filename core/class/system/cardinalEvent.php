<?php

class cardinalEvent {
	
	private static $collection = array();
	private static $events = array();
	private static $loader = array();

	private static function add($action, $callback, $ref = false, $params = "", $priority = false, $loader = false) {
		if($ref===false) {
			$typeEvent = 'standart';
			if(!isset(self::$collection[$typeEvent])) {
				self::$collection[$typeEvent] = array();
			}
		} else {
			$typeEvent = 'ref';
			if(!isset(self::$collection[$typeEvent])) {
				self::$collection[$typeEvent] = array();
			}
		}
		if(is_array($callback) && is_callable($callback)) {
			if(!isset(self::$collection[$typeEvent][$action])) {
				self::$collection[$typeEvent][$action] = array();
			}
			if($priority===false) {
				$priority = sizeof(self::$collection[$typeEvent][$action]);
			}
			if(isset(self::$collection[$typeEvent][$action][$priority])) {
				foreach(self::$collection[$typeEvent][$action] as $pr => $datas) {
					unset(self::$collection[$typeEvent][$action][$pr]);
					$pr++;
					self::$collection[$typeEvent][$action][$pr] = $datas;
				}
			}
			self::$collection[$typeEvent][$action][$priority] = array("fn" => $callback, "data" => $params, "loader" => $loader);
		}
		if(!is_array($callback) && is_callable($callback)) {
			if(!isset(self::$collection[$typeEvent][$action])) {
				self::$collection[$typeEvent][$action] = array();
			}
			if($priority===false) {
				$priority = sizeof(self::$collection[$typeEvent][$action]);
			}
			if(isset(self::$collection[$typeEvent][$action][$priority])) {
				foreach(self::$collection[$typeEvent][$action] as $pr => $datas) {
					unset(self::$collection[$typeEvent][$action][$pr]);
					$pr++;
					self::$collection[$typeEvent][$action][$pr] = $datas;
				}
			}
			self::$collection[$typeEvent][$action][$priority] = array("fn" => $callback, "data" => $params, "loader" => $loader);
		}
	}

	public static function loader($arr) {
		self::$loader = $arr;
	}
	
	public static function addListener($action, $callback, $params = "", $priority = false) {
		if(sizeof(self::$loader)==0) {
			$loader = debug_backtrace();
			self::$loader = $loader[0];
		}
		if(is_array($action)) {
			for($i=0;$i<sizeof($action);$i++) {
				self::add($action[$i], $callback, false, $params, $priority, self::$loader);
			}
		} else {
			self::add($action, $callback, false, $params, $priority, self::$loader);
		}
		self::$loader = array();
	}
	
	public static function addListenerRef($action, $callback, $params = "", $priority = false) {
		if(sizeof(self::$loader)==0) {
			$loader = debug_backtrace();
			self::$loader = $loader[0];
		}
		if(is_array($action)) {
			for($i=0;$i<sizeof($action);$i++) {
				self::add($action[$i], $callback, true, $params, $priority, self::$loader);
			}
		} else {
			self::add($action, $callback, true, $params, $priority, self::$loader);
		}
		self::$loader = array();
	}
	
	public static function removeListener($action) {
		if(is_array($action)) {
			for($i=0;$i<sizeof($action);$i++) {
				if(isset(self::$collection['standart']) && isset(self::$collection['standart'][$action[$i]])) {
					unset(self::$collection['standart'][$action[$i]]);
				}
			}
		} else {
			if(isset(self::$collection['standart']) && isset(self::$collection['standart'][$action])) {
				unset(self::$collection['standart'][$action]);
			}
		}
	}
	
	public static function removeListenerRef($action) {
		if(is_array($action)) {
			for($i=0;$i<sizeof($action);$i++) {
				if(isset(self::$collection['ref']) && isset(self::$collection['ref'][$action[$i]])) {
					unset(self::$collection['ref'][$action[$i]]);
				}
			}
		} else {
			if(isset(self::$collection['ref']) && isset(self::$collection['ref'][$action])) {
				unset(self::$collection['ref'][$action]);
			}
		}
	}

	public static function getListeners($action = "") {
		$listener = array();
		foreach(self::$collection as $collection) {
			if($action!=="" && !isset($collection[$action])) {
				$listener = false;
				break;
			} else if($action!=="" && isset($collection[$action])) {
				$a = array_keys($collection[$action]);
				for($z=0;$z<sizeof($a);$z++) {
					$listener[] = $collection[$action][$a[$z]]['loader'];
				}
			} else {
				$arr = array_keys($collection);
				for($i=0;$i<sizeof($arr);$i++) {
					$a = array_keys($collection[$arr[$i]]);
					for($z=0;$z<sizeof($a);$z++) {
						$listener[] = $collection[$arr[$i]][$a[$z]]['loader'];
					}
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
	
	public static function execute($action) {
		$args = func_get_args();
		array_shift($args);
		$return = (isset($args[0]) ? $args[0] : false);
		if(!empty($action) && !isset(self::$events[$action])) {
			if(sizeof(self::$loader)==0) {
				$loader = debug_backtrace();
				self::$loader = $loader[0];
			}
			self::$events[$action] = self::$loader;
			self::$loader = array();
		}
		if(!empty($action) && isset(self::$collection['standart']) && isset(self::$collection['standart'][$action])) {
			ksort(self::$collection['standart'][$action]);
			$return = "";
			foreach(self::$collection['standart'][$action] as $v) {
				$data = array();
				if($return!=="") {
					$data[] = $return;
				} else if($v['data']!=="") {
					$data[] = $v['data'];
				}
				$data = array_merge($data, $args);
				$ret = call_user_func_array($v['fn'], $data);
				if(!empty($ret)) {
					$return = $ret;
				}
			}
			return $return;
		} else {
			return $return;
		}
	}
	
	public static function executeRef($action, &$ref1 = "", &$ref2 = "", &$ref3 = "", &$ref4 = "", &$ref5 = "", &$ref6 = "", &$ref7 = "", &$ref8 = "") {
		$return = ($ref1!==null ? $ref1 : false);
		if(!empty($action) && !isset(self::$events[$action])) {
			if(sizeof(self::$loader)==0) {
				$loader = debug_backtrace();
				self::$loader = $loader[0];
			}
			self::$events[$action] = self::$loader;
			self::$loader = array();
		}
		if(!empty($action) && isset(self::$collection['ref']) && isset(self::$collection['ref'][$action])) {
			ksort(self::$collection['ref'][$action]);
			$return = "";
			foreach(self::$collection['ref'][$action] as $v) {
				if($return!=="") {
					$data = array($return, &$ref1, &$ref2, &$ref3, &$ref4, &$ref5, &$ref6, &$ref7, &$ref8);
				} else if($v['data']!=="") {
					$data = array($v['data'], &$ref1, &$ref2, &$ref3, &$ref4, &$ref5, &$ref6, &$ref7, &$ref8);
				} else {
					$data = array(&$ref1, &$ref2, &$ref3, &$ref4, &$ref5, &$ref6, &$ref7, &$ref8);
				}
				$ret = call_user_func_array($v['fn'], $data);
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