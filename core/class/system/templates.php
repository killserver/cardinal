<?php
/*
 *
 * @version 5.4
 * @copyright 2014-2016 KilleR for Cardinal Engine
 *
 * Version Engine: 5.4
 * Version File: 4
 *
 * 3.0
 * add fix foreach block. before - if given string in foreaching data - viewing error in page
 * 3.0.5
 * add fix lost password in templates and fix old call config
 * 3.1
 * fix userlevel data
 * 3.2
 * fix error in minify for position in page
 * 3.3
 * add and fix header list for pages
 * 3.4
 * add support preg_replace_callback_array function in php 7
 * 3.5
 * fix title in error
 * 3.6
 * add support routification
 * clean and boost code
 * 4.0
 * first doc-file on engine, comment all method and variable
 * add support include template as php file[BETA-TEST]
 * add support special code for use variable as data for php file
 * 4.1
 * add support get params in routification
 * 4.2
 * rebuild get config in templates, rebuild logic parse templates and safe mode in php format
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

/**
 * Class templates
 */
class templates {

	/**
	 * @var array
     */
	public static $blocks = array();
	/**
	 * @var array
     */
	private static $foreach = array("count" => 0, "all" => array());
	/**
	 * @var array
     */
	private static $module = array("head" => array(), "body" => array(), "blocks" => array());
	/**
	 * @var array
     */
	private static $editor = array();
	/**
	 * @var array
     */
	private static $header = array();
	/**
	 * @var string
     */
	private static $tmp = "";
	/**
	 * @var bool|string
     */
	private static $skins = "main";
	/**
	 * @var string
     */
	private static $dir_skins = "skins";
	/**
	 * @var bool
     */
	public static $gzip = true;
	/**
	 * @var bool
     */
	public static $gzipActive = false;
	/**
	 * @var int
     */
	public static $time = 0;

	private static $isChangeHead = false;
	private static $typeTpl = "tpl";
	private static $mainTpl = "main";
	private static $mainSkins = "";
	private static $accessEmpty = false;

	/**
	 * templates constructor.
	 * @param array $config Configuration template
     */
	final public function __construct($config = array()) {
		self::SetConfig($config);
		self::$dir_skins = substr(str_replace(ROOT_PATH, "", PATH_SKINS), 0, (-(strlen(DS))));
	}

	/**
	 * Reset config template
	 * @access public
	 * @param array $config Array configuration template
     */
	final public static function SetConfig($config) {
		if(isset($config['gzip_output']) && !$config['gzip_output']) {
			self::$gzip = $config['gzip_output'];
		}
		if(isset($config['skins_skins'])) {
			self::$skins = $config['skins_skins'];
		}
		if(isset($config["skins_test_shab"]) && !empty($test_shab)) {
			self::$skins = $config["skins_test_shab"];
		}
		if(defined("MOBILE") && MOBILE && isset($config['skins_mobile'])) {
			self::$skins = $config['skins_mobile'];
		}
		if(!defined("PHP_EX")) {
			$phpEx = substr(strrchr(__FILE__, '.'), 1);
			if(empty($phpEx)) {
				$phpEx = "php";
			}
		} else {
			$phpEx = PHP_EX;
		}
		if(file_exists(PATH_SKINS.self::$skins.DS."functions.".$phpEx)) {
			require_once(PATH_SKINS.self::$skins.DS."functions.".$phpEx);
		}
		if(!defined("TEMPLATEPATH")) {
			define("TEMPLATEPATH", PATH_SKINS.self::$skins.DS);
		}
	}

    /**
     * Set gzip debug
     * @param bool $active Activation gzip debug
     */
    final public static function gzip($active = true) {
		self::$gzip = $active;
	}

    /**
     * Set gzip method
     * @param bool $active Activation gzip method
     */
    final public static function gzipActive($active = false) {
		self::$gzipActive = $active;
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
	 * Call another function
	 * @access public
	 * @param string $func Name function for call
	 * @param array $args Params for calling
	 * @return mixed Result work function
     */
	final public static function __callBack($func, array $args) {
		return call_user_func_array($func, $args);
	}

	/**
	 * If skin setting - reset default directory skin or return default
	 * @access public
	 * @param string $skin Directory skin name
	 * @return string default or set directory skin
     */
	final public static function dir_skins($skin = "") {
		if(!empty($skin)) {
			self::$dir_skins = $skin;
		}
		return self::$dir_skins;
	}

	/**
	 * Set skin
	 * @access public
	 * @param string $skin Skin
     */
	final public static function set_skins($skin) {
		self::$skins = $skin;
	}
	
	final public static function set_mainSkins($skin) {
		self::$mainSkins = $skin;
	}

	final public static function accessNull($val = true) {
		self::$accessEmpty = $val;
	}

	/**
	 * Get skin
	 * @access public
	 * @return bool|string Skin
     */
	final public static function get_skins() {
		return self::$skins;
	}
	
	
	final public static function get_mainSkins() {
		return self::$mainSkins;
	}

	/**
	 * Return UNIX-time with microseconds
	 * @access private
	 * @return mixed time with microseconds
     */
	final private static function time() {
		return microtime(true);
	}

	final private static function gen_uuid() {
		return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		// 32 bits for "time_low"
		mt_rand(0, 0xffff), mt_rand(0, 0xffff),

		// 16 bits for "time_mid"
		mt_rand(0, 0xffff),

		// 16 bits for "time_hi_and_version",
		// four most significant bits holds version number 4
		mt_rand(0, 0x0fff) | 0x4000,

		// 16 bits, 8 bits for "clk_seq_hi_res",
		// 8 bits for "clk_seq_low",
		// two most significant bits holds zero and one for variant DCE1.1
		mt_rand(0, 0x3fff) | 0x8000,

		// 48 bits for "node"
		mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
		);
	}

	private static $assignVarI = 0;
	/**
	 * Set data for template
	 * @access public
	 * @param string $name Name data for template
	 * @param string $value Value data for template
	 * @param string $block Block data for create array
     */
	final public static function assign_var($name, $value, $block = "", $id = "") {
		if($block!=="" && $id==="") {
			$id = self::gen_uuid();
		}
		if(is_bool($value)) {
			if($value===true) {
				$value = "true";
			} else if($value===false) {
				$value = "false";
			}
		}
		if(empty($block)) {
			self::$blocks[$name] = $value;
		} else if(!empty($id)) {
			self::$blocks[$block][$id][$name] = $value;
		} else {
			self::$blocks[$block][self::$assignVarI][$name] = $value;
			self::$assignVarI++;
		}
	}

	/**
	 * Set array datas for template
	 * @access public
	 * @param array $array Array data for template
	 * @param string $block Block data for cycle
	 * @param string $view Unique id for block data
     */
	final public static function assign_vars($array, $block = "", $view = "") {
		if($block!=="" && $view==="") {
			$view = self::gen_uuid();
		}
		if($block==="") {
			foreach($array as $name => $value) {
				if(is_bool($value)) {
					if($value===true) {
						$value = "true";
					} else if($value===false) {
						$value = "false";
					}
				}
				self::$blocks[$name] = $value;
			}
		} elseif($view!=="") {
			foreach($array as $name => $value) {
				if(is_bool($value)) {
					if($value===true) {
						$value = "true";
					} else if($value===false) {
						$value = "false";
					}
				}
				if(!isset(self::$blocks[$block]) || !is_array(self::$blocks[$block])) {
					self::$blocks[$block] = array();
				}
				if(!isset(self::$blocks[$block][$view]) || !is_array(self::$blocks[$block][$view])) {
					self::$blocks[$block][$view] = array();
				}
				if(!isset(self::$blocks[$block][$view][$name]) || !is_array(self::$blocks[$block][$view][$name])) {
					self::$blocks[$block][$view][$name] = array();
				}
				self::$blocks[$block][$view][$name] = $value;
			}
		} else {
			foreach($array as $name => $value) {
				if(is_bool($value)) {
					if($value===true) {
						$value = "true";
					} else if($value===false) {
						$value = "false";
					}
				}
				self::$blocks[$block][$name] = $value;
			}
		}
	}

	final public static function loadObject($obj, $name = "", &$i = 0) {
		if(!is_object($obj) && !is_array($obj)) {
			self::ErrorTemplate("First parameter is not object and not array");
			die();
		}
		if(is_object($obj) && !($obj instanceof DBObject)) {
			$arr = get_object_vars($obj);
		} else {
			$arr = $obj;
		}
		if(empty($name)) {
			if(is_array($obj) && isset($obj[0]) && is_object($obj[0])) {
				$name = get_class($obj[0]);
			} else if(is_object($obj)) {
				$name = get_class($obj);
			}
		}
		if(empty($name)) {
			self::ErrorTemplate("Name is not set");
			die();
		}
		foreach($arr as $k => $v) {
			if(is_array($arr) && is_object($v) && $v instanceof DBObject && !empty($name)) {
				$v = $v->getArray();
				$vs = cardinalEvent::execute("templates::loadObject", $v);
				if(!empty($vs)) {
					$v = $vs;
				}
				self::assign_vars($v, $name, $name.$k.$i);
			} else if(is_object($arr) && $arr instanceof DBObject) {
				$v = $arr->getArray();
				var_dump($v);
				$vs = cardinalEvent::execute("templates::loadObject", $v);
				if(!empty($vs)) {
					$v = $vs;
				}
				self::loadObject($v, $name, $i);
			} else if(is_object($v) && $v instanceof DBObject) {
				$v = $v->getArray();
				$vs = cardinalEvent::execute("templates::loadObject", $v);
				if(!empty($vs)) {
					$v = $vs;
				}
				self::loadObject($v, $name, $i);
			} else if(is_object($v)) {
				$vs = cardinalEvent::execute("templates::loadObject", $v);
				if(!empty($vs)) {
					$v = $vs;
				}
				self::loadObject($v, $name, $i);
			} else if(is_array($arr)) {
				$vs = cardinalEvent::execute("templates::loadObject", $v);
				if(!empty($vs)) {
					$v = $vs;
				}
				self::assign_var($k, $v, $name, $name.$k.$i);
			} else {
				$vs = cardinalEvent::execute("templates::loadObject", $v);
				if(!empty($vs)) {
					$v = $vs;
				}
				self::assign_var($k, $v);
			}
		}
		$i++;
		return true;
	}
	
	final public static function resetVars($array, $view = "") {
		if(empty($view) && isset(self::$blocks[$array])) {
			unset(self::$blocks[$array]);
		} else if(isset(self::$blocks[$array]) && isset(self::$blocks[$array][$view])) {
			unset(self::$blocks[$array][$view]);
		}
	}
	
	final public static function resetVar($name, $block = "", $id = "") {
		if(empty($block) && isset(self::$blocks[$name])) {
			unset(self::$blocks[$name]);
		} else if(!empty($id) && isset(self::$blocks[$block][$id][$name])) {
			unset(self::$blocks[$block][$id][$name]);
		} else if(isset(self::$blocks[$block][$name])) {
			unset(self::$blocks[$block][$name]);
		}
	}

	final public static function getElements($file) {
		$tpl = self::load_templates($file);
		foreach(self::$blocks as $name => $val) {
			if(is_array($name)) {
				continue;
			}
			if(!is_array($val) && strpos($tpl, "{".$name."}") !== false) {
				$tpl = str_replace("{".$name."}", $val, $tpl);
			} else {
				$tpl = self::callback_array("#{(.+?)(\[|\.)(.+?)(\]|)}#", ("templates::getElementBlock"), $tpl);
			}
		}
		preg_match_all("#{(.+?)(\[|\.)(.+?)(\]|)}#i", $tpl, $arr);
		for($i=0;$i<sizeof($arr[0]);$i++) {
			if(
				stripos($arr[0][$i], "{C_")!==false  ||
				stripos($arr[0][$i], "{R_")!==false  ||
				stripos($arr[0][$i], "{L_")!==false  ||
				stripos($arr[0][$i], "{if ")!==false ||
				stripos($arr[0][$i], "{U_")!==false  ||
				stripos($arr[0][$i], "{D_")!==false  ||
				stripos($arr[0][$i], "{RP")!==false  ||
				stripos($arr[0][$i], "{M_")!==false  ||
				stripos($arr[0][$i], "{UL_")!==false ||
				stripos($arr[0][$i], "{S_")!==false  ||
				stripos($arr[0][$i], "{FN_")!==false  ||
				stripos($arr[0][$i], "{THEME}")!==false
			) {
				unset($arr[0][$i]);
			}
		}
		$arr[0] = array_values($arr[0]);
		return $arr[0];
	}

	final private static function getElementBlock($array) {
		if(!isset(self::$blocks[$array[1]])) {
			return $array[0];
		}
		if(is_array(self::$blocks[$array[1]])) {
			$get = current(self::$blocks[$array[1]]);
			if(isset($get[$array[3]])) {
				return "array";
			}
			return "array";
		}
	}

	/**
	 * Add insert in before and after head or body
	 * @access public
	 * @param string $data Data for insert in template
	 * @param array $where Conditions for insert
     */
	final public static function add_modules($data, $where) {
		if(!is_string($where)) {
			$where = (string)($where);
		}
		$where = explode("|", $where);
		if($where[0]=="head") {
			if(empty(self::$module['head'][$where[1]])) {
				self::$module['head'][$where[1]] = $data;
			} else {
				self::$module['head'][$where[1]] = self::$module['head'][$where[1]].$data;
			}
		} elseif($where[0]=="body") {
			if(empty(self::$module['body'][$where[1]])) {
				self::$module['body'][$where[1]] = $data;
			} else {
				self::$module['body'][$where[1]] = self::$module['body'][$where[1]].$data;
			}
		}
	}

	/**
	 * Method return result work preg_replace for php 7 and old
	 * @access private
	 * @param string $pattern Template for Regular aspect
	 * @param mixed $func Called function after work
	 * @param string $data Original data
	 * @return array|mixed|NUll Return data after replace
     */
	final private static function callback_array($pattern, $func, $data) {
		if(function_exists("preg_replace_callback_array")) {
			return preg_replace_callback_array(array($pattern => $func), $data);
		} else {
			return preg_replace_callback($pattern, $func, $data);
		}
	}

	/**
	 * Replace data in template
	 * @access private
	 * @param array $array Array data
	 * @return string|void Return result replacing
     */
	final private static function foreachs($array) {
		if(!isset(self::$blocks[$array[1]])) {
			return false;
		}
		$data = self::$blocks[$array[1]];
		$key = array_keys($data);
		$text = ($array[2]);
		$tt = "";
		$num = 1;
		$all = sizeof($data)-1;
		$rnum = $all+1;
		self::$foreach['all'][$array[1]] = $all;
		for($i=0;$i<=$all;$i++) {
			if(isset(self::$foreach['count']) && self::$foreach['count']!=0 && $num >= self::$foreach['count']) {
				$num = 1;
			}
			if(!is_array($data[$key[$i]])) {
				continue;
			}
			$dd = $text;
			$nams = array_keys($data[$key[$i]]);$vals = array_values($data[$key[$i]]);
			for($is=0;$is<sizeof($data[$key[$i]]);$is++) {
				if(!is_string($vals[$is])) {
					$vals[$is] = var_export($vals[$is], true);
				}
				$new = str_replace('{$id}', $num, $dd);
				$new = str_replace('{$rid}', $rnum, $new);
				$new = str_replace('{'.$array[1].'.$id}', $num, $new);
				$new = str_replace('{'.$array[1].'.$rid}', $rnum, $new);
				$new = str_replace('{'.$nams[$is].'}', $vals[$is], $new);
				$dd = str_replace('{'.$array[1].'.'.$nams[$is].'}', $vals[$is], $new);
			}
			$dd = str_replace('{$size_for}', $all+1, $dd);
			$dd = self::callback_array("#\\[if (.*?)\\]([\s\S]*?)\\[else\\]([\s\S]*?)\\[/if\\]#i", ("templates::is"), $dd);
			$dd = self::callback_array("#\\[if (.*?)\\]([\s\S]*?)\\[/if\\]#i", ("templates::is"), $dd);
			$dd = self::callback_array('#\[foreachif (.*?)\]([\s\S]*?)\[else \\1\]([\s\S]*?)\[/foreachif \\1\]#i', ("templates::is"), $dd);
			$dd = self::callback_array('#\[foreachif (.*?)\]([\s\S]*?)\[/foreachif \\1\]#i', ("templates::is"), $dd);
			$dd = self::callback_array("#\\[foreachif (.*?)\\]([\s\S]*?)\\[else\\]([\s\S]*?)\\[/foreachif\\]#i", ("templates::is"), $dd);
			$dd = self::callback_array("#\\[foreachif (.*?)\\]([\s\S]*?)\\[/foreachif\\]#i", ("templates::is"), $dd);
			$num++;
			$rnum--;
			$tt .= str_replace('\n', "\n", $dd);
		}
	return $tt;
	}

	/**
	 * Get save data about user
	 * @access private
	 * @param array $array Array data about user
	 * @return bool|string Result get information about user
     */
	final private static function user($array) {
		if(isset($array[2])) {
			$mod = modules::get_user(array($array[1], $array[2]));
		} else {
			$mod = modules::get_user($array[1]);
		}
		if(!$mod) {
			$mod = "0";
		}
		if(!is_null($mod)) {
			return $mod;
		} else {
			return $array[0];
		}
	}

	/**
	 * Return isset parameter in routification
	 * @access private
	 * @param array $arr Array gets params
	 * @return mixed Return data in routification or original line
     */
	final private static function routeparam($arr) {
		$list = Route::param();
		if(isset($arr[1]) && !empty($arr[1]) && isset($list[$arr[1]])) {
			return $list[$arr[1]];
		} else {
			return $arr[0];
		}
	}

	/**
	 * Get routification link
	 * @access private
	 * @param array $array Params for routification
	 * @return mixed Return routification link or original line
     */
	final private static function route($array) {
		if(isset($array[1])) {
			$route = Route::get($array[1]);
			if(!is_bool($route)) {
				$params = array();
				if(isset($array[2])) {
					$array = explode(";", $array[2]);
					for($i=0;$i<sizeof($array);$i++) {
						$exp = explode("=", $array[$i]);
						if(isset($exp[1])) {
							$val = $exp[1];
						} else {
							$val = "";
						}
						if(isset($exp[0])) {
							$params[$exp[0]] = $val;
						} else {
							$params[] = $val;
						}
					}
				}
				unset($val, $exp, $array);
				return $route->uri($params);
			} else {
				return $array[0];
			}
		} else {
			return $array[0];
		}
	}

	/**
	 * Get system data
	 * @access private
	 * @param array $array Data for getting
	 * @return int|mixed Return safe data system function or original line
     */
	final private static function systems($array) {
		switch($array[1]) {
			case "rand":
				$ret = self::mrand();
			break;
			case "time":
				$ret = time();
			break;
			case "dirSkins":
				$ret = self::$dir_skins;
			break;
			default:
				$ret = $array[0];
			break;
		}
		return $ret;
	}

	/**
	 * Get random numeric
	 * @access private
	 * @param int $min Minimal integer for random numeric
	 * @param int $max Maximal integer for random numeric
	 * @return int Return random numeric
     */
	final private static function mrand($min = 0, $max = 0) {
		if($min==0 && $max==0) {
			if(function_exists("random_int") && defined("PHP_INT_MIN")) {
				$min = PHP_INT_MIN;
			}
			if(function_exists("random_int") && defined("PHP_INT_MAX")) {
				$max = PHP_INT_MAX;
			} else {
				if(function_exists("mt_rand")) {
					$max = mt_getrandmax();
				} else {
					$max = getrandmax();
				}
			}
		}
		if(function_exists("random_int")) {
			return random_int($min, $max);
		}
		if(function_exists("mt_rand")) {
			return mt_rand($min, $max);
		} else {
			return rand($min, $max);
		}
	}

	/**
	 * Get config data
	 * @access private
	 * @param array $array Params for get config
	 * @return bool Return data in config or original line
     */
	final private static function config($array) {
		if(class_exists("config") && method_exists("config", "Select")) {
			if(isset($array[3])) {
				$isset = config::Select($array[1], $array[2], $array[3]);
			} else if(isset($array[2])) {
				$isset = config::Select($array[1], $array[2]);
			} else {
				$isset = config::Select($array[1]);
			}
		} else {
			global $config;
			if(isset($array[3])) {
				$isset = (isset($config[$array[1]]) && isset($config[$array[1]][$array[2]]) && isset($config[$array[1]][$array[2]][$array[3]]) ? $config[$array[1]][$array[2]][$array[3]] : false);
			} else if(isset($array[2])) {
				$isset = (isset($config[$array[1]]) && isset($config[$array[1]][$array[2]]) ? $config[$array[1]][$array[2]] : false);
			} else {
				$isset = (isset($config[$array[1]]) ? $config[$array[1]] : false);
			}
		}
		if(!empty($isset)) {
			return $isset;
		} else {
			return (self::$accessEmpty ? $isset : $array[0]);
		}
	}

	/**
	 * Get language data
	 * @access private
	 * @param array $array Params for get language
	 * @return bool Return data in language or original line
     */
	final private static function lang($array) {
		if(isset($array[4])) {
			$isset = modules::get_lang($array[2], $array[4]);
		} else {
			$isset = modules::get_lang($array[2]);
		}
		if(!empty($isset) && $isset!='""') {
			return $isset;
		} else if($array[2]!='""') {
			return $array[2];
		} else {
			return "";
		}
	}

	/**
	 * Line replaced on params
	 * @access private
	 * @param string $text Original line
	 * @param array $arr Array data for replacing
	 * @return string Return replaced string
     */
	final private static function sprintf($text, $arr=array()) {
		for($i=0;$i<sizeof($arr); $i++) {
			$text = str_replace("%s[".($i+1)."]", $arr[$i], $text);
		}
		return $text;
	}

	/**
	 * Get constant
	 * @access private
	 * @param array $array Get constant
	 * @return string Return data in constant or original line
     */
	final private static function define($array) {
	global $manifest;
		if(defined($array[1])) {
			return constant($array[1]);
		} else {
			$def = $manifest['define'];
			if(isset($def[$array[1]])) {
				return $def[$array[1]];
			}
			return $array[0];
		}
	}

	/**
	 * Rebuild DateTime with and without start time
	 * @access private
	 * @param array $data Array data for rebuild view time
	 * @return bool|string Return DateTime to template
     */
	final private static function sys_date($data) {
		if(is_array($data)) {
			if(empty($data[2])) {
				return date($data[1]);
			} else {
				if(is_numeric($data[1])) {
					return date($data[2], $data[1]);
				} elseif(is_string($data[1])) {
					$tmp = strtotime($data[1]);
					return date($data[2], $tmp);
				}
			}
		}
		return "";
	}

    /**
     * Check equals mobile
     * @param string $type Type equals type if mobile, tablet or desktop
     * @return int Result checking
     */
    final private static function checkMobileExec($type) {
	global $mobileDetect;
		if(!in_array($type, array('desktop', 'tablet', 'mobile', 'iOS', 'androidOS'))) {
			throw new Exception("ERROR check type device");
		}
		if(!is_object($mobileDetect)) {
			$mobileDetect = new Mobile_Detect();
		}
		if($type=="tablet" && $mobileDetect->isTablet()) {
			return 1;
		} else if($type=="mobile" && ($mobileDetect->isMobile() && !$mobileDetect->isTablet())) {
			return 1;
		} else if($type=="desktop" && !($mobileDetect->isMobile() || $mobileDetect->isTablet())) {
			return 1;
		} else if($type=="iOS" && $mobileDetect->isiOS()) {
			return 1;
		} else if($type=="androidOS" && $mobileDetect->isAndroidOS()) {
			return 1;
		} else {
			return 0;
		}
	}

    /**
     * Check equals mobile
     * @param array $array Matches checking
     * @return string Return checking
     */
    final private static function checkMobile($array) {
		if(strpos($array[1], "|")!==false) {
			$exp = explode("|", $array[1]);
			$ret = false;
			for($i=0;$i<sizeof($exp);$i++) {
				if(self::checkMobileExec($exp[$i])>0) {
					$ret = true;
					break;
				}
			}
			if($ret) {
				return "true";
			} else {
				return "false";
			}
		}
		if(strpos($array[1], "&")!==false) {
			$exp = explode("&", $array[1]);
			$size = sizeof($exp);
			$start = 1;
			for($i=0;$i<$size;$i++) {
				$start += self::checkMobileExec($exp[$i]);
			}
			if($start==$size) {
				return "true";
			} else {
				return "false";
			}
		}
		$start = self::checkMobileExec($array[1]);
		if($start>0) {
			return "true";
		} else {
			return "false";
		}
	}

	private static $mixins = array();
	private static function mixinCache($arr) {
		self::$mixins[$arr[1]] = $arr[2];
		return "";
	}

	final private static function include_mixin($arr) {
		$ret = $arr[0];
		if(isset(self::$mixins[$arr[1]])) {
			$ret = self::$mixins[$arr[1]];
		}
		return $ret;																																								
	}

	final private static function declension($arr) {
		if(strpos($arr[3], "|")!==false) {
			$arr[3] = explode("|", $arr[3]);
		} else {
			$arrs = array($arr[3]);
			unset($arr[3]);
			$arr[3] = $arrs;
			unset($arrs);
		}
		$lastInt = $arr[2] % 100;
		$lastInt = $lastInt > 10 && $lastInt < 20 ? 9 : $arr[2] % 10;
		return $arr[1].(isset($arr[3][$lastInt]) ? $arr[3][$lastInt] : "");
	}

	/**
	 * View part template if group user more than
	 * @access private
	 * @param array $array Group and template
	 * @return string Result checking
     */
	final private static function group($array) {
		$level = modules::get_user('level');
		if($level==0) {
			$level = 5;
		}
		$groups = $array[2];
		$block = $array[3];
		$groups = (strpos(",", $groups) !== false ? explode(',', $groups) : array($groups));
		if($array[1] === "group") {
			if(!in_array($level, $groups)) {
				return "";
			}
		} else {
			if(in_array($level, $groups)) {
				return "";
			}
		}
	return $block;
	}

	/**
	 * View part template if use ajax
	 * @access public
	 * @param array $array Params checking
	 * @return string Result view
     */
	final public static function ajax($array) {
		if(strpos($array[0], "!ajax") !== false) {
			if((isset($_GET['tajax']) && isset($_GET['jajax'])) || getenv('HTTP_X_REQUESTED_WITH') != 'XMLHttpRequest') {
				return $array[1];
			} else {
				return "";
			}
		} else {
			if(getenv('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest') {
				return $array[1];
			} else {
				return "";
			}
		}
	}

	/**
	 * View part template if use GET-param "jajax"
	 * @access private
	 * @param array $array Array data
	 * @return string Result review
     */
	final private static function ajax_click($array) {
		if(strpos($array[0], "!ajax_click") !== false) {
			if(!isset($_GET['tajax']) && !isset($_GET['jajax'])) {
				return $array[1];
			} else {
				return "";
			}
		} else {
			if(isset($_GET['tajax']) && isset($_GET['jajax'])) {
				return $array[1];
			} else {
				return "";
			}
		}
	}

	/**
	 * Get utensils user to group and hath access to part template
	 * @access private
	 * @param array $array Array data
	 * @return string Return "true" or "false"
     */
	final private static function level($array) {
		if(isset($array[3])) {
			$array[3] = str_replace("\"", "", $array[3]);
			return userlevel::check($array[1], $array[2]);
		} else {
			$is = userlevel::get($array[1]);
			return ($is=="yes" ? "true" : "false");
		}
	}

	/**
	 * IF
	 * @access private
	 * @param array $array Array data
	 * @param bool|false $elseif Check elseif or not
	 * @return array|bool|string Return result conditions
     */
	final private static function is($array, $elseif=false) {
		$else=false;
		$good = true;
		$ret = (isset($array[3]) ? (!$elseif ? $array[3] : false) : "");
		if(isset($array[1]) && strpos($array[1], "||") !== false) {
			$data = explode("||", $array[1]);
			$array[1] = $data[0];
			$else = self::is(array($array, $data[1], $array[2], $ret), true);
		}
		if(isset($array[1]) && strpos($array[1], "&&") !== false) {
			$data = explode("&&", $array[1]);
			$array[1] = $data[0];
			$good = self::is(array($array, $data[1], $array[2], $ret), true);
		}
		if(!$elseif) {
			$data = $array[2];
		} else {
			$data = true;
		}
		$e = array();
		if(strpos($array[1], "UL") !== false) {
			$type = "true";
		} elseif(strpos($array[1], "!ajax") !== false) {
			$type = "not_ajax";
		} elseif(strpos($array[1], "ajax") !== false) {
			$type = "ajax";
		} elseif(strpos($array[1], "<>") !== false) {
			$type = "not";
			$e = explode("<>", $array[1]);
		} elseif(strpos($array[1], ">=") !== false) {
			$type = "biga";
			$e = explode(">=", $array[1]);
		} elseif(strpos($array[1], "<=") !== false) {
			$type = "smalla";
			$e = explode("<=", $array[1]);
		} elseif(strpos($array[1], "<") !== false) {
			$type = "small";
			$e = explode("<", $array[1]);
		} elseif(strpos($array[1], ">") !== false) {
			$type = "big";
			$e = explode(">", $array[1]);
		} elseif(strpos($array[1], "!=") !== false) {
			$type = "not";
			$e = explode("!=", $array[1]);
		} elseif(strpos($array[1], "=") !== false) {
			$type = "yes";
			$t = str_replace("==", "=", $array[1]);
			$e = explode("=", $t);
		} elseif(strpos($array[1], "mod") !== false) {
			$type = "mod";
			$e = explode("mod", $array[1]);
		}
		if(strpos($array[1], "!class_exists(") !== false) {
			$type = "nce";
			$e = preg_replace("/!class_exists(.+?)/", "$1", $array[1]);
			$e = str_replace(array("(", ")"), "", $e);
		} elseif(strpos($array[1], "class_exists(") !== false) {
			$type = "ce";
			$e = preg_replace("/class_exists(.+?)/", "$1", $array[1]);
			$e = str_replace(array("(", ")"), "", $e);
		} elseif(strpos($array[1], "!empty(") !== false) {
			$type = "not_empty";
			$e = preg_replace("/!empty(.+?)/", "$1", $array[1]);
			$e = str_replace(array("(", ")"), "", $e);
			if(strpos($e, "{")!==false) {
				$e = preg_replace("#\{(.+?)\}#", "$1", $e);
			}
		} elseif(strpos($array[1], "empty(") !== false) {
			$type = "empty";
			$e = preg_replace("/empty(.+?)/", "$1", $array[1]);
			$e = str_replace(array("(", ")"), "", $e);
			if(strpos($e, "{")!==false) {
				$e = preg_replace("#\{(.+?)\}#", "$1", $e);
			}
		} elseif($array[1]=="true") {
			$type = "yes";
			$e = array($array[1], $array[1]);
		} elseif($array[1]=="false") {
			$type = "not";
			$e = array($array[1], $array[1]);
		}
		if(!isset($type)) return false;
		if($type == "UL") {
			$e = str_replace("\"", "", $e);
			if(($e=="true" || $else) && $good) {
				unset($e);
				unset($type);
				return $data;
			} else {
				unset($e);
				unset($type);
				return $ret;
			}
		} elseif($type == "nce") {
			$e = str_replace("\"", "", $e);
			if((!class_exists($e) || $else) && $good) {
				unset($e);
				unset($type);
				return $data;
			} else {
				unset($e);
				unset($type);
				return $ret;
			}
		} elseif($type == "ce") {
			$e = str_replace("\"", "", $e);
			if((class_exists($e) || $else) && $good) {
				unset($e);
				unset($type);
				return $data;
			} else {
				unset($e);
				unset($type);
				return $ret;
			}
		} elseif($type == "not") {
			$t = str_replace("\"", "", $e[1]);
			if(($e[0] != $e[1] || $e[0] != $t || $else) && $good) {
				unset($e);
				unset($type);
				return $data;
			} else {
				unset($e);
				unset($type);
				return $ret;
			}
		} elseif($type == "smalla") {
			if(($e[0] <= $e[1] || $else) && $good) {
				unset($e);
				unset($type);
				return $data;
			} else {
				unset($e);
				unset($type);
				return $ret;
			}
		} elseif($type == "biga") {
			if(($e[0] >= $e[1] || $else) && $good) {
				unset($e);
				unset($type);
				return $data;
			} else {
				unset($e);
				unset($type);
				return $ret;
			}
		} elseif($type == "small") {
			if(($e[0] < $e[1] || $else) && $good) {
				unset($e);
				unset($type);
				return $data;
			} else {
				unset($e);
				unset($type);
				return $ret;
			}
		} elseif($type == "big") {
			if(($e[0] > $e[1] || $else) && $good) {
				unset($e);
				unset($type);
				return $data;
			} else {
				unset($e);
				unset($type);
				return $ret;
			}
		} elseif($type == "yes") {
			$t = str_replace("\"", "", $e[1]);
			if(($e[0] == $e[1] || $e[0] == $t || $else) && $good) {
				unset($e);
				unset($type);
				return $data;
			} else {
				unset($e);
				unset($type);
				return $ret;
			}
		} elseif($type == "empty") {
			$e = str_replace("\"", "", $e);
			if(($e==="" || $else) && $good) {
				unset($e);
				unset($type);
				return $data;
			} else {
				unset($e);
				unset($type);
				return $ret;
			}
		} elseif($type == "not_empty") {
			$e = str_replace("\"", "", $e);
			if(($e!=="" || isset(self::$blocks[$e]) || $else) && $good) {
				unset($e);
				unset($type);
				return $data;
			} else {
				unset($e);
				unset($type);
				return $ret;
			}
		} elseif($type == "ajax") {
			if(getenv('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest' && !isset($_GET['jajax'])) {
				unset($type);
				return $data;
			} else {
				return "";
			}
		} elseif($type == "not_ajax") {
			if(getenv('HTTP_X_REQUESTED_WITH') != 'XMLHttpRequest') {
				unset($type);
				return $data;
			} else {
				return "";
			}
		} elseif($type == "mod") {
			if(is_array($e)) {
				$e = array_map("trim", $e);
			}
			if(((is_array($e) && sizeof($e)==2 && $e[0]!=="" && $e[0]>0 && $e[1]!=="" && $e[1]>0) || $else) && $good) {
				if(($e[0]%$e[1])!=1) {
					unset($e);
					unset($type);
					return $data;
				} else {
					unset($e);
					unset($type);
					return $ret;
				}
			} else if(((is_array($e) && sizeof($e)==2 && $e[0]!=="" && $e[0]>0) || $else) && $good) {
				if(($e[0]%2)!=1) {
					unset($e);
					unset($type);
					return $data;
				} else {
					unset($e);
					unset($type);
					return $ret;
				}
			} else {
				unset($e);
				unset($type);
				return $ret;
			}
		}
		return "";
	}

	/**
	 * Reset count element in array
	 * @access private
	 * @param array $array Count elements in array
     */
	final private static function foreach_set($array) {
		self::$foreach = array_merge(self::$foreach, array("count" => $array[1]));
	}

	/**
	 * Return count elements in array
	 * @access private
	 * @param array $array Needed array for count elements in he'm
	 * @return int
     */
	final private static function countforeach($array) {
		if(isset(self::$foreach["all"][$array[1]])) {
			$data = self::$foreach["all"][$array[1]]+1;
		} else {
			$data = 0;
		}
		return $data;
	}

	/**
	 * Replace part template
	 * @access private
	 * @param array $array Array data for replacing
	 * @return mixed Return part template or original line
     */
	final private static function replace_tmp($array) {
		if(isset(self::$blocks[$array[1]][$array[2]])) {
			return self::$blocks[$array[1]][$array[2]];
		} else {
			return $array[0];
		}
	}

	/**
	 * Included completed template or result working module
	 * @param $arr Parameters for sub functions
	 * @return string Result working
     */
	final private static function includeFile($arr) {
		if($arr[1]=="module") {
			unset($arr[1]);$arr = array_values($arr);
			return self::include_module($arr);
		} else if($arr[1]=="templates") {
			unset($arr[1]);$arr = array_values($arr);
			return self::include_tpl($arr);
		} else if($arr[1]=="content") {
			unset($arr[1]);$arr = array_values($arr);
			return self::include_content($arr);
		} else if($arr[1]=="mixin") {
			unset($arr[1]);$arr = array_values($arr);
			return self::include_mixin($arr);
		}
	}

	final private static function include_content($array) {
		if(strpos($array[1], ",") !== false) {
			$file = explode(",", $array[1]);
		} else {
			$file = array($array[1]);
		}
		$files = self::load_templates($file[0], "", (isset($file[1]) && !empty($file[1]) ? $file[1] : "null"), "");
		$files = str_replace("{THEME}", config::Select("default_http_local").self::$dir_skins."/".self::$skins, $files);
		return $files;
	}

	/**
	 * Include other template in main template
	 * @access private
	 * @param array $array Array data for including
	 * @return array|mixed|NUll|string Return part template for insert in main template
     */
	final private static function include_tpl($array) {
		if(strpos($array[1], ",") !== false) {
			$file = explode(",", $array[1]);
		} else {
			$file = array($array[1]);
		}
		if(strpos($file[0], self::$typeTpl)!==false) {
			$file[0] = substr($file[0], 0, (-(strlen(".".self::$typeTpl))));
		}
		$files = self::load_templates($file[0], "", (isset($file[1]) && !empty($file[1]) ? $file[1] : "null"));
		$files = self::comp_datas($files, $file[0]);
		return $files;
	}

	/**
	 * Include module
	 * @access private
	 * @param array $array Array data for initialize module-file
	 * @return mixed Result work module or original line
     */
	final private static function include_module($array) {
		$params = array();
		if(strpos($array[1], "&")!==false) {
			$exp = explode("&", $array[1]);
			$array[1] = $exp[0];
			unset($exp[0]);
			foreach($exp as $v) {
				$v = explode("=", $v);
				$params[$v[0]] = $v[1];
			}
		}
		if(strpos($array[1], ",") !== false) {
			$exp = explode(",", $array[1]);
			$ret = $exp[1];
			$array[1] = $exp[0];
		} else {
			$ret = $array[0];
		}
		if(strpos($array[1], ".".ROOT_EX) === false) {
			$array[1] = $array[1].".".ROOT_EX;
		}
		$class = str_replace(array(".class", ".".ROOT_EX), "", $array[1]);
		
		if(!file_exists(PATH_MODULES.$array[1]) && !file_exists(PATH_AUTOLOADS.$array[1])) {
			return $ret;
		}
		if(!class_exists($class)) {
			return $ret;
		}
		if(!method_exists($class, "start")) {
			return $ret;
		}
		$mod = new $class();
		return call_user_func_array(array(&$mod, "start"), $params);
	}

	/**
	 * Change block-data in memory
	 * @access public
	 * @param string $name Name block-data
	 * @param string $value Value block-data
	 * @param string $func Method working(add,edit,delete)
     */
	final public static function change_blocks($name, $value = "", $func = "add") {
		if($func=="add" || $func=="edit") {
			self::$blocks[$name] = $value;
		} elseif($func=="delete") {
			unset(self::$blocks[$name]);
		}
	}

	/**
	 * Count block-data in memory
	 * @access private
	 * @param array $array Block-data
	 * @return int Return count block-data
     */
	final private static function count_blocks($array) {
		if(isset($array[2]) && isset(self::$blocks[$array[2]])) {
			return sizeof(self::$blocks[$array[2]]);
		} else {
			return 0;
		}
	}

	final private static function fmk($array) {
		if(!isset($array[2]) || empty($array[2])) {
			return "";
		}
		$file = str_replace("{THEME}", config::Select("default_http_local").self::$dir_skins."/".self::$skins, $array[2]);
		$file = substr(ROOT_PATH, 0, -1).$file;
		$file = filemtime($file);
		if($file===false) {
			return "";
		}
		return $file;
	}

	final private static function callFn($arr) {
		if(is_callable($arr[1])) {
			if(isset($arr[3])) {
				if(strpos($arr[3], ";")!==false) {
					$arr[3] = explode(";", $arr[3]);
				} else {
					$arr[3] = array($arr[3]);
				}
			} else {
				$arr[3] = array();
			}
			$res = call_user_func_array($arr[1], $arr[3]);
			if($res===true) {
				$res = "true";
			} else if($res===false) {
				$res = "false";
			}
			return $res;
		} else {
			return $arr[0];
		}
	}

	private static $independentCount = 0;
	final public static function optimizeImage($url) {
		$arr = array();
		$exp = explode("&", $url);
		$arr['link'] = $exp[0];
		for($i=1;$i<sizeof($exp);$i++) {
			$exps = explode("=", $exp[$i]);
			$arr[$exps[0]] = (isset($exps[1]) ? $exps[1] : "");
		}
		if(strpos($arr['link'], "?")!==false) {
			$arr['link'] = explode("?", $arr['link']);
			$arr['link'] = current($arr['link']);
		}
		$filename_ROOT_PATH = str_replace("\/", "/", $arr['link']);
		if(substr($filename_ROOT_PATH, 0, 1)=="/") {
			$filename_ROOT_PATH = substr($filename_ROOT_PATH, 1);
		}
		if(empty($filename_ROOT_PATH)) {
			return $arr['link'];
		}
		$filename_ROOT_PATH = rawurldecode(ROOT_PATH.$filename_ROOT_PATH);
		if(!file_exists($filename_ROOT_PATH)) {
			return $arr['link'];
		}

		$exp = explode(".", $filename_ROOT_PATH);
		$type = end($exp);
		$checkFile = str_replace(".".$type, "", $filename_ROOT_PATH);
		if(isset($arr['width'])) {
			$checkFile .= "_w".round($arr['width']);
		}
		if(isset($arr['height'])) {
			$checkFile .= "_h".round($arr['height']);
		}
		if(!isset($arr['width']) && !isset($arr['height'])) {
			$checkFile .= ".min";
		}
		$checkFile .= ".".$type;
		if(file_exists($checkFile)) {
			return str_replace(ROOT_PATH, "", $checkFile);
		}
		if(self::$independentCount < 5) {
			self::$independentCount++;
			return self::imageRes(array("", $url));
		}
		return $url;
	}

	final public static function retina($link, $size = array("480", "720", "1020")) {
		if(!is_array($size)) {
			switch($size) {
				case 'small':
					$size = array("480");
				break;
				case 'medium':
					$size = array("720");
				break;
				case 'big':
					$size = array("1020");
				break;
			}
		}
		$rets = array();
		$size = array_unique($size);
		for($i=0;$i<sizeof($size);$i++) {
			$rets[$size[$i]] = array("img" => self::imageRes(array("", $link."&width=".$size[$i])), "size" => $size[$i]);
		}
		return (sizeof($rets)>0 ? $rets : $link);
	}

	final private static function retinaImg($arr) {
		$link = $arr[1];
		$size = false;
		$type = "img";
		if(isset($arr[4])) {
			$size = $arr[4];
		}
		if(isset($arr[2])) {
			$type = $arr[2];
		}
		$ret = $arr[0];
		if($size===false) {
			$ret = self::retina($link);
		} else {
			$ret = self::retina($link, $size);
		}
		$ret = array_values($ret);
		$count = sizeof($ret);
		$center = round($count/2);
		//$html = ' src="medium.png" srcset=" small.png 480w, medium.png 720w, big.png 1020w " sizes=" (min-width: 1100px) 1020px, (min-width: 720px) 720px, 100vw "';
		$main = (isset($ret[$center-1]) ? $ret[$center-1] : current($ret));
		$html = "";
		if($type=="img") {
			$html = 'src="{C_default_http_local}'.$main['img'].'"';
		} elseif($type=="div") {
			$main = 'background-image:url(\'{C_default_http_local}'.$main['img'].'\')';
		}
		$sizes = $srcset = array();
		$max = 0;
		for($i=0;$i<$count;$i++) {
			if($type=="img") {
				$sizes[$ret[$i]['size']] = "(min-width: ".$ret[$i]['size']."px) ".$ret[$i]['size']."px";
				$srcset[] = '{C_default_http_local}'.$ret[$i]['img']." ".$ret[$i]['size']."w";
			} elseif($type=="div") {
				$max = max($max, $ret[$i]['size']);
				$srcset[] = "@media(max-width:".$ret[$i]['size']."px){background-image:url('{C_default_http_local}".$ret[$i]['img']."');}";
			}
		}
		krsort($sizes);
		if(sizeof($srcset)>0) {
			if($type=="img") {
				$html .= ' srcset="'.implode(", ", $srcset).'" sizes="'.implode(", ", $sizes).', 100vw"';
			} elseif($type=="div") {
				$html .= implode("", $srcset).'@media(min-width:'.$max.'px){background-image:url(\''.$main.'\');}';
			}
		}
		return $html;
	}
	
	final private static function imageRes($arrData) {
		$arr = array();
		$exp = explode("&", $arrData[1]);
		$arr['link'] = $exp[0];
		for($i=1;$i<sizeof($exp);$i++) {
			$exps = explode("=", $exp[$i]);
			$arr[$exps[0]] = (isset($exps[1]) ? $exps[1] : "");
		}
		if(strpos($arr['link'], "?")!==false) {
			$arr['link'] = explode("?", $arr['link']);
			$arr['link'] = current($arr['link']);
		}
		$filename_ROOT_PATH = str_replace("\/", "/", $arr['link']);
		if(substr($filename_ROOT_PATH, 0, 1)=="/") {
			$filename_ROOT_PATH = substr($filename_ROOT_PATH, 1);
		}
		if(empty($filename_ROOT_PATH)) {
			return $arr['link'];
		}
		$filename_ROOT_PATH = rawurldecode(ROOT_PATH.$filename_ROOT_PATH);
		if(!file_exists($filename_ROOT_PATH)) {
			return $arr['link'];
		}

		$exp = explode(".", $filename_ROOT_PATH);
		$type = end($exp);
		$checkFile = str_replace(".".$type, "", $filename_ROOT_PATH);
		if(isset($arr['width'])) {
			$checkFile .= "_w".round($arr['width']);
		}
		if(isset($arr['height'])) {
			$checkFile .= "_h".round($arr['height']);
		}
		if(!isset($arr['width']) && !isset($arr['height'])) {
			$checkFile .= ".min";
		}
		$checkFile .= ".".$type;
		if(file_exists($checkFile)) {
			return str_replace(ROOT_PATH, "", $checkFile);
		}


		if(substr(decoct(fileperms($filename_ROOT_PATH)), -4) != 0777) {
			@chmod($filename_ROOT_PATH, 0777);
		}
		$size_img = getimagesize($filename_ROOT_PATH);
		$src_ratio = $size_img[0] / $size_img[1];

		if(!isset($arr['width']) && !isset($arr['height'])) {
			if($size_img[0] > 1000) {
				$arr['width'] = $size_img[0]/5;
			}
			if($size_img[1] > 1000) {
				$arr['height'] = $size_img[1]/5;
			}
		}

		if(isset($arr['height']) && !isset($arr['width'])) {
			$ratio = $arr['height'] / $size_img[1];
			$setWidth = $size_img[0] * $ratio;
		}
		if(!isset($arr['height']) && isset($arr['width'])) {
			$ratio = $arr['width'] / $size_img[0];
			$setHeight = $size_img[1] * $ratio;
		}
		if(isset($arr['persent']) && $arr['persent']>0 && $arr['persent']<100) {
			$arr['width'] = ($size_img[0]/100*$arr['persent']);
			$arr['height'] = ($size_img[1]/100*$arr['persent']);
		}

		$resizerWidth = $resizeWidth = (isset($arr['width']) ? $arr['width'] : (isset($setWidth) ? $setWidth : $size_img[0]));
		$resizerHeight = $resizeHeight = (isset($arr['height']) ? $arr['height'] : (isset($setHeight) ? $setHeight : $size_img[1]));
		$trimWidth = $resizeWidth;
		$trimHeight = $resizeHeight;

		$sizeImgWidth = $size_img[0];
		$sizeImgHeight = $size_img[1];


		if(isset($arr['scale'])) {
			$ratio = $arr['height'] / $sizeImgHeight;
			$sizeImgHeight = $sizeImgWidth * $ratio;
		}

		$resizeHeight = floor($resizeWidth / $src_ratio);
		$dest_imgs = imagecreatetruecolor($resizeWidth, $resizeHeight);
		$image_type = $size_img[2];
		if($image_type == IMAGETYPE_JPEG) {
			$src_img = imagecreatefromjpeg($filename_ROOT_PATH);
			if($src_img && function_exists('exif_read_data')) {
				if(
					(version_compare(phpversion(), "7.2.0", ">=") && strpos($filename_ROOT_PATH, "http")!==false)
					||
					strpos($filename_ROOT_PATH, "http")===false
				) {
					if(strpos($filename_ROOT_PATH, "http")===false) {
						$exif = exif_read_data($filename_ROOT_PATH, 0, true);
					} else {
						$stream = fopen($filename_ROOT_PATH, "rb");
						$exif = exif_read_data($stream, 0, true);
					}
					if(isset($exif['IFD0']) && isset($exif['IFD0']['Orientation']) && !empty($exif['IFD0']['Orientation'])) {
						switch($exif['IFD0']['Orientation']) {
							case 8:
								$src_img = imagerotate($src_img, 90, 0);
							break;
							case 3:
								$src_img = imagerotate($src_img, 180, 0);
							break;
							case 6:
								$src_img = imagerotate($src_img, -90, 0);
							break;
						}
					}
				}
			}
		} elseif($image_type == IMAGETYPE_GIF) {
			$src_img = imagecreatefromgif($filename_ROOT_PATH);
		} elseif($image_type == IMAGETYPE_PNG) {
			$src_img = imagecreatefrompng($filename_ROOT_PATH);
		} else {
			throw new Exception("Error type image");die();
		}
		imagealphablending( $dest_imgs, false );
		imagesavealpha( $dest_imgs, true );
		imagecopyresampled($dest_imgs, $src_img, 0, 0, 0, 0, $resizeWidth, $resizeHeight, $size_img[0], $size_img[1]);
		imagedestroy($src_img);
		$dest_img = imagecreatetruecolor($resizerWidth, $resizerHeight);
		imagealphablending( $dest_img, false );
		imagesavealpha( $dest_img, true );
		imagecopyresampled($dest_img, $dest_imgs, 0, 0, 0, 0, $resizerWidth, $resizerHeight, $resizeWidth, $resizeHeight);

		$thumb = imagecreatetruecolor($trimWidth, $trimHeight);
		if(isset($arr['width'])) {
			if(isset($arr['posX']) && $arr['posX']>=0 && $arr['posX']<=100) {
				$posX = round(($arr['width'] - $trimWidth) * $arr['posX'] / 100);
			} else {
				$posX = round(($arr['width'] - $trimWidth) / 2);
			}
		}
		if(isset($arr['posY']) && $arr['posY']>=0 && $arr['posY']<=100) {
			$posY = round(($resizeHeight - $trimHeight) * $arr['posY'] / 100);
		} else {
			$posY = round(($resizeHeight - $trimHeight) / 2);
		}
		imagealphablending( $thumb, false );
		imagesavealpha( $thumb, true );
		imagecopyresampled($thumb, $dest_img, 0, 0, 0, 0, $trimWidth, $trimHeight, $trimWidth, $trimHeight);
		$compression = (isset($arr['compress']) && $arr['compress']>0 && $arr['compress']<=100  ? intval($arr['compress']) : 60);

		$exp = explode(".", $filename_ROOT_PATH);
		$type = end($exp);
		$filename_ROOT_PATH = str_replace(".".$type, "", $filename_ROOT_PATH);
		$set = false;
		if(isset($arr['width'])) {
			$set = true;
			$filename_ROOT_PATH .= "_w".round($arr['width']);
		}
		if(isset($arr['height'])) {
			$set = true;
			$filename_ROOT_PATH .= "_h".round($arr['height']);
		}
		if(!$set) {
			$filename_ROOT_PATH .= ".min";
		}
		$filename_ROOT_PATH .= ".".$type;

		ob_start();
		if($image_type == IMAGETYPE_JPEG) {
			imagejpeg($thumb, $filename_ROOT_PATH, $compression);
		} elseif($image_type == IMAGETYPE_GIF) {
			imagegif($thumb, $filename_ROOT_PATH);
		} elseif($image_type == IMAGETYPE_PNG) {
			//header("content-type:image/png");
			imagepng($thumb, $filename_ROOT_PATH);
		}
		ob_get_clean();
		$file = str_replace(ROOT_PATH, "", $filename_ROOT_PATH);
		$file = rawurlencode($file);
		$file = str_replace("%2F", "/", $file);
		return $file;
	}

	final private static function fors($data) {
		$step = 1;
		if(!empty($data[4]) && is_numeric($data[4]) && $data[4]>0) {
			$step = intval($data[4]);
		}
		$tpl = $data[5];
		$sub = "";
		for($i=$data[1];$i<=$data[2];$i=$i+$step) {
			$sub .= str_replace("{id}", $i, $tpl);
		}
		return $sub;
	}

	/**
	 * Rebuild standard template for completed
	 * @access public
	 * @param string $file File for completed
	 * @param bool|false $no_skin Template exist on directory without skin
     */
	final public static function load_template($file, $no_skin = false) {
		$time = self::time();
		self::$tmp = self::load_templates($file, "", (!$no_skin ? "null" : ""));
		self::$time += self::time()-$time;
	}

	/**
	 * Load template as string for further using
	 * @access public
	 * @param string $file File template
	 * @param string $charset Charset file
	 * @param string $dir Directory template
	 * @return string Template for further using
     */
	final public static function load_templates($file, $charset = "", $dir = "null", $type = true) {
		if($type===true) {
			$type = ".".self::$typeTpl;
		}
		$time = self::time();
		if($dir == "null") {
			$tpl = self::loadFile(ROOT_PATH."".self::$dir_skins.DS.self::$skins.DS.$file.$type);
		} elseif($dir=="admin") {
			$tpl = self::loadFile(ROOT_PATH.ADMINCP_DIRECTORY.DS."temp".DS.$file.$type);
		} elseif(empty($dir)) {
			$tpl = self::loadFile(ROOT_PATH."".self::$dir_skins.DS.$file.$type);
		} else {
			$tpl = self::loadFile(ROOT_PATH."".self::$dir_skins.DS.$dir.DS.$file.$type);
		}
		if(!empty($charset)) {
			$tpl = iconv($charset, modules::get_config("charset"), $tpl);
		}
		self::$time += self::time()-$time;
		return $tpl;
	}

	final private static function loadFile($file) {
		try {
			$tpl = file_get_contents($file);
		} catch(Exception $ex) {
			$tpl = false;
		}
		if($tpl===false) {
			self::ErrorTemplate("File \"".$file."\" is not exists", $file);
			die();
		}
		return $tpl;
	}

	/**
	 * Completed template for finally using
	 * @access public
	 * @param string $file File template
	 * @param string $dir Directory template
	 * @return array|mixed|NUll|string Completed template
     */
	final public static function completed_assign_vars($file, $dir = "null") {
		$time = self::time();
		$tpl = self::load_templates($file, "", $dir);
		$tpl = self::comp_datas($tpl, $file);
		if($dir == "null") {
			$tpl = str_replace("{THEME}", config::Select("default_http_local").self::$dir_skins."/".self::$skins, $tpl);
		} elseif(empty($dir)) {
			$tpl = str_replace("{THEME}", config::Select("default_http_local").self::$dir_skins, $tpl);
		} else {
			$tpl = str_replace("{THEME}", config::Select("default_http_local").self::$dir_skins."/".$dir, $tpl);
		}
		self::$time += self::time()-$time;
		return $tpl;
	}
	/**
	 * Completed template for finally using
	 * @access public
	 * @param string $file File template
	 * @param string $dir Directory template
	 * @return array|mixed|NUll|string Completed template
	 */
	final public static function complited_assing_vars($file, $dir = "null") {
		return self::completed_assign_vars($file, $dir);
	}

	final public static function check_exists($file, $dir = "null", $type = true) {
		if($type===true) {
			$type = ".".self::$typeTpl;
		}
		if($dir == "null") {
			return file_exists(ROOT_PATH."".self::$dir_skins.DS.self::$skins.DS.$file.$type);
		} elseif($dir=="admin") {
			$tpl = file_exists(ROOT_PATH.ADMINCP_DIRECTORY.DS."temp".DS.$file.$type);
		} elseif(empty($dir)) {
			return file_exists(ROOT_PATH."".self::$dir_skins.DS.$file.$type);
		} else {
			return file_exists(ROOT_PATH."".self::$dir_skins.DS.$dir.DS.$file.$type);
		}
	}

	final public static function findTemplate($file, $dir = "null", $type = true) {
		if($type===true) {
			$type = ".".self::$typeTpl;
		}
		if($dir == "null") {
			return (ROOT_PATH."".self::$dir_skins.DS.self::$skins.DS.$file.$type);
		} elseif($dir=="admin") {
			$tpl = (ROOT_PATH.ADMINCP_DIRECTORY.DS."temp".DS.$file.$type);
		} elseif(empty($dir)) {
			return (ROOT_PATH."".self::$dir_skins.DS.$file.$type);
		} else {
			return (ROOT_PATH."".self::$dir_skins.DS.$dir.DS.$file.$type);
		}
	}

	/**
	 * Prepare template for viewing
	 * @access public
	 * @param string $tmp Completed template
	 * @param array|string $header List headers or title
     */
	final public static function completed($tmp, $headerSet = "") {
	global $manifest;
		$time = self::time();
		if(!is_array($headerSet)) {
			$arr = array(
				"meta" => array(
					"og" => array(
						"description" => htmlspecialchars(lang::get_lang("s_description")),
					),
					"ogpr" => array(
						"og:description" => htmlspecialchars(lang::get_lang("s_description")),
					),
					"description" => htmlspecialchars(lang::get_lang("s_description")),
				),
			);

			if(!empty($headerSet)) {
				$arr['title'] = $headerSet;
				$arr['meta']['og']['title'] = $headerSet;
				$arr['meta']['ogpr']['title'] = $headerSet;
			} else {
				$arr['title'] = htmlspecialchars(lang::get_lang("sitename"));
				$arr['meta']['og']['title'] = htmlspecialchars(lang::get_lang("sitename"));
				$arr['meta']['ogpr']['title'] = htmlspecialchars(lang::get_lang("sitename"));
			}

			$header = $arr;
		} else {
			$header = $headerSet;
		}
		if(!is_array(self::$header)) {
			self::$header = array();
		}
		if(!self::$isChangeHead) {
			self::$header = array_replace_recursive(self::$header, releaseSeo(array(), true), $header);
		} else {
			self::$header = array_replace_recursive($header, releaseSeo(array(), true), self::$header);
		}
		if(function_exists("execEventRef")) {
			execEventRef("change_header", self::$header);
		}
		if(!empty($headerSet)) {
			$manifest['mod_page'][HTTP::getip()]['title'] = self::$header['title'];
			modules::manifest_set(array('mod_page', HTTP::getip(), 'title'), self::$header['title']);
		}
		$tmp = self::comp_datas($tmp);
		self::$tmp = $tmp;
		self::$time += self::time()-$time;
	}

	/**
	 * Prepare template for viewing
	 * @access public
	 * @param string $tmp Completed template
	 * @param array|string $header List headers or title
	 */
	final public static function complited($tmp, $header = "") { return self::completed($tmp, $header); }

	/**
	 * Change title page
	 * @access public
	 * @param string $header Necessary title
     */
	final public static function change_head($header) {
		self::$isChangeHead = true;
		if(!is_array($header)) {
			self::$header = array("title" => $header);
		} else {
			self::$header = array_merge($header, self::$header);
		}
	}

	final public static function getHeaders() {
		return self::$header;
	}

	/**
	 * "Last" compiling template before he view
	 * @access private
	 * @param string $tmp Template for last rebuild
	 * @param string $file Execute file
	 * @return array|mixed|NUll|string Result rebuild
     */
	final private static function ecomp($tmp, $file = "null") {
		$tmp = self::comp_datas($tmp, $file, "ecomp");
		return $tmp;
	}

	final private static function langdate($arr) {
		if(isset($arr[3]) && !empty($arr[3])) {
			$temp = $arr[3];
		} else {
			$temp = ", H:i";
		}
		$only_date = false;
		if(isset($arr[4]) && !empty($arr[4])) {
			$only_date = true;
		}
		if(isset($arr[1])) {
			$date = $arr[1];
		}
		if(!is_numeric($date)) {
			if(!isset($arr[3]) || empty($arr[3])) {
				$temp = $date;
				$date = "";
				$only_date = true;
			} else {
				return $arr[0];
			}
		}
		return langdate($date, $temp, $only_date);
	}

	private static function set($arr) {
		self::$blocks[$arr[1]] = self::comp_datas($arr[2]);
		return "";
	}

	/**
	 * Final completed template
	 * @access public
	 * @param string $tpl File template
	 * @param string $file Sub file for completed
	 * @param string $type Type compile
	 * @return array|mixed|NUll Done completed
     */
	public static function comp_datas($tpl, $file = "null", $type = "all") {
		$tpl = preg_replace("/\/\/\/\*\*\*(.+?)\*\*\*\/\/\//is", "", $tpl);
		if(function_exists("execEvent")) {
			$tpl = execEvent("compileTPL", $tpl, $file, $type);
			$tpl = execEvent("templates::compile::before", $tpl, $file, $type);
		}
		$tpl = self::callback_array("#\[SET \{(.+?)\}=\{(.+?)\}\]#i", ("templates::set"), $tpl);

		if($type=="all" || $type=="ecomp") {
			if(class_exists("modules") && modules::manifest_get(array("temp", "block"))!==false) {
				self::$blocks = array_merge(self::$blocks, modules::manifest_get(array("temp", "block")));
			}
			foreach(self::$blocks as $name => $val) {
				if(is_array($name)) {
					continue;
				}
				if(!is_array($val) && strpos($tpl, "{".$name."}") !== false) {
					$tpl = str_replace("{".$name."}", $val, $tpl);
				} else {
					$tpl = self::callback_array("/{(.+?)\[(.+?)\]}/", ("templates::replace_tmp"), $tpl);
				}
			}
			$tpl = self::callback_array("#\\[(not-group)=(.+?)\\](.+?)\\[/not-group\\]#is", ("templates::group"), $tpl);
			$tpl = self::callback_array("#\\[(group)=(.+?)\\](.+?)\\[/group\\]#is", ("templates::group"), $tpl);
			$tpl = self::callback_array("#\[MIXIN name=(.+?)\](.*?)\[/MIXIN\]#is", "templates::mixinCache", $tpl);
			$tpl = self::callback_array("#\{include (.+?)=['\"](.*?)['\"](|[\"'](.+?)[\"'])\}#", ("templates::includeFile"), $tpl);
			$tpl = self::callback_array("~\{is_last\[(\"|)(.+?)(\"|)\]\}~", ("templates::count_blocks"), $tpl);
			$tpl = self::callback_array("#\{UL_(.*?)(|\[(.*?)\])\}#", ("templates::level"), $tpl);
			if(function_exists("plural_form")) {
				$tpl = self::callback_array("#\{LP_\[(.*?)\]\[(.*?)\](|\[(.*?)\])\}#", ("plural_form"), $tpl);
			}
			$tpl = self::callback_array("#\{RP\[([a-zA-Z0-9\-_]+)\]\}#", ("templates::routeparam"), $tpl);
			$tpl = self::callback_array("#\{R_\[(.+?)\]\[(.+?)\]\}#", ("templates::route"), $tpl);
			$tpl = self::callback_array("#\{R_\[(.+?)\]\}#", ("templates::route"), $tpl);
			if(preg_match("#\{S_langdata=['\"](.+?)['\"](|,['\"](.*?)['\"])(|,true|false)\}#i", $tpl)) {
				$tpl = self::callback_array("#\{S_langdata=['\"](.+?)['\"](|,['\"](.*?)['\"])(|,(true|false))\}#i", "templates::langdate", $tpl);
			}
			if(preg_match("#\{S_langdate=['\"](.+?)['\"](|,['\"](.*?)['\"])(|,true|false)\}#i", $tpl)) {
				$tpl = self::callback_array("#\{S_langdate=['\"](.+?)['\"](|,['\"](.*?)['\"])(|,(true|false))\}#i", "templates::langdate", $tpl);
			}
			$tpl = self::callback_array("#\{F_['\"](.+?)['\"],['\"](.+?)['\"],['\"](.+?)['\"]\}#", "templates::declension", $tpl);
		}

		if($type=="all" || $type =="lcud" || $type =="ecomp") {
			$tpl = self::callback_array("#\{L_([\"|']|)([a-zA-Z0-9\-_]+)(\\1)\[([a-zA-Z0-9\-_]*?)\]\}#", ("templates::lang"), $tpl);
			$tpl = self::callback_array("#\{L_()([a-zA-Z0-9\-_]+)()\[([a-zA-Z0-9\-_]*?)\]\}#", ("templates::lang"), $tpl);
			$tpl = self::callback_array("#\{L_()([a-zA-Z0-9\-_]+)()\[(.*?)\]\}#", ("templates::lang"), $tpl);
			$tpl = self::callback_array("#\{L_([\"|']|)(.+?)(\\1)\}#", ("templates::lang"), $tpl);
			$tpl = self::callback_array("#\{L_()(.+?)()\}#", ("templates::lang"), $tpl);
			$tpl = self::callback_array("#\{C_([a-zA-Z0-9\-_\.]+)\[([a-zA-Z0-9\-_]*?)\]\[([a-zA-Z0-9\-_]*?)\]\}#", ("templates::config"), $tpl);
			$tpl = self::callback_array("#\{C_([a-zA-Z0-9\-_\.]+)\[([a-zA-Z0-9\-_]*?)\]\}#", ("templates::config"), $tpl);
			$tpl = self::callback_array("#\{C_([a-zA-Z0-9\-_\.]+)\}#", ("templates::config"), $tpl);
			$tpl = self::callback_array("#\{U_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#", ("templates::user"), $tpl);
			$tpl = self::callback_array("#\{U_([a-zA-Z0-9\-_]+)\}#", ("templates::user"), $tpl);
			$tpl = self::callback_array("#\{D_([a-zA-Z0-9\-_]+)\}#", ("templates::define"), $tpl);
			$tpl = self::callback_array("#\{S_data=['\"](.+?)['\"](|,['\"](.*?)['\"])\}#", ("templates::sys_date"), $tpl);
			$tpl = self::callback_array("#\{S_([a-zA-Z0-9\-_]+)\}#", ("templates::systems"), $tpl);
			$tpl = self::callback_array("#\{M_\[(.+?)\]\}#", ("templates::checkMobile"), $tpl);
		}

		if($type=="ecomp") {
			$tpl = self::callback_array('#\[if (.+?)\](.*?)\[else \\1\](.*?)\[/if \\1\]#i', ("templates::is"), $tpl);
			while(preg_match('~\[if (.+?)\]([^[]*)\[/if \\1\]~iU', $tpl)) {
				$tpl = self::callback_array('~\[if (.+?)\]([^[]*)\[/if \\1\]~iU', ("templates::is"), $tpl);
			}
			$tpl = self::callback_array("#\\[if (.+?)\\](.*?)\\[else\\](.*?)\\[/if\\]#i", ("templates::is"), $tpl);
			$tpl = self::callback_array('~\[if (.+?)\]([^[]*)\[/if\]~iU', ("templates::is"), $tpl);
			while(preg_match('~\[if (.+?)\]([^[]*)\[/if\]~iU', $tpl)) {
				$tpl = self::callback_array('~\[if (.+?)\]([^[]*)\[/if\]~iU', ("templates::is"), $tpl);
			}
		}

		if($type=="all") {
			$tpl = self::callback_array("#\{FMK_(['\"])(.*?)\[(.*?)\]\}#", ("templates::fmk"), $tpl);
			$tpl = self::callback_array("#\{M_\[(.+?)\]\}#", ("templates::checkMobile"), $tpl);
			$tpl = self::callback_array("#\{foreach\}([0-9]+)\{/foreach\}#i", ("templates::foreach_set"), $tpl);
			$tpl = self::callback_array("#\\[foreach block=(.+?)\\](.+?)\\[/foreach $1\\]#is", ("templates::foreachs"), $tpl);
			$tpl = self::callback_array("#\\[foreach block=(.+?)\\](.+?)\\[/foreach\\]#is", ("templates::foreachs"), $tpl);
			$tpl = preg_replace("#\{foreach\}([0-9]+)\{/foreach\}#i", "", $tpl);
			$tpl = self::callback_array("#\{count\[(.*?)\]\}#is", ("templates::countforeach"), $tpl);
			$array_use = array();
			foreach(self::$blocks as $name => $val) {
				if(is_array($name)) {
					continue;
				}
				if(!is_array($val) && strpos($tpl, "{".$name."}") !== false) {
					$tpl = str_replace("{".$name."}", $val, $tpl);
					$array_use[$name] = $val;
				} else {
					$tpl = self::callback_array("/{(.+?)\[(.+?)\]}/", ("templates::replace_tmp"), $tpl);
				}
			}

			$tpl = self::callback_array('#\[for ([0-9]+) to ([0-9]+)(| step=([0-9]+))\](.+?)\[/for\]#is', ("templates::fors"), $tpl);
			$tpl = self::callback_array("#\\[ajax\\]([\s\S]*?)\\[else\\]([\s\S]*?)\\[/ajax\\]#i", ("templates::ajax"), $tpl);
			$tpl = self::callback_array("#\\[ajax\\]([\s\S]*?)\\[/ajax\\]#i", ("templates::ajax"), $tpl);
			$tpl = self::callback_array("#\\[ajax_click\\]([\s\S]*?)\\[/ajax_click\\]#i", ("templates::ajax_click"), $tpl);
			$tpl = self::callback_array("#\\[!ajax_click\\]([\s\S]*?)\\[/!ajax_click\\]#i", ("templates::ajax_click"), $tpl);
			$tpl = self::callback_array("#\\[!ajax\\]([\s\S]*?)\\[/!ajax\\]#i", ("templates::ajax"), $tpl);

			$tpl = self::callback_array('#\[if (.*?)\]([\s\S]*?)\[else \\1\]([\s\S]*?)\[/if \\1\]#i', ("templates::is"), $tpl);
			$tpl = self::callback_array('#\[if (.*?)\]([\s\S]*?)\[/if \\1\]#i', ("templates::is"), $tpl);

			$tpl = self::callback_array("#\\[if (.*?)\\]([\s\S]*?)\\[else\\]([\s\S]*?)\\[/if\\]#i", ("templates::is"), $tpl);
			$tpl = self::callback_array("#\\[if (.*?)\\]([\s\S]*?)\\[/if\\]#i", ("templates::is"), $tpl);

			$tpl = self::callback_array("#\\[module_(.+?)\\](.+?)\\[/module_(.+?)\\]#i", ("templates::is"), $tpl);
			$tpl = self::callback_array("#\\[module_(.+?)\\](.+?)\\[/module_(.+?)\\]#i", ("templates::is"), $tpl);
			$tpl = str_replace("{THEME}", config::Select("default_http_local").self::$dir_skins."/".self::$skins, $tpl);
			$tpl = self::callback_array("#\{IMG_[\"'](.+?)[\"']\}#", ("templates::imageRes"), $tpl);
			$tpl = self::callback_array("#\{FN_[\"'](.+?)[\"'](,[\"'](.*?)[\"'])\}#", "templates::callFn", $tpl);
			$tpl = self::callback_array("#\{RETINA_[\"'](.+?)[\"'],[\"'](.+?)[\"'](|,[\"'](.+?)[\"'])\}#", ("templates::retinaImg"), $tpl);
			if(strpos($tpl, "[clear]") !== false) {
				foreach($array_use as $name => $val) {
					unset(self::$blocks[$name]);
				}
				unset($array_use);
				$tpl = str_replace("[clear]", "", $tpl);
			}
		}

		$tpl = self::callback_array("#\{E_\[(.+?)\](|\[(.+?)\])\}#", ("templates::execEvent"), $tpl);
		if(function_exists("execEvent")) {
			$tpl = execEvent("templates::compile::after", $tpl, $file, $type);
		}

		return $tpl;
	}

	private static function execEvent($arr) {
		$args = array($arr[1], false);
		if(isset($arr[3])) {
			$exp = explode(";", $arr[3]);
			$size = 2;
			for($i=0;$i<sizeof($exp);$i++) {
				$exp[$i] = explode("=", $exp[$i]);
				if(isset($exp[$i][0]) && isset($exp[$i][1])) {
					$args[$exp[$i][0]] = $exp[$i][1];
				} if(isset($exp[$i][0]) && !isset($exp[$i][1])) {
					$args[$exp[$i][0]] = true;
				} if(!isset($exp[$i][0]) && isset($exp[$i][1])) {
					$args[$size] = $exp[$i][1];
				}
				$size++;
			}
		}
		$ret = call_user_func_array("cardinalEvent::execute", $args);
		return ($ret===false ? "" : $ret);
	}

	/**
	 * Completed special system variables
	 * @access public
	 * @param string $tmp Template for needed complete
	 * @return array|mixed|NUll Result completed
     */
	final public static function lcud($tmp) {
		$tmp = self::comp_datas($tmp, "null", "lcud");
	return $tmp;
	}

	/**
	 * Quick viewing template
	 * @access public
	 * @param string $data Template for completed
	 * @param string|array $header List headers or title
	 * @return array|mixed|NUll|string Result completed
     */
	final public static function view($data, $header = "") {
		$old = self::$tmp;
		self::complited($data, $header);
		$h = self::$tmp;
		self::$tmp = $old;
		$h = str_replace("{THEME}", config::Select("default_http_local").self::$dir_skins."/".self::$skins, $h);
		if(config::Select("manifestCache")) {
			$h = self::linkToHeader($h);
		}
		return self::ecomp($h);
	}

	/**
	 * Prepare template for viewing
	 * @access public
	 * @param string $tmp Completed template
	 * @param array|string $header List headers or title
	 */
	final public static function templates($tmp, $header = "") { return self::completed($tmp, $header); }

	/**
	 * Block data for replace
	 * @access public
	 * @param string $name Name replace
	 * @param string|array $var Value replace
     */
	final public static function set_block($name, $var) {
		if(is_array($var) && sizeof($var)>0) {
			foreach($var as $key => $vars) {
				self::set_block($key, $vars);
			}
		} else {
			self::$editor[$name] = $var;
		}
	}

	// Gorlum's minifier BOF
	/**
	* Minifies template w/i PHP code by removing extra spaces
	* @access private
	*/
	final private static function minify($html, $force = false) {
		if(!$force && !modules::get_config('tpl_minifier')) {
			return $html;
		}
		// TODO: Match <code> and <pre> too - in separate arrays
		preg_match_all('/(<pre[^>]*?>.*?<\/pre>)/si', $html, $pre);
		preg_match_all('/(<script[^>]*?>.*?<\/script>)/si', $html, $script);
		preg_match_all('/(<textarea[^>]*?>.*?<\/textarea>)/si', $html, $textarea);
		$i=0;
		while(preg_match('/(<pre[^>]*?>.*?<\/pre>)/si', $html)) {
			$html = preg_replace('/(<pre[^>]*?>.*?<\/pre>)/si', '#pre'.$i.'#', $html, 1);
			$i++;
		}
		$i=0;
		while(preg_match('/(<script[^>]*?>.*?<\/script>)/si', $html)) {
			$html = preg_replace('/(<script[^>]*?>.*?<\/script>)/si', '#script'.$i.'#', $html, 1);
			$i++;
		}
		$i=0;
		while(preg_match('/(<textarea[^>]*?>.*?<\/textarea>)/si', $html)) {
			$html = preg_replace('/(<textarea[^>]*?>.*?<\/textarea>)/si', '#textarea'.$i.'#', $html, 1);
			$i++;
		}
		$html = preg_replace('#<!-(.*?)\[(if|endif)(.*?)\](.*?)>#', '<#!-$1[$2$3]$4>', $html);
		$html = preg_replace('#<!(.*?)\[(if|endif)(.*?)\](.*?)>#', '<#!$1[$2$3]$4>', $html);
		$html = preg_replace('#<!-[^\[].+?->#s', '', $html);//ToDo: WTF?!
		while(preg_match('#<\#!(.*?)\[(if|endif)(.*?)\](.*?)>#', $html)) {
			$html = preg_replace('#<\#!(.*?)\[(if|endif)(.*?)\](.*?)>#', '<!$1[$2$3]$4>', $html);
		}
		$html = preg_replace('/[\r\n\t]+/', ' ', $html);
		$html = preg_replace('/>[\s]*</', '><', $html); // Strip spacechars between tags
		$html = preg_replace('/[\s]+/', ' ', $html); // Replace several spacechars with one space
		if(!empty($pre[0])) {
			$i=0;
			foreach($pre[0] as $tag) {
				$html = preg_replace('/#pre'.$i.'#/', $tag, $html, 1);
				unset($pre[0][$i]);
				$i++;
			}
		}
		if(!empty($script[0])) {
			$i=0;
			foreach($script[0] as $tag) {
				$tag = preg_replace('/^\ *\/\/[^\<]*?$/m', ' ', $tag); // Strips comments - except those that contains HTML comment inside
				$tag = preg_replace('/[\ \t]{2,}/', ' ', $tag); // Replace several spaces by one
				$tag = preg_replace('/\s{2,}/', "\r\n", $tag); // Replace several linefeeds by one
				$html = preg_replace('/#script'.$i.'#/', $tag, $html, 1);
				unset($script[0][$i]);
				$i++;
			}
		}
		if(!empty($textarea[0])) {
			$i=0;
			foreach($textarea[0] as $tags) {
				$tags = preg_replace('/^\ *\/\/[^\<]*?$/m', ' ', $tags);
				$tags = preg_replace('/[\ \t]{3,}/', ' ', $tags);
				$html = preg_replace('/#textarea'.$i.'#/', $tags, $html, 1);
				$i++;
			}
		}
	return $html;
	}
	// Gorlum's minifier EOF
	
	final public static function error($mess) {
		if(self::check_exists(self::$mainTpl, self::$skins)) {
			self::assign_var("message", $mess);
			$h = self::completed_assign_vars(self::$mainTpl, (!empty(self::$mainSkins) ? self::$mainSkins : "null"));
		} else {
			$h = $mess;
		}
		self::completed($h);
		self::display();
	}

	/**
	 * Style error template
	 * @param string $msg Error message
     */
	final private static function ErrorTemplate($msg, $file) {
		errorHeader();
		$type = "main";
		$orFile = $file;
		if(strpos($file, ADMINCP_DIRECTORY)!==false) {
			$type = "admin";
			$file = str_replace(ADMINCP_DIRECTORY.DS."temp".DS, "", $file);
		}
		$file = str_replace(ROOT_PATH, "", $file);
		$file = str_replace(DS, "/", $file);
		if($type=="admin" && config::Select("skins", "admincp")=="xenon") {
			$file = str_replace(config::Select("skins", "admincp")."/", "", $file);
			$pr = new Parser();
			$pr->header();
			$pr->header_array();
			$pr->timeout(3);
			$pr->init();
			$pr->get("https://killserver.github.io/ForCardinal/admin/xenon/".$file);
			$hr = $pr->getHeaders();
			if($hr['code']===200) {
				try {
					$file = $pr->getHTML();
					file_put_contents($orFile, $file);
					header("Location: ".$_SERVER['REQUEST_SCHEME']."://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
					die();
				} catch(Exception $ex) {}
			}
		}
		if(file_exists(PATH_SKINS."ErrorTpl.".self::$typeTpl)) {
			$file = file_get_contents(PATH_SKINS."ErrorTpl.".self::$typeTpl);
			$file = str_replace("{msg}", $msg, $file);
			echo $file;
		} else {
			echo $msg;
		}
	}

	final public static function changeMain($tpl = "") {
		if(empty($tpl)) {
			return self::$mainTpl;
		} else {
			self::$mainTpl = $tpl;
			return true;
		}
	}

	final public static function changeTypeTpl($type = "") {
		if(empty($type)) {
			return self::$typeTpl;
		} else {
			self::$typeTpl = $type;
			return true;
		}
	}
	
	final public static function multipleHead() {
		$ret = '<!--[if lt IE 7 ]> <html class="ie6"> <![endif]-->'.PHP_EOL.'<!--[if IE 7 ]> <html class="ie7"> <![endif]-->'.PHP_EOL.'<!--[if IE 8 ]> <html class="ie8"> <![endif]-->'.PHP_EOL.'<!--[if IE 9]> <html class="ie9"> <![endif]-->'.PHP_EOL.'<html class="no-js">';
		return $ret;
	}

	/**
	 * Display done completed page
	 * @access public
     */
	final public static function display() {
	global $lang;
		$time = self::time();
		if(!self::check_exists(self::$mainTpl, self::$skins) && !self::check_exists(self::$mainTpl, self::$mainSkins)) {
			self::ErrorTemplate("error templates", ROOT_PATH."".self::$dir_skins.DS.self::$skins.DS.self::$mainTpl.".".self::$typeTpl);
			return;
		}
		if(!defined("ROOT_EX")) {
			$phpEx = substr(strrchr(__FILE__, '.'), 1);
			if(empty($phpEx)) {
				$phpEx = "php";
			}
		} else {
			$phpEx = ROOT_EX;
		}
		if(function_exists("execEventRef")) {
			execEventRef("templates::display");
		}
		if(self::check_exists("tpl", self::$skins.DS."lang", $phpEx)) {
			include_once(ROOT_PATH."".self::$dir_skins.DS.self::$skins.DS."lang".DS."tpl.".$phpEx);
		}
		$h = self::completed_assign_vars(self::$mainTpl, (!empty(self::$mainSkins) ? self::$mainSkins : "null"));
		if(function_exists("execEvent")) {
			$h = execEvent("templates::mainPageLoad", $h);
		}
		if(self::check_exists("login", self::$skins)) {
			$l = self::load_templates("login", modules::get_config("charset"), self::$skins);
			$h = str_replace("{login}", $l, $h);
		}
		$head = "";
		if(isset(self::$module['head']['before'])) {
			$head .= self::$module['head']['before'];
		}
		$no_js = false;
		$no_css = false;
		$headerInit = false;
		if(strpos($h, "{create_js}")!==false) {
			$no_js = true;
			if($headerInit===false) {
				$header = new Headers();
			}
			$h = str_replace("{create_js}", $header->create_js(), $h);
		}
		if(strpos($h, "{create_css}")!==false) {
			$no_css = true;
			if($headerInit===false) {
				$header = new Headers();
			}
			$h = str_replace("{create_css}", $header->create_css(), $h);
		}
		$head .= headers(self::$header, false, $no_js, $no_css);
		if(isset(self::$module['head']['after'])) {
			$head .= self::$module['head']['after'];
		}
		if(strpos($head, '<meta name="theme-color"')===false) {
			$head .= '<meta name="theme-color" content="#AC1F1F">';
		}
		$h = str_replace("{headers}", $head, $h);

		$body = "";
		if(isset(self::$module['body']['before'])) {
			$body .= self::$module['body']['before'];
		}
		$body .= self::$tmp;
		if(isset(self::$module['body']['after'])) {
			$body .= self::$module['body']['after'];
		}
		if(isset($_GET['jajax']) || (getenv('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest' && isset($_GET['jajax']))) {
			unset($h);
			$thead = "{L_sitename}";
			if(isset(self::$header['title'])) {
				$thead = self::$header['title'];
			}
			$thead = "<div id=\"pretitle\">".$thead."</div>";
			$h = $thead.$body;
		} else {
			$h = str_replace("{content}", $body, $h);
		}
		$h = str_replace("{THEME}", config::Select("default_http_local").self::$dir_skins."/".self::$skins, $h);
		$h = self::ecomp($h);//, self::$dir_skins.DS.self::$skins.DS."main"
		$find_preg = $replace_preg = array();
		if(sizeof(self::$editor)) {
			foreach(self::$editor as $key_find => $key_replace) {
				$find_preg[] = $key_find;
				$replace_preg[] = $key_replace;
			}
			$h = preg_replace($find_preg, $replace_preg, $h);
		}
		if(config::Select("manifestCache")) {
			$h = self::linkToHeader($h);
		} else if(config::Select("fastLoad")) {
			$h = self::linkToHeader($h, false);
		}
		$h = self::callback_array("#\{<html>\}#", ("templates::multipleHead"), $h);
		if(!preg_match("#<html.*?lang=['\"](.+?)['\"].*?>#iU", $h)) {
			$rLang = Route::param('lang');
			if(!$rLang && !empty($rLang)) {
				$lang = $rLang;
			} else {
				$lang = config::Select("lang");
			}
			$h = preg_replace("#<html(.*?)>#is", "<html lang=\"".$lang."\"$1>", $h);
		}
		if(!preg_match("#<html.*?prefix=['\"](.+?)['\"].*?>#is", $h)) {
			$arr = array();
			$prefix = config::Select("htmlPrefix");
			foreach($prefix as $namespace => $link) {
				$arr[] = $namespace.": ".$link."#";
			}
			$h = preg_replace("#<html(.*?)>#is", "<html$1 prefix=\"".implode(" ", $arr)."\">", $h);
		}
		$h = self::minify($h);
		$h = cardinalEvent::execute("templates::display", $h);
		HTTP::echos($h);
		unset($h, $body, $lang);
		self::clean();
		if(function_exists("memory_get_usage")) {
			echo "<!-- ".round((memory_get_usage()/1024/1024), 2)." MB -->";
		}
		self::$time += self::time()-$time;
	}

	final private static function linkToHeader($tpl, $manifestAccess = true) {
		if(!isset($_SERVER['REQUEST_URI'])) {
			return $tpl;
		}
		$scripts = $css = array();
		preg_match_all("#<link(.*?)href=['\"](.+?)['\"](.*?)>#is", $tpl, $link);
		if(isset($link[2]) && is_Array($link[2]) && sizeof($link[2])>0) {
			for($i=0;$i<sizeof($link[2]);$i++) {
				if(strpos($link[2][$i], ".ico")===false && strpos($link[2][$i], ".png")===false && strpos($link[2][$i], ".jpg")===false && strpos($link[2][$i], ".jpeg")===false) {
					$lk = "<link".$link[1][$i]."href=\"".$link[2][$i]."\"".$link[3][$i].">";
					if(strpos($lk, "stylesheet")===false) {
						continue;
					}
					$lk = str_replace("rel='stylesheet'", "rel=\"preload\" as=\"style\" onload=\"this.onload=null;this.rel='stylesheet'\"", $lk);
					$lk = str_replace("rel='stylesheet'", "rel='preload' as='style' onload='this.onload=null;this.rel=\'stylesheet\''", $lk);
					$tpl = str_replace($link[0][$i], $lk, $tpl);
					$css[$link[2][$i]] = true;
				}
			}
		}
		preg_match_all("#<script.*?src=['\"](.+?)['\"].*?>#i", $tpl, $link);
		if(isset($link[1]) && is_Array($link[1]) && sizeof($link[1])>0) {
			for($i=0;$i<sizeof($link[1]);$i++) {
				if(strpos($link[1][$i], ".ico")===false && strpos($link[1][$i], ".png")===false && strpos($link[1][$i], ".jpg")===false && strpos($link[1][$i], ".jpeg")===false) {
					$scripts[$link[1][$i]] = true;
				}
			}
		}
		$scripts = array_keys($scripts);
		$scripts = array_unique($scripts);
		$scripts = array_values($scripts);
		$css = array_keys($css);
		$css = array_unique($css);
		$css = array_values($css);
		for($i=0;$i<sizeof($scripts);$i++) {
			header("link: <".$scripts[$i].">; rel=preload; as=script", false);
		}
		for($i=0;$i<sizeof($css);$i++) {
			header("link: <".$css[$i].">; rel=preload; as=style", false);
		}
		if($manifestAccess && file_exists(PATH_MANIFEST) && is_dir(PATH_MANIFEST) && file_exists(PATH_MANIFEST.md5($_SERVER['REQUEST_URI']).".txt")) {
			return file_get_contents(PATH_MANIFEST.md5($_SERVER['REQUEST_URI']).".txt");
		}
		if($manifestAccess && file_exists(PATH_MANIFEST) && is_dir(PATH_MANIFEST) && is_writable(PATH_MANIFEST)) {
			$linkAll = array_merge($scripts, $css);
			if(isset($linkAll) && is_Array($linkAll) && sizeof($linkAll)>0) {
				$linkAll = serialize($linkAll);
				$md5 = var_export($linkAll, true);
				$md5 = md5($md5);
				if(!file_exists(PATH_MANIFEST.$md5.".txt")) {
					file_put_contents(PATH_MANIFEST.$md5.".txt", $linkAll);
				}
				$tpl = preg_replace("#<html(.*?)>#is", "<html$1 manifest=\"".config::Select("default_http_local")."manifest.cache?f=".urlencode($md5)."\">", $tpl);// type=\"text/cache-manifest\"
			}
		}
		return $tpl;
	}

	/**
	 * Clear all data in template
	 * @access public
     */
	final public static function clean() {
		self::$blocks = array();
		self::$foreach = array("count" => 0, "all" => array());
		self::$module = array("head" => array(), "body" => array(), "blocks" => array());
		self::$editor = array();
		self::$header = null;
		self::$tmp = "";
		self::$skins = "";
	}

}

?>
