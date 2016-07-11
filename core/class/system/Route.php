<?php
/*
 *
 * @version 3.0
 * @copyright 2014-2016 KilleR for Cardinal Engine
 *
 * Version Engine: 3.0
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
 * 2.1
 * add support insert data on params
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
	protected $_callback = "";
	protected $_uri = "";
	protected $_regex = array();
	private $_route_regex = "";
	private $_defaults = array('page' => 'index', 'method' => '', 'host' => false);
	private static $_params = array();
	private static $_secret = "route";
	private static $_config = array();
	
	public static function Config($get) {
		if(is_array($get)) {
			self::$_config = $get;
			return true;
		} else {
			if(isset(self::$_config[$get])) {
				return self::$_config[$get];
			}
		}
		return false;
	}
	
	public static function Build(array $arr, $mode = 1) {
		$arrs = array();
		foreach($arr as $n => $v) {
			if(!is_numeric($n)) {
				global $$n;
				if($mode===2 && is_array($$n)) {
					$$n = array_merge($$n, $v);
				} else {
					$arrs[$n] = $v;
				}
			}
		}
		extract($arrs);
	}
	
	private static function GetBuild($n = "") {
		if(is_array($n)) {
			$b = self::$_secret;
			global $$b;
			if(isset($GLOBALS[$b]) && is_array($GLOBALS[$b])) {
				$b = $GLOBALS[$b];
			} else if(isset($$b) && is_array($$b)) {
				$b = $$b;
			}
			if($b==self::$_secret) {
				return false;
			}
			foreach($n as $nm => $vl) {
				if(isset($b[$vl])) {
					return $b[$vl];
				} else {
					return false;
				}
			}
		} else if(!empty($n)) {
			$b = self::$_secret;
			global $$b;
			if(isset($GLOBALS[$b]) && is_array($GLOBALS[$b]) && isset($GLOBALS[$b][$n])) {
				return $GLOBALS[$b][$n];
			} else if(isset($$b) && is_array($$b) && isset($$b[$n])) {
				return $$b[$n];
			} else {
				return false;
			}
		} else {
			$b = self::$_secret;
			global $$b;
			if(isset($GLOBALS[$b])) {
				return $GLOBALS[$b];
			} else if(isset($$b)) {
				return $$b;
			} else {
				return false;
			}
		}
	}

	public function __construct($uri = "", $regex = array()) {
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

	public static function Set($name, $uri_callback = "", $regex = array()) {
		$class = __class__;
		$ret = new $class($uri_callback, $regex);
		self::Build(array(
			"".self::$_secret."" => array($name => $ret),
		), 2);
		return $ret;
	}

	public static function Get($name) {
		$list = self::GetBuild($name);
		if(!$list) {
			return false;
		}
		return $list;
	}

	public static function Name($route) {
		$_routes = self::GetBuild();
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

	public function Matches($uri, $def = "") {
		if($this->_callback) {
			$closure = $this->_callback;
			$params = call_user_func_array($closure, array($uri, $def));
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
	
	private static function PreDefault($url) {
		$page = $url;
		if(!empty($url)) {
			$page = substr($url, 1);
			if(strpos($page, "&") !== false) {
				$pages = explode("&", $page);
				if(empty($pages[0])) {
					$page = $pages[1];
				} else {
					$page = $pages[0];
				}
			}
			if(strpos($page, "=") !== false) {
				$pages = explode("=", $page);
				$page = $pages[0];
			}
		}
		return $page;
	}

	public static function Load($default = "") {
		$uri = substr(getenv("PATH_INFO"), 1);
		if(!isset($GLOBALS[self::$_secret])) {
			return false;
		}
		$preload = self::PreDefault($uri);
		if($preload!=$default) {
			$routes = $GLOBALS[self::$_secret];
			$params = "";
			foreach($routes as $name => $route) {
				if($params = $route->Matches($uri, $default)) {
					self::$_params = $params;
					return array('params' => $params, 'route' => $route);
				}
			}
		}
		$param = array('page' => $default, 'method' => '');
		self::$_params = $param;
		return array('params' => $param, 'route' => "");
	}
	
	public static function Delete($name) {
		if(!isset($GLOBALS[self::$_secret])) {
			return false;
		}
		$routes = $GLOBALS[self::$_secret];
		if(isset($routes[$name])) {
			unset($routes[$name]);
			$GLOBALS[self::$_secret] = $routes;
		}
	}
	
	public static function RegParam($key, $value = "") {
		if(is_array($key)) {
			$keys = array_keys($key);
			$values = array_values($key);
			for($i=0;$i<sizeof($keys);$i++) {
				self::$_params[$keys[$i]] = $values[$i];
			}
		} else {
			self::$_params[$key] = $value;
		}
	}

	public static function param($key = "", $default = false) {
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
		if(self::Config("rewrite")) {
			return $uri;
		} else {
			return self::Config("default_http_host")."index.php/".$uri;
		}
	}

}

?>