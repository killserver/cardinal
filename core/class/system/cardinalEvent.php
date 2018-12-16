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
				$list = self::$collection[$typeEvent][$action];
				krsort($list);
				foreach($list as $pr => $datas) {
					unset(self::$collection[$typeEvent][$action][$pr]);
					$k = $pr+1;
					self::$collection[$typeEvent][$action][$k] = $datas;
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
				$list = self::$collection[$typeEvent][$action];
				krsort($list);
				foreach($list as $pr => $datas) {
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
	
	public static function addListenerRef($action, $callback, $priority = false) {
		if(sizeof(self::$loader)==0) {
			$loader = debug_backtrace();
			self::$loader = $loader[0];
		}
		if(is_array($action)) {
			for($i=0;$i<sizeof($action);$i++) {
				self::add($action[$i], $callback, true, "", $priority, self::$loader);
			}
		} else {
			self::add($action, $callback, true, "", $priority, self::$loader);
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

	public static function exists($action) {
		return array_key_exists($action, self::$events);
	}

	public static function did($action) {
		$ret = self::exists($action);
		if($ret) {
			return self::$events[$action]['called'];
		} else {
			return 0;
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
	
	public static function execute($action, $data = "") {
		$args = func_get_args();
		array_shift($args);
		array_shift($args);
		$return = $data;
		if(!empty($action) && !isset(self::$events[$action])) {
			if(sizeof(self::$loader)==0) {
				$loader = debug_backtrace();
				self::$loader = $loader[0];
			}
			self::$events[$action] = array("data" => self::$loader, "called" => 1, "time" => microtime_float());
			self::$loader = array();
		} else if(!empty($action) && isset(self::$events[$action])) {
			self::$events[$action]['called']++;
		}
		if(!empty($action) && isset(self::$collection['standart']) && isset(self::$collection['standart'][$action])) {
			ksort(self::$collection['standart'][$action]);
			foreach(self::$collection['standart'][$action] as $v) {
				$data = array();
				if($v['data']!=="") {
					if(!is_array($v['data'])) {
						$v['data'] = array($v['data']);
					}
					$data = array_merge($v['data'], $data);
				}
				$data[] = $return;
				$data = array_merge($data, $args);
				$ret = call_user_func_array($v['fn'], $data);
				$return = $ret;
			}
			if(!empty($action) && isset(self::$events[$action])) {
				self::$events[$action]['time'] = (self::$events[$action]['time']);
			}
			return $return;
		} else {
			return $return;
		}
	}
	
	public static function executeRef($action, &$ref1 = "", &$ref2 = "", &$ref3 = "", &$ref4 = "", &$ref5 = "", &$ref6 = "", &$ref7 = "", &$ref8 = "") {
		if(!empty($action) && !isset(self::$events[$action])) {
			if(sizeof(self::$loader)==0) {
				$loader = debug_backtrace();
				self::$loader = $loader[0];
			}
			self::$events[$action] = array("data" => self::$loader, "called" => 1, "time" => microtime_float());
			self::$loader = array();
		} else if(!empty($action) && isset(self::$events[$action])) {
			self::$events[$action]['called']++;
		}
		if(!empty($action) && isset(self::$collection['ref']) && isset(self::$collection['ref'][$action])) {
			ksort(self::$collection['ref'][$action]);
			foreach(self::$collection['ref'][$action] as $v) {
				$data = array(&$ref1, &$ref2, &$ref3, &$ref4, &$ref5, &$ref6, &$ref7, &$ref8);
				call_user_func_array($v['fn'], $data);
			}
			if(!empty($action) && isset(self::$events[$action])) {
				self::$events[$action]['time'] = (self::$events[$action]['time']);
			}
			return true;
		} else {
			return false;
		}
	}
	
}