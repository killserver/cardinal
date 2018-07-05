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

class Route {

	const REGEX_KEY = '<([a-zA-Z0-9_]++)>';
	const REGEX_SEGMENT = '[^/.,;?\n]++';
	const REGEX_ESCAPE = '[.\\+*?[^\\]${}=!|]';
	protected $_callback = "";
	protected $_uri = "";
	protected $_regex = array();
	private $_route_regex = "";
	private $_defaults = array('page' => 'index', 'method' => '', 'inPage' => "main", 'host' => false);
	private static $_params = array();
	private static $_errorRoute = array('page' => "error", 'inPage' => "main", 'method' => '');
	private static $_notFound = array('page' => '{default}', 'inPage' => "main", 'method' => '');
	private static $_secret = "route";
	private static $_config = array();
	private static $_lang = "";
	private static $_langForce = "";
	private static $_loaded = "";
	private static $_newMethod = false;

	final public static function newMethod() {
		self::$_newMethod = true;
	}

	final public static function setSecret($name) {
		self::$_secret = $name;
	}
	
	final public static function Config($get) {
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
	
	final public static function SetLang($lang, $force = false) {
		self::$_lang = $lang;
		if($force) {
			self::$_params['lang'] = $lang;
			self::$_langForce = $lang;
		}
	}
	
	final public static function Build(array $arr, $mode = 1) {
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
	
	final private static function GetBuild($n = "") {
		if(is_array($n)) {
			$b = self::$_secret;
			global $$b;
			$a = $$b;
			if(isset($GLOBALS[$b]) && is_array($GLOBALS[$b])) {
				$b = $GLOBALS[$b];
			} else if(isset($a) && is_array($a)) {
				$b = $a;
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
			$a = $$b;
			if(isset($GLOBALS[$b]) && is_array($GLOBALS[$b]) && isset($GLOBALS[$b][$n])) {
				return $GLOBALS[$b][$n];
			} else if(isset($a) && is_array($a) && isset($a[$n])) {
				return $a[$n];
			} else {
				return false;
			}
		} else {
			$b = self::$_secret;
			global $$b;
			$a = $$b;
			if(isset($GLOBALS[$b])) {
				return $GLOBALS[$b];
			} else if(isset($a)) {
				return $a;
			} else {
				return false;
			}
		}
	}

	final public function __construct($uri = "", $regex = array()) {
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

	final public static function Set($name, $uri_callback = "", $regex = array()) {
		$class = __class__;
		$ret = new $class($uri_callback, $regex);
		self::Build(array(
			"".self::$_secret."" => array($name => $ret),
		), 2);
		return $ret;
	}

	final public static function Get($name) {
		$list = self::GetBuild($name);
		if(!$list) {
			return false;
		}
		return $list;
	}
	
	final public static function Search($route) {
		$_routes = self::GetBuild();
		return array_key_exists($route, $_routes);
	}

	final public static function Name($route) {
		$_routes = self::GetBuild();
		return array_search($route, $_routes);
	}

	final public static function Compile($uri, $regex = array()) {
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

	final public function defaults($defaults = array()) {
		if(!is_array($defaults)) {
			return false;
		}
		$this->_defaults = $defaults;
		return $this;
	}

	final public function Matches($uri, $def = "") {
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
				if($key=="lang") {
					$params["now_".$key] = $value;
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
	
	final private static function PreDefault($url) {
		$page = $url;
		if(!empty($url)) {
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

	final public function GetDefaults() {
		if(!is_array($this->_defaults)) {
			return false;
		}
		return $this->_defaults;
	}
	
	final public static function checkEmpty($arr) {
		if(!is_array($arr)) {
			return false;
		}
		$k = key($arr);
		return (isset($arr[$k]) && !empty($arr[$k]));
	}

	final public static function setError($ret) {
		if(!is_array($ret)) {
			$ret = array("response" => $ret);
		}
		self::$_errorRoute = $ret;
	}

	final public static function setNotFound($ret) {
		if(!is_array($ret)) {
			$ret = array("response" => $ret);
		}
		self::$_notFound = $ret;
	}

	final public static function Load($default = "") {
		$uri = getenv(ROUTE_GET_URL);
		$v = getenv("SCRIPT_NAME");
		$len = "index.php";
		if(($pos = strpos($v, "index.php"))!==false) {
			$v = substr($v, 0, $pos);
		}
		$len = strlen("index.php");
		if(substr($uri, 0, $len)==="/index.php") {
			$uri = substr($uri, $len)-1;
		}
		$len = strlen($uri);
		if(strpos($uri, "&")!==false) {
			$len = strpos($uri, "&")-1;
		}
		if(strpos($uri, "?")!==false) {
			$len = strpos($uri, "?")-1;
		}
		if($len>0) {
			$uri = substr($uri, 1, $len);
		} else {
			$uri = "";
		}
		if($v!=="/") {
			$uri = str_replace($v, "", $uri);
		}
		if($uri===$v) {
			return array('params' => array("pages" => "main"), 'route' => "");
		}
		if(!isset($GLOBALS[self::$_secret])) {
			return false;
		}
		$preload = self::PreDefault($uri);
		if($preload!=$default) {
			$routes = $GLOBALS[self::$_secret];
			$params = "";
			foreach($routes as $name => $route) {
				if($params = $route->Matches($uri, $default)) {
					$newLang = array();
					if(class_exists("lang", false) && method_exists("lang", "support")) {
						$langs = lang::support();
						for($i=0;$i<sizeof($langs);$i++) {
							$clearLang = $langs[$i];
							if(strlen($langs[$i])>2) {
								$clearLang = substr($clearLang, 4, -3);
							}
							$newLang[$clearLang] = $langs[$i];
						}
					}
					if(!empty(self::$_langForce) && !isset($params['now_lang'])) {
						$params['lang'] = self::$_langForce;
					} else if(isset($params['now_lang']) && !empty($params['now_lang']) && sizeof($newLang)>0 && isset($newLang[$params['now_lang']])) {
						$params['lang'] = $params['now_lang'];
					} else if(isset($params['now_lang']) && !empty($params['now_lang']) && sizeof($newLang)>0 && !isset($newLang[$params['now_lang']])) {
						header("HTTP/1.1 301 Moved Permanently");
						header("Location: ".$v.substr($uri, 3));
						die();
					}
					self::$_loaded = $uri;
					self::$_params = array_merge(self::$_params, $params);
					return array('params' => $params, 'route' => $route);
				}
			}
		}
		if($uri!==false) {
			$param = self::$_errorRoute;
		} else {
			$notFound = self::$_notFound;
			foreach($notFound as $k => $v) {
				$notFound[$k] = str_replace('{default}', $default, $v);
			}
			$param = $notFound;
		}
		self::$_params = array_merge(self::$_params, $param);
		return array('params' => $param, 'route' => "");
	}
	
	final public static function getLoaded() {
		return self::$_loaded;
	}
	
	final public static function Delete($name) {
		if(!isset($GLOBALS[self::$_secret])) {
			return false;
		}
		$routes = $GLOBALS[self::$_secret];
		if(isset($routes[$name])) {
			unset($routes[$name]);
			$GLOBALS[self::$_secret] = $routes;
		}
	}
	
	final public static function RegParam($key, $value = "") {
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

	final public static function param($key = "", $default = false) {
		if(empty($key)) {
			return self::$_params;
		}
		return isset(self::$_params[$key]) ? self::$_params[$key] : $default;
	}

	final public function Uri($params = array()) {
		if(!empty(self::$_lang)) {
			$params['lang'] = self::$_lang;
		}
		if(!empty(self::$_langForce) && (!isset($params['lang']) || $params['lang'] == config::Select("lang"))) {
			$params['lang'] = self::$_langForce;
		}
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
		if(self::$_newMethod===false) {
			$uri = rtrim($uri, "/");
		}
		$uri = preg_replace('#//+#', '/', $uri);
		if(self::Config("rewrite")) {
			return $uri;
		} else {
			return self::Config("default_http_host")."index.php/".$uri;
		}
	}

}

?>