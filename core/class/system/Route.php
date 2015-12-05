<?php
/*
 *
 * @version 2.0
 * @copyright 2014-2015 KilleR for Cardinal Engine
 *
 * Version Engine: 2.0
 * Version File: 2
 *
 * 1.1
 * create routification for engine
 * 1.2
 * add error for routification
 * 1.3
 * fix data on links
 * 1.4
 * add support return list parameters
 * 2.0
 * rebuild logic routification
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

final class Route {

	const REGEX_KEY = '<([a-zA-Z0-9_]++)>';
	const REGEX_SEGMENT = '[^/.,;?\n]++';
	const REGEX_ESCAPE = '[.\\+*?[^\\]${}=!|]';
	protected $_callback = null;
	protected $_uri = null;
	protected $_regex = array();
	private $_route_regex = null;
	private static $_defaults = array('page' => 'index', 'method' => '', 'host' => false);
	private static $_params = array();

	public function __construct($uri = null, $regex = array()) {
		if(empty($uri)) {
			return;
		}
		if(!is_string($uri) && is_callable($uri)) {
			$this->_callback = $uri;
			$this->_uri = $regex;
			$regex = array();
		} elseif(!empty($uri)) {
			$this->_uri = $uri;
		}
		if(!empty($regex)) {
			$this->_regex = $regex;
		}
		$this->_route_regex = self::Compile($uri, $regex);
	}

	public static function Set($name, $uri_callback = null, $regex = array()) {
		$class = __class__;
		$ret = new $class($uri_callback, $regex);
		modules::manifest_set(array('route', $name), $ret);
		return $ret;
	}

	public static function Get($name) {
		$list = modules::manifest_get(array('route', $name));
		if(!$list) {
			return false;
		}
		return $list;
	}

	public static function Name($route) {
		$_routes = modules::manifest_get('route');
		return array_search($route, $_routes);
	}

	public static function Compile($uri, $regex = array()) {
		if(!is_string($uri)) {
			return;
		}
		if(!is_array($regex)) {
			return;
		}
		$expression = preg_replace('#'.self::REGEX_ESCAPE.'#', '\\\\$0', $uri);
		if(strpos($expression, '(') !== false) {
			$expression = str_replace(array('(', ')'), array('(?:', ')?'), $expression);
		}
		$expression = str_replace(array('<', '>'), array('(?P<', '>'.self::REGEX_SEGMENT.')'), $expression);
		if($regex) {
			$search = $replace = array();
			foreach($regex as $key => $value) {
				$search[]  = "<".$key.">".Route::REGEX_SEGMENT;
				$replace[] = "<".$key.">".$value;
			}
			$expression = str_replace($search, $replace, $expression);
		}
		return '#^'.$expression.'$#uD';
	}

	public function defaults($defaults = array()) {
		if(!is_array($defaults)) {
			return false;
		}
		$this->_defaults = $defaults;
		return $this;
	}

	public function Matches($uri) {
		if($this->_callback) {
			$closure = $this->_callback;
			$params = call_user_func($closure, $uri);
			if(!is_array($params)) {
				return false;
			}
		} else {
			if(!preg_match($this->_route_regex, $uri, $matches)) {
				return false;
			}
			$params = array();
			foreach($matches as $key => $value) {
				if(is_int($key)) {
					continue;
				}
				$params[$key] = $value;
			}
		}
		foreach($this->_defaults as $key => $value) {
			if(!isset($params[$key]) || empty($params[$key])) {
				$params[$key] = $value;
			}
		}

		return $params;
	}

	public static function Load($default = null) {
		$uri = substr(getenv("PATH_INFO"), 1);
		$routes = modules::manifest_get('route');
		$params = null;
		foreach($routes as $name => $route) {
			if($params = $route->Matches($uri)) {
				self::$_params = $params;
				return array('params' => $params, 'route' => $route);
			}
		}
		return array('params' => array('page' => $default, 'method' => ''), 'route' => null);
	}

	public static function param($key = null, $default = false) {
		if(empty($key)) {
			return self::$_params;
		}
		return isset(self::$_params[$key]) ? self::$_params[$key] : $default;
	}

	public function Uri($params = array()) {
		$uri = $this->_uri;
		if(strpos($uri, '<') === false && strpos($uri, '(') === false) {
			return $uri;
		}
		while(preg_match('#\([^()]++\)#', $uri, $match)) {
			$search = $match[0];
			$replace = substr($match[0], 1, -1);
			while(preg_match('#'.self::REGEX_KEY.'#', $replace, $match)) {
				list($key, $param) = $match;
				if(isset($params[$param])) {
					$replace = str_replace($key, $params[$param], $replace);
				} else {
					$replace = '';
					break;
				}
			}
			$uri = str_replace($search, $replace, $uri);
		}

		while(preg_match('#'.self::REGEX_KEY.'#', $uri, $match)) {
			list($key, $param) = $match;
			if(!isset($params[$param])) {
				if(isset($this->_defaults[$param])) {
					$params[$param] = $this->_defaults[$param];
				} else {
					return false;
				}
			}
			$uri = str_replace($key, $params[$param], $uri);
		}
		$uri = preg_replace('#//+#', '/', rtrim($uri, '/'));
		return $uri;
	}

}

?>