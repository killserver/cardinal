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
	private static $module = array("head" => array(), "body" => array(), "blocks" => array(), "menu" => array());
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
	private static $pathToCache = "";
	private static $mainSkins = "";

	/**
	 * templates constructor.
	 * @param array $config Configuration template
     */
	final public function __construct($config = array()) {
		self::SetConfig($config);
		self::$pathToCache = PATH_CACHE_TEMP;
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
		if(file_exists(PATH_SKINS.self::$skins.DS."functions.".PHP_EX)) {
			include_once(PATH_SKINS.self::$skins.DS."functions.".PHP_EX);
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

	/**
	 * Set array datas for template
	 * @access public
	 * @param array $array Array data for template
	 * @param string $block Block data for cycle
	 * @param string $view Unique id for block data
     */
	final public static function assign_vars($array, $block = "", $view = "") {
		if(empty($block)) {
			foreach($array as $name => $value) {
				self::$blocks[$name] = $value;
			}
		} elseif(!empty($view)) {
			foreach($array as $name => $value) {
				self::$blocks[$block][$view][$name] = $value;
			}
		} else {
			foreach($array as $name => $value) {
				self::$blocks[$block][$name] = $value;
			}
		}
	}
	
	final public static function resetVars($array, $view = "") {
		if(empty($view)) {
			unset(self::$blocks[$array]);
		} else {
			unset(self::$blocks[$array][$view]);
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
	 * Set data for template
	 * @access public
	 * @param string $name Name data for template
	 * @param string $value Value data for template
	 * @param string $block Block data for create array
     */
	final public static function assign_var($name, $value, $block = "") {
		if(empty($block)) {
			self::$blocks[$name] = $value;
		} else {
			self::$blocks[$block][$name] = $value;
		}
	}
	
	final public static function resetVar($name, $block = "") {
		if(empty($block)) {
			unset(self::$blocks[$name]);
		} else {
			unset(self::$blocks[$block][$name]);
		}
	}

	/**
	 * Set datas for menu
	 * @access public
	 * @param string $name Name menu on template
	 * @param string $html HTML-code for template
	 * @param string $block Block menu
     */
	final public static function set_menu($name, $html = "", $block = "") {
		if(empty($block)) {
			self::$module['menu'][$name] = $html;
		} else {
			self::$module['menu'][$name][$block] = array("name" => $block, "value" => $html);
		}
	}

	/**
	 * Get menu datas
	 * @access public
	 * @param string $name Name menu
	 * @param string $block Block menu
	 * @return mixed Return data on menu
     */
	final public static function select_menu($name, $block) {
		if(isset(self::$module['menu'][$name][$block]) && is_array(self::$module['menu'][$name][$block]) && isset(self::$module['menu'][$name][$block]['value'])) {
			return self::$module['menu'][$name][$block]['value'];
		} else {
			return false;
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
			if(isset($array[2])) {
				$isset = config::Select($array[1], $array[2]);
			} else {
				$isset = config::Select($array[1]);
			}
		} else {
			global $config;
			if(isset($array[2])) {
				$isset = (isset($config[$array[1]]) && isset($config[$array[1]][$array[2]]) ? $config[$array[1]][$array[2]] : false);
			} else {
				$isset = (isset($config[$array[1]]) ? $config[$array[1]] : false);
			}
		}
		if(!empty($isset)) {
			return $isset;
		} else {
			return $array[0];
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
	 * Review language data
	 * @access private
	 * @param array $array Datas language and he's parameters
	 * @return string Return reviewed language data or original line
     */
	final private static function slangf($array) {
		if(isset($array[5])) {
			$decode = $array[5];
			$vLang = modules::get_lang($array[2], $array[4]);
		} else {
			$decode = $array[4];
			$vLang = modules::get_lang($array[2]);
		}
		if(!empty($vLang)) {
			if(strpos(base64_decode($decode), ",") !==false) {
				$arrays = explode(",", base64_decode($decode));
				return self::sprintf($vLang, $arrays);
			} else {
				return sprintf($vLang, base64_decode($decode));
			}
		} else {
			return "{L_".$array[2]."}";
		}
	}

	/**
	 * Get constant
	 * @access private
	 * @param array $array Get constant
	 * @return string Return data in constant or original line
     */
	final private static function define($array) {
		if(defined($array[1])) {
			return constant($array[1]);
		} else {
			$def = defines::all();
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
	 * Include created php-file template
	 * @access private
	 * @param string $tpl Rebuild template
	 * @param string $file Execute template file
	 * @return bool|string Return result including file
     */
	final private static function ParseTemp($tpl, $file) {
		$del = false;
		if($file==self::$dir_skins.DS.self::$skins.DS."null") {
			$del = true;
		}
		$file = str_replace(array("/", DS, "-", ".."), array("-", "_", "_", "_"), $file);
		$file = self::$pathToCache.$file.".".ROOT_EX;
		$tpl = self::minify($tpl, true);
		if(!file_exists($file)) {
			file_put_contents($file, '<?php if(!defined("IS_CORE")) { echo "403 ERROR"; die(); } ?>'.$tpl);
		}
		if(file_exists($file)) {
			ob_start();
			$data = self::$blocks;
			require($file);
			$file_content = ob_get_clean();
		} else {
			return false;
		}
		if($del && file_exists($file)) {
			unlink($file);
		}
		return $file_content;
	}

	/**
	 * Re-Rebuild back in template
	 * @access private
	 * @param string $tpl Rebuild line
	 * @return mixed Return re-rebuilding
     */
	final private static function RebuildOffPhp($tpl) {
		$tpl = preg_replace("#<!-- FOREACH (.+?) -->#", '[foreach block=\\1]', $tpl);
		$tpl = preg_replace("#<!-- ENDFOREACH (.+?) -->#", '[/foreach]', $tpl);
		$tpl = preg_replace("#<!-- ENDFOREACH -->#", '[/foreach]', $tpl);
		$tpl = preg_replace("#<!-- IF (.+?) -->#", "[if \\1]", $tpl);
		$tpl = preg_replace("#<!-- ELSE -->#", "[else]", $tpl);
		$tpl = preg_replace("#<!-- ELSEIF (.+?) -->#", "[else \\1]", $tpl);
		$tpl = preg_replace("#<!-- ENDIF -->#", "[/if]", $tpl);
		$tpl = preg_replace("#<!-- ENDIF (.+?) -->#", "[/if \\1]", $tpl);

		$tpl = preg_replace("#\{% L_sprintf\(([\"|']|)([a-zA-Z0-9\-_]+)(\\1)\[([a-zA-Z0-9\-_]*?)\],(.*?)\) %\}#", '{L_sprintf(\\2[\\4],\\5)}', $tpl);
		$tpl = preg_replace("#\{% L_sprintf\(()(.+?)()\[([a-zA-Z0-9\-_]*?)\],(.*?)\) %\}#", '{L_sprintf(\\2[\\4],\\5)}', $tpl);
		$tpl = preg_replace("#\{% L_sprintf\(([\"|']|)(.+?)(\\1),(.*?)\) %\}#", '{L_sprintf(\\2,\\4)}', $tpl);
		$tpl = preg_replace("#\{% L_sprintf\(()(.+?)(),(.*?)\) %\}#", '{L_sprintf(\\2,\\4)}', $tpl);
		$tpl = preg_replace("#\{% L_([\"|']|)([a-zA-Z0-9\-_]+)([\"|']|)\[([a-zA-Z0-9\-_]*?)\] %\}#", '{L_\\2[\\4]}', $tpl);
		$tpl = preg_replace("#\{% L_([\"|']|)(.+?)(\\1) %\}#", '{L_\\2}', $tpl);
		$tpl = preg_replace("#\{% L_()(.+?)() %\}#", '{L_\\2}', $tpl);
		$tpl = preg_replace("#\{% C_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\] %\}#", '{C_\\1[\\2]}', $tpl);
		$tpl = preg_replace("#\{% C_([a-zA-Z0-9\-_]+) %\}#", '{C_\\1}', $tpl);
		$tpl = preg_replace("#\{% U_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\] %\}#", '{U_\\1[\\2]}', $tpl);
		$tpl = preg_replace("#\{% U_([a-zA-Z0-9\-_]+) %\}#", '{U_\\1}', $tpl);
		$tpl = preg_replace("#\{% D_([a-zA-Z0-9\-_]+) %\}#", '{D_\\1}', $tpl);
		$tpl = preg_replace("#\{% RP\[(.+?)\] %\}#", '{RP[\\1]}', $tpl);
		$tpl = preg_replace("#\{% R_\[(.+?)\]\[(.+?)\] %\}#", '{R_[\\1][\\2]}', $tpl);
		$tpl = preg_replace("#\{\$ R_\[(.+?)\]\[(.+?)\] \$\}#", '{R_[\\1][\\2]}', $tpl);
		$tpl = preg_replace("#\{% R_\[(.+?)\] %\}#", '{R_[\\1]}', $tpl);
		$tpl = preg_replace("#\{\$ R_\[(.+?)\] \$\}#", '{R_[\\1]}', $tpl);
		$tpl = preg_replace("#\{% M_\[(.+?)\] %\}#", '{M_[\\1]}', $tpl);

		$tpl = preg_replace("#\{% ([a-zA-Z0-9\-_]+)\.([a-zA-Z0-9\-_]+) %\}#is", '{\\1.\\2}', $tpl);
		$tpl = preg_replace("#\{% ([a-zA-Z0-9\-_]+) %\}#is", '{\\1}', $tpl);


		$tpl = preg_replace("#\{\# ([a-zA-Z0-9\-_]+)\.([a-zA-Z0-9\-_]+) \#\}#is", '{\\1.\\2}', $tpl);
		$tpl = preg_replace("#\{\# ([a-zA-Z0-9\-_]+) \#\}#is", '{\\1}', $tpl);
		if(defined("PERMISSION_PHP")) {
			$tpl = preg_replace('#\<\?php(.*?)\?\>#isU', "", $tpl);
		}
		return $tpl;
	}

    /**
     * Check equals mobile
     * @param string $type Type equals type if mobile, tablet or desktop
     * @return int Result checking
     */
    final private static function checkMobileExec($type) {
	global $mobileDetect;
		if(!in_array($type, array('desktop', 'tablet', 'mobile'))) {
			throw trigger_error("ERROR check type device");
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

	/**
	 * Rebuild template in php-file
	 * @access private
	 * @param string $tpl Original template
	 * @param string $file Execute file template
	 * @return mixed Result rebuild and including file
     */
	final private static function ParsePHP($tpl, $file = "") {
		if(!config::Select("ParsePHP") || !file_exists(self::$pathToCache) || !is_dir(self::$pathToCache) || !is_writable(self::$pathToCache)) {
			return self::RebuildOffPhp($tpl);
		}
		if(empty($file)) {
			return $tpl;
		}
		$exp = str_replace(array("/", DS, "-", ".."), array("-", "_", "_", "_"), $file);
		if(file_exists($file) && file_exists(self::$pathToCache.$exp.".md5")) {
			$md5 = file_get_contents(self::$pathToCache.$exp.".md5");
			if($md5 == md5($tpl)) {
				return self::ParseTemp($tpl, $file);
			}
		} else {
			$md5 = md5($tpl);
			file_put_contents(self::$pathToCache.$exp.".md5", $md5);
		}
		unset($md5);
		if(!defined("PERMISSION_PHP")) {
			$safe = array(
				"<?php" => "&lt;?php",
				"<?" => "&lt;?",
				"?>" => "?&gt;",
			);
			$tpl = str_replace(array_keys($safe), array_values($safe), $tpl);
			if(strpos($tpl, "&lt;?xml")!==false) {
				$safe = array(
					"&lt;?xml" => '<?php echo \'<?xml\'; ?>',
					"?&gt;" => '<?php echo \'?>\'; ?>',
				);
				$tpl = str_replace(array_keys($safe), array_values($safe), $tpl);
			}
		} else {
			if(strpos($tpl, "<?xml")!==false) {
				$safe = array(
					"<?xml" => '<?php echo \'<?xml\'; ?>',
				);
				$tpl = str_replace(array_keys($safe), array_values($safe), $tpl);
			}
		}
		$tpl = preg_replace("#<!-- FOREACH (.+?) -->#", '<?php if(isset($data[\'\\1\']) && is_array($data[\'\\1\']) && sizeof($data[\'\\1\'])>0) { foreach($data[\'\\1\'] as $\\1) { ?>', $tpl);
		$tpl = preg_replace("#<!-- ENDFOREACH (.+?) -->#", '<?php } } ?>', $tpl);
		$tpl = preg_replace("#<!-- ENDFOREACH -->#", '<?php } } ?>', $tpl);
		$tpl = preg_replace("#<!-- IF (.+?) -->#", "<?php if(\\1) { ?>", $tpl);
		$tpl = preg_replace("#<!-- ELSE -->#", "<?php } else { ?>", $tpl);
		$tpl = preg_replace("#<!-- ELSEIF (.+?) -->#", "<?php } elseif(\\1) { ?>", $tpl);
		$tpl = preg_replace("#<!-- ENDIF (.+?) -->#", "<?php } ?>", $tpl);
		$tpl = preg_replace("#<!-- ENDIF -->#", "<?php } ?>", $tpl);

		$tpl = preg_replace("#\{% L_sprintf\(([\"|']|)([a-zA-Z0-9\-_]+)([\"|']|)\[([a-zA-Z0-9\-_]*?)\],(.*?)\) %\}#", 'templates::slangf(array(null, \'\', \'\\2\', \'\', \'\\4\', \'\\5\'))', $tpl);
		$tpl = preg_replace("#\{% L_sprintf\(([\"|']|)(.+?)([\"|']|),(.*?)\) %\}#", 'templates::slangf(array(null, \'\', \'\\2\', \'\', \'\\4\'))', $tpl);
		$tpl = preg_replace("#\{% L_([\"|']|)([a-zA-Z0-9\-_]+)([\"|']|)\[([a-zA-Z0-9\-_]*?)\] %\}#", 'templates::lang(array(null, \'\', \'\\2\', \'\', \'\\4\'))', $tpl);
		$tpl = preg_replace("#\{% L_([\"|']|)(.+?)([\"|']|) %\}#", 'templates::lang(array(null, \'\', \'\\2\'))', $tpl);
		$tpl = preg_replace("#\{% L_()(.+?)() %\}#", 'templates::lang(array(null, \'\', \'\\2\', \'\'))', $tpl);
		$tpl = preg_replace("#\{% C_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\] %\}#", 'config::Select(\'\\1\', \'\\2\')', $tpl);
		$tpl = preg_replace("#\{% C_([a-zA-Z0-9\-_]+) %\}#", 'config::Select(\'\\1\')', $tpl);
		$tpl = preg_replace("#\{% U_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\] %\}#", 'templates::user(array(null, \'\\1\', \'\\2\'))', $tpl);
		$tpl = preg_replace("#\{% U_([a-zA-Z0-9\-_]+) %\}#", 'templates::user(array(null, \'\\1\'))', $tpl);
		$tpl = preg_replace("#\{% D_([a-zA-Z0-9\-_]+) %\}#", 'templates::define(array(null, \'\\1\'))', $tpl);
		$tpl = preg_replace("#\{% R_\[(.+?)\]\[(.+?)\] %\}#", 'templates::route(array(null, "\\1", "\\2"))', $tpl);
		$tpl = preg_replace("#\{% M_\[(.+?)\] %\}#", 'templates::checkMobile(array(null, "\\1"))', $tpl);
		$tpl = preg_replace("#\{% RP\[(.+?)\] %\}#", 'templates::routeparam(array(null, \'\\1\'))', $tpl);

		$tpl = preg_replace("#\{\@ ([a-zA-Z0-9\-_]+)\.([a-zA-Z0-9\-_]+) \@\}#is", '(isset($\\1[\'\\2\']) ? $\\1[\'\\2\'] : \'\')', $tpl);
		$tpl = preg_replace("#\{% ([a-zA-Z0-9\-_]+)\.([a-zA-Z0-9\-_]+) %\}#is", '$\\1[\'\\2\']', $tpl);
		$tpl = preg_replace("#\{% ([a-zA-Z0-9\-_]+) %\}#is", '$data[\'\\1\']', $tpl);

		$tpl = preg_replace("#\{L_sprintf\(([\"|']|)([a-zA-Z0-9\-_]+)(\\1)\[([a-zA-Z0-9\-_]*?)\],(.*?)\)\}#", '<?php echo templates::slangf(array(null, \'\', \'\\2\', \'\', \'\\4\', \'\\5\')); ?>', $tpl);
		$tpl = preg_replace("#\{L_sprintf\(()([a-zA-Z0-9\-_]+)()\[([a-zA-Z0-9\-_]*?)\],(.*?)\)\}#", '<?php echo templates::slangf(array(null, \'\', \'\\2\', \'\', \'\\4\', \'\\5\')); ?>', $tpl);
		$tpl = preg_replace("#\{L_sprintf\(([\"|']|)(.+?)(\\1),(.*?)\)\}#", '<?php echo templates::slangf(array(null, \'\', \'\\2\', \'\', \'\\4\')); ?>', $tpl);
		$tpl = preg_replace("#\{L_sprintf\(()(.+?)(),(.*?)\)\}#", '<?php echo templates::slangf(array(null, \'\', \'\\2\', \'\', \'\\4\')); ?>', $tpl);
		$tpl = preg_replace("#\{L_([\"|']|)([a-zA-Z0-9\-_]+)(\\1)\[([a-zA-Z0-9\-_]*?)\]\}#", '<?php echo templates::lang(array(null, \'\', \'\\2\', \'\', \'\\4\')); ?>', $tpl);
		$tpl = preg_replace("#\{L_()([a-zA-Z0-9\-_]+)()\[([a-zA-Z0-9\-_]*?)\]\}#", '<?php echo templates::lang(array(null, \'\', \'\\2\', \'\', \'\\4\')); ?>', $tpl);
		$tpl = preg_replace("#\{L_()([a-zA-Z0-9\-_]+)()\[(.*?)\]\}#", '<?php echo templates::lang(array(null, \'\', \'\\2\', \'\', \'\\4\')); ?>', $tpl);
		$tpl = preg_replace("#\{L_([\"|']|)(.+?)(\\1)\}#", '<?php echo templates::lang(array(null, \'\', \'\\2\')); ?>', $tpl);
		$tpl = preg_replace("#\{L_()(.+?)()\}#", '<?php echo templates::lang(array(null, \'\', \'\\2\')); ?>', $tpl);
		$tpl = preg_replace("#\{C_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#", '<?php echo config::Select(\'\\1\', \'\\2\'); ?>', $tpl);
		$tpl = preg_replace("#\{C_([a-zA-Z0-9\-_]+)\}#", '<?php echo config::Select(\'\\1\'); ?>', $tpl);
		$tpl = preg_replace("#\{U_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#", '<?php echo templates::user(array(null, \'\\1\', \'\\2\')); ?>', $tpl);
		$tpl = preg_replace("#\{U_([a-zA-Z0-9\-_]+)\}#", '<?php echo templates::user(array(null, \'\\1\')); ?>', $tpl);
		$tpl = preg_replace("#\{D_([a-zA-Z0-9\-_]+)\}#", '<?php echo templates::define(array(null, \'\\1\')); ?>', $tpl);
		$tpl = preg_replace("#\{RP\[(.+?)\]\}#", '<?php echo templates::routeparam(array(null, \'\\1\')); ?>', $tpl);
		$tpl = preg_replace("#\{R_\[(.+?)\]\[(.+?)\]\}#", '<?php echo templates::route(array(null, "\\1", "\\2")); ?>', $tpl);
		$tpl = preg_replace("#\{R_\[(.+?)\]\}#", '<?php echo templates::route(array(null, "\\1")); ?>', $tpl);
		$tpl = preg_replace("#\{\@ R_\[(.+?)\]\[(.+?)\] \@\}#", '<?php echo templates::route(array(null, \'\\1\', \\2)); ?>', $tpl);
		$tpl = preg_replace("#\{\@ R_\[(.+?)\] \@\}#", '<?php echo templates::route(array(null, \'\\1\')); ?>', $tpl);
		$tpl = preg_replace("#\{M_\[(.+?)\]\}#", '<?php echo templates::checkMobile(array(null, \'\\1\')); ?>', $tpl);

		$tpl = preg_replace("#\{\# ([a-zA-Z0-9\-_]+)\.([a-zA-Z0-9\-_]+) \#\}#is", '<?php echo (isset($\\1[\'\\2\']) ? $\\1[\'\\2\'] : \'{\\1.\\2}\'); ?>', $tpl);
		$tpl = preg_replace("#\{\# ([a-zA-Z0-9\-_]+) \#\}#is", '<?php echo (isset($data[\'\\1\']) ? $data[\'\\1\'] : \'{\\1}\'); ?>', $tpl);
		$tpl = self::ParseTemp($tpl, $file);
		return $tpl;
	}

	/**
	 * "Last" compiling template before he view
	 * @access private
	 * @param string $tmp Template for last rebuild
	 * @param string $file Execute file
	 * @return array|mixed|NUll|string Result rebuild
     */
	final private static function ecomp($tmp, $file = "") {
		$tmp = preg_replace("/\/\/\/\*\*\*(.+?)\*\*\*\/\/\//is", "", $tmp);
		// while(strpos($tmp, "///***")!==false && strpos($tmp, "***///")!==false) {
		//	$tmp = nsubstr($tmp, 0, nstrpos($tmp, "///***")).nsubstr($tmp, nstrpos($tmp, "***///")+6, nstrlen($tmp));
		// }
		$tmp = self::ParsePHP($tmp, $file);
		$tmp = self::callback_array("#\{include (.+?)=['\"](.*?)['\"]\}#", ("templates::includeFile"), $tmp);
		$tmp = preg_replace("~\{\#is_last\[(\"|)(.*?)(\"|)\]\}~", "\\1", $tmp);
		$tmp = self::callback_array("#\\[(not-group)=(.+?)\\](.+?)\\[/not-group\\]#is", ("templates::group"), $tmp);
		$tmp = self::callback_array("#\\[(group)=(.+?)\\](.+?)\\[/group\\]#is", ("templates::group"), $tmp);
		$tmp = self::callback_array("#\{L_sprintf\(([\"|']|)([a-zA-Z0-9\-_]+)(\\1)\[([a-zA-Z0-9\-_]*?)\],(.*?)\)\}#", ("templates::slangf"), $tmp);
		$tmp = self::callback_array("#\{L_sprintf\(()([a-zA-Z0-9\-_]+)()\[([a-zA-Z0-9\-_]*?)\],(.*?)\)\}#", ("templates::slangf"), $tmp);
		$tmp = self::callback_array("#\{L_sprintf\(([\"|']|)(.+?)(\\1),(.*?)\)\}#", ("templates::slangf"), $tmp);
		$tmp = self::callback_array("#\{L_sprintf\(()(.+?)(),(.*?)\)\}#", ("templates::slangf"), $tmp);
		$tmp = self::callback_array("#\{L_([\"|']|)([a-zA-Z0-9\-_]+)(\\1)\[([a-zA-Z0-9\-_]*?)\]\}#", ("templates::lang"), $tmp);
		$tmp = self::callback_array("#\{L_()([a-zA-Z0-9\-_]+)()\[([a-zA-Z0-9\-_]*?)\]\}#", ("templates::lang"), $tmp);
		$tmp = self::callback_array("#\{L_()([a-zA-Z0-9\-_]+)()\[(.*?)\]\}#", ("templates::lang"), $tmp);
		$tmp = self::callback_array("#\{L_([\"|']|)(.+?)(\\1)\}#", ("templates::lang"), $tmp);
		$tmp = self::callback_array("#\{L_()(.+?)()\}#", ("templates::lang"), $tmp);
		$tmp = self::callback_array("#\{LP_\[(.*?)\]\[(.*?)\](|\[(.*?)\])\}#", ("plural_form"), $tmp);
		$tmp = self::callback_array("#\{C_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#", ("templates::config"), $tmp);
		$tmp = self::callback_array("#\{C_([a-zA-Z0-9\-_]+)\}#", ("templates::config"), $tmp);
		$tmp = self::callback_array("#\{U_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#", ("templates::user"), $tmp);
		$tmp = self::callback_array("#\{U_([a-zA-Z0-9\-_]+)\}#", ("templates::user"), $tmp);
		$tmp = self::callback_array("#\{D_([a-zA-Z0-9\-_]+)\}#", ("templates::define"), $tmp);
		$tmp = self::callback_array("#\{RP\[(.+?)\]\}#", ("templates::routeparam"), $tmp);
		$tmp = self::callback_array("#\{R_\[(.+?)\]\[(.+?)\]\}#", ("templates::route"), $tmp);
		$tmp = self::callback_array("#\{R_\[(.+?)\]\}#", ("templates::route"), $tmp);
		$tmp = self::callback_array("#\{M_\[(.+?)\]\}#", ("templates::checkMobile"), $tmp);
		$tmp = str_replace("{reg_link}", config::Select('link', 'reg'), $tmp);
		$tmp = str_replace("{login_link}", config::Select('link', 'login'), $tmp);
		$tmp = str_replace("{logout-link}", config::Select('link', 'logout'), $tmp);
		$tmp = str_replace("{lost_link}", config::Select('link', 'lost'), $tmp);
		$tmp = str_replace("{login}", modules::get_user('username'), $tmp);
		$tmp = str_replace("{addnews-link}", config::Select('link', 'add'), $tmp);
		$tmp = str_replace("{lostpassword-link}", config::Select('link', 'recover'), $tmp);
		$tmp = self::callback_array("#\{UL_(.*?)(|\[(.*?)\])\}#", ("templates::level"), $tmp);

		$tmp = self::callback_array('#\[page=(.*?)\]([^[]*)\[/page\]#i', ("templates::nowpage"), $tmp);
		$tmp = self::callback_array('#\[not-page=(.*?)\]([^[]*)\[/not-page\]#i', ("templates::npage"), $tmp);

		$tmp = self::callback_array('#\[if (.+?)\](.*?)\[else \\1\](.*?)\[/if \\1\]#i', ("templates::is"), $tmp);
		while(preg_match('~\[if (.+?)\]([^[]*)\[/if \\1\]~iU', $tmp)) {
			$tmp = self::callback_array('~\[if (.+?)\]([^[]*)\[/if \\1\]~iU', ("templates::is"), $tmp);
		}

		$tmp = self::callback_array("#\\[if (.+?)\\](.*?)\\[else\\](.*?)\\[/if\\]#i", ("templates::is"), $tmp);
		$tmp = self::callback_array('~\[if (.+?)\]([^[]*)\[/if\]~iU', ("templates::is"), $tmp);
		while(preg_match('~\[if (.+?)\]([^[]*)\[/if\]~iU', $tmp)) {
			$tmp = self::callback_array('~\[if (.+?)\]([^[]*)\[/if\]~iU', ("templates::is"), $tmp);
		}
		$tmp = self::callback_array("#\{S_data=['\"](.+?)['\"](|,['\"](.*?)['\"])\}#", ("templates::sys_date"), $tmp);
		if(preg_match("#\{S_langdata=['\"](.+?)['\"](|,['\"](.*?)['\"])(|,true)\}#", $tmp)) {
			$tmp = self::callback_array("#\{S_langdata=['\"](.+?)['\"](|,['\"](.*?)['\"])(|,true)\}#", "langdate", $tmp);
		}
		$tmp = self::callback_array("#\{S_([a-zA-Z0-9\-_]+)\}#", ("templates::systems"), $tmp);
		return $tmp;
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
		} elseif(strpos($array[1], "empty(") !== false) {
			$type = "empty";
			$e = preg_replace("/empty(.+?)/", "$1", $array[1]);
			$e = str_replace(array("(", ")"), "", $e);
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
			if((empty($e) || $else) && $good) {
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
			if((!empty($e) || isset(self::$blocks[$e]) || $else) && $good) {
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
		}
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
		if(strpos($file[0], self::$typeTpl) === false) {
			$file[0] = $file[0].".".self::$typeTpl;
		}
		if(!isset($file[1])) {
			$dir = ROOT_PATH."".self::$dir_skins.DS.self::$skins.DS.$file[0];
		} elseif(isset($file[1]) && !empty($file[1])) {
			$dir = ROOT_PATH."".self::$dir_skins.DS.$file[1].DS.$file[0];
		} else {
			$dir = ROOT_PATH."".self::$dir_skins.DS.$file[0];
		}
		$files = $array[0];
		try {
			$files = file_get_contents($dir);
			$files = self::comp_datas($files, $file[0]);
		} catch(Exception $ex) {}
		return $files;
	}

	/**
	 * Include module
	 * @access private
	 * @param array $array Array data for initialize module-file
	 * @return mixed Result work module or original line
     */
	final private static function include_module($array) {
		if(strpos($array[1], ".".ROOT_EX) === false) {
			$array[1] = $array[1].".".ROOT_EX;
		}
		if(strpos($array[1], ",") !== false) {
			$exp = explode(",", $array[1]);
			$ret = $exp[1];
			$array[1] = $exp[0];
		} else {
			$ret = $array[0];
		}
		$class = str_replace(array(".class", ".".ROOT_EX), "", $array[1]);
		if(!file_exists(PATH_MODULES.$array[1]) && !file_exists(PATH_MODULES."autoload".DS.$array[1])) {
			return $ret;
		}
		if(!class_exists($class)) {
			return $ret;
		}
		if(!method_exists($class, "start")) {
			return $ret;
		}
		$mod = new $class();
		return $mod->start();
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
		return sizeof(self::$blocks[$array[2]]);
	}

	/**
	 * "Not In Page". Check user without this page
	 * @access private
	 * @param array $array Array data
	 * @return mixed Return part template if user without this page
     */
	final private static function npage($array) {
	global $manifest;
		if(strpos($array[1], "|")!==false) {
			$search = explode("|", $array[1]);
		} else {
			$search = array($array[1]);
		}
		if(!in_array($manifest['mod_page'][HTTP::getip()]['page'], $search)) {
			return $array[2];
		}
		return "";
	}

	/**
	 * "In Page". Check user on this page
	 * @access private
	 * @param array $array Array data
	 * @return mixed Return part template if user on this page
     */
	final private static function nowpage($array) {
	global $manifest;
		if(strpos($array[1], "|")!==false) {
			$search = explode("|", $array[1]);
		} else {
			$search = array($array[1]);
		}
		if(in_array($manifest['mod_page'][HTTP::getip()]['page'], $search)) {
			return $array[2];
		}
		return "";
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

	/**
	 * Final completed template
	 * @access public
	 * @param string $tpl File template
	 * @param string $file Sub file for completed
	 * @param bool|false $test ToDo: WTF?!
	 * @return array|mixed|NUll Done completed
     */
	public static function comp_datas($tpl, $file = "null", $test = false) {
		$tpl = self::ParsePHP($tpl, self::$dir_skins.DS.self::$skins.DS.$file);
		$tpl = self::callback_array("#\{include (.+?)=['\"](.*?)['\"]\}#", ("templates::includeFile"), $tpl);
		$tpl = preg_replace(array('~\{\#\#(.+?)}~', '~\{\#(.+?)}~', '~\{\_(.+?)}~'), "{\\1}", $tpl);
		$tpl = self::callback_array("~\{is_last\[(\"|)(.+?)(\"|)\]\}~", ("templates::count_blocks"), $tpl);
		$tpl = self::callback_array("#\{C_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#", ("templates::config"), $tpl);
		$tpl = self::callback_array("#\{C_([a-zA-Z0-9\-_]+)\}#", ("templates::config"), $tpl);
		$tpl = self::callback_array("#\{U_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#", ("templates::user"), $tpl);
		$tpl = self::callback_array("#\{U_([a-zA-Z0-9\-_]+)\}#", ("templates::user"), $tpl);
		$tpl = self::callback_array("#\{D_([a-zA-Z0-9\-_]+)\}#", ("templates::define"), $tpl);
		$tpl = self::callback_array("#\{UL_(.*?)(|\[(.*?)\])\}#", ("templates::level"), $tpl);
		$tpl = self::callback_array("#\{FMK_(['\"])(.*?)\[(.*?)\]\}#", ("templates::fmk"), $tpl);
		$tpl = self::callback_array("#\{M_\[(.+?)\]\}#", ("templates::checkMobile"), $tpl);
		$tpl = self::callback_array("#\{RP\[([a-zA-Z0-9\-_]+)\]\}#", ("templates::routeparam"), $tpl);
		$tpl = self::callback_array('#\[page=(.*?)\](.*)\[/page\]#i', ("templates::nowpage"), $tpl);
		$tpl = self::callback_array('#\[not-page=(.*?)\](.*)\[/not-page\]#i', ("templates::npage"), $tpl);
		if(modules::manifest_get(array("temp", "block"))!==false) {
			self::$blocks = array_merge(self::$blocks, modules::manifest_get(array("temp", "block")));
		}
		$tpls = $tpl;
		foreach(self::$blocks as $name => $val) {
			if(is_array($name)) {
				continue;
			}
			if(!is_array($val) && strpos($tpl, "{".$name."}") !== false) {
				$tpls = str_replace("{".$name."}", $val, $tpls);
			} else {
				$tpls = self::callback_array("/{(.+?)\[(.+?)\]}/", ("templates::replace_tmp"), $tpls);
			}
		}
if(!$test) {
		$tpl = self::callback_array("#\{foreach\}([0-9]+)\{/foreach\}#i", ("templates::foreach_set"), $tpl);
		$tpl = self::callback_array("#\\[foreach block=(.+?)\\](.+?)\\[/foreach $1\\]#is", ("templates::foreachs"), $tpls);
		$tpl = self::callback_array("#\\[foreach block=(.+?)\\](.+?)\\[/foreach\\]#is", ("templates::foreachs"), $tpls);
		$tpl = preg_replace("#\{foreach\}([0-9]+)\{/foreach\}#i", "", $tpl);
		$tpl = self::callback_array("#\{count\[(.*?)\]\}#is", ("templates::countforeach"), $tpl);
}
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

		$tpl = self::callback_array("#\{S_data=['\"](.+?)['\"](|,['\"](.*?)['\"])\}#", ("templates::sys_date"), $tpl);
		$tpl = self::callback_array("#\{S_([a-zA-Z0-9\-_]+)\}#", ("templates::systems"), $tpl);
		$tpl = self::callback_array("#\\[module_(.+?)\\](.+?)\\[/module_(.+?)\\]#i", ("templates::is"), $tpl);
		$tpl = self::callback_array("#\\[module_(.+?)\\](.+?)\\[/module_(.+?)\\]#i", ("templates::is"), $tpl);
		$tpl = str_replace("{THEME}", config::Select("default_http_local").self::$dir_skins."/".self::$skins, $tpl);
		if(strpos($tpl, "[clear]") !== false) {
			foreach($array_use as $name => $val) {
				unset(self::$blocks[$name]);
			}
			unset($array_use);
			$tpl = str_replace("[clear]", "", $tpl);
		}
		return $tpl;
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

	final public static function loadObject($obj) {
		if(!is_object($obj)) {
			self::ErrorTemplate("First parameter is not object");
			die();
		}
		$arr = get_class_vars($obj);
		$i = 0;
		foreach($arr as $k => $v) {
			if(is_object($v) && $v instanceof DBObject) {
				$v = $v->getArray();
				self::loadObject($v);
			} else if(is_object($v)) {
				self::loadObject($v);
			} else if(is_array($v)) {
				self::assign_vars($v, $k, $k.$i);
			} else {
				self::assign_var($k, $v);
			}
			$i++;
		}
		return true;
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
	final public static function load_templates($file, $charset = "", $dir = "null") {
		$time = self::time();
		if($dir == "null") {
			if(!file_exists(ROOT_PATH."".self::$dir_skins.DS.self::$skins.DS.$file.".".self::$typeTpl)) {
				self::ErrorTemplate("File \"".ROOT_PATH."".self::$dir_skins.DS.self::$skins.DS.$file.".".self::$typeTpl."\" is not exists", ROOT_PATH."".self::$dir_skins.DS.self::$skins.DS.$file.".".self::$typeTpl);
				die();
			}
			$tpl = file_get_contents(ROOT_PATH."".self::$dir_skins.DS.self::$skins.DS.$file.".".self::$typeTpl);
		} elseif($dir=="admin") {
			if(!file_exists(ROOT_PATH.ADMINCP_DIRECTORY.DS."temp".DS.$file.".".self::$typeTpl)) {
				self::ErrorTemplate("File \"".ROOT_PATH.ADMINCP_DIRECTORY.DS."temp".DS.$file.".".self::$typeTpl."\" is not exists", ROOT_PATH.ADMINCP_DIRECTORY.DS."temp".DS.$file.".".self::$typeTpl);
				die();
			}
			$tpl = file_get_contents(ROOT_PATH.ADMINCP_DIRECTORY.DS."temp".DS.$file.".".self::$typeTpl);
		} elseif(empty($dir)) {
			if(!file_exists(ROOT_PATH."".self::$dir_skins.DS.$file.".".self::$typeTpl)) {
				self::ErrorTemplate("File \"".ROOT_PATH."".self::$dir_skins.DS.$file.".".self::$typeTpl."\" is not exists", ROOT_PATH."".self::$dir_skins.DS.$file.".".self::$typeTpl);
				die();
			}
			$tpl = file_get_contents(ROOT_PATH."".self::$dir_skins.DS.$file.".".self::$typeTpl);
		} else {
			if(!file_exists(ROOT_PATH."".self::$dir_skins.DS.$dir.DS.$file.".".self::$typeTpl)) {
				self::ErrorTemplate("File \"".ROOT_PATH."".self::$dir_skins.DS.$dir.DS.$file.".".self::$typeTpl."\" is not exists", ROOT_PATH."".self::$dir_skins.DS.$dir.DS.$file.".".self::$typeTpl);
				die();
			}
			$tpl = file_get_contents(ROOT_PATH."".self::$dir_skins.DS.$dir.DS.$file.".".self::$typeTpl);
		}
		if(!empty($charset)) {
			$tpl = iconv($charset, modules::get_config("charset"), $tpl);
		}
		self::$time += self::time()-$time;
		return $tpl;
	}

	/**
	 * Completed template for finally using
	 * @access public
	 * @param string $file File template
	 * @param string $dir Directory template
	 * @param bool|false $test //ToDo: WTF?!
	 * @return array|mixed|NUll|string Completed template
     */
	final public static function completed_assign_vars($file, $dir = "null", $test = false) {
		$time = self::time();
		if($dir == "null") {
			try {
				$tpl = file_get_contents(ROOT_PATH."".self::$dir_skins.DS.self::$skins.DS.$file.".".self::$typeTpl);
			} catch(Exception $ex) {
				self::ErrorTemplate("File \"".ROOT_PATH."".self::$dir_skins.DS.self::$skins.DS.$file.".".self::$typeTpl."\" is not exists", ROOT_PATH."".self::$dir_skins.DS.self::$skins.DS.$file.".".self::$typeTpl);
				die();
			}
		} elseif(empty($dir)) {
			try {
				$tpl = file_get_contents(ROOT_PATH."".self::$dir_skins.DS.$file.".".self::$typeTpl);
			} catch(Exception $ex) {
				self::ErrorTemplate("File \"".ROOT_PATH."".self::$dir_skins.DS.$file.".".self::$typeTpl."\" is not exists", ROOT_PATH."".self::$dir_skins.DS.$file.".".self::$typeTpl);
				die();
			}
		} else {
			try {
				$tpl = file_get_contents(ROOT_PATH."".self::$dir_skins.DS.$dir.DS.$file.".".self::$typeTpl);
			} catch(Exception $ex) {
				self::ErrorTemplate("File \"".ROOT_PATH."".self::$dir_skins.DS.$dir.DS.$file.".".self::$typeTpl."\" is not exists", ROOT_PATH."".self::$dir_skins.DS.$dir.DS.$file.".".self::$typeTpl);
				die();
			}
		}
		$tpl = self::comp_datas($tpl, $file, $test);
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
	 * @param bool|false $test //ToDo: WTF?!
	 * @return array|mixed|NUll|string Completed template
	 */
	final public static function complited_assing_vars($file, $dir = "null", $test = false) { return self::completed_assign_vars($file, $dir, $test); }

	final public static function check_exists($file, $dir = "null") {
		if($dir == "null") {
			return file_exists(ROOT_PATH."".self::$dir_skins.DS.self::$skins.DS.$file.".".self::$typeTpl);
		} elseif(empty($dir)) {
			return file_exists(ROOT_PATH."".self::$dir_skins.DS.$file.".".self::$typeTpl);
		} else {
			return file_exists(ROOT_PATH."".self::$dir_skins.DS.$dir.DS.$file.".".self::$typeTpl);
		}
	}

	/**
	 * Prepare template for viewing
	 * @access public
	 * @param string $tmp Completed template
	 * @param array|string $header List headers or title
     */
	final public static function completed($tmp, $header = "") {
	global $manifest;
		$time = self::time();
		if(!is_array($header)) {
			$header = array(
				"title" => $header,
				"meta" => array(
					"og" => array(
						"title" => $header,
						"description" => "{L_s_description}",
					),
					"ogpr" => array(
						"og:title" => $header,
						"og:description" => "{L_s_description}",
					),
					"description" => "{L_s_description}",
				),
			);
		}
		if(!is_array(self::$header)) {
			self::$header = array();
		}
		if(!self::$isChangeHead) {
			self::$header = array_merge(self::$header, $header);
		} else {
			self::$header = array_merge($header, self::$header);
		}
		$manifest['mod_page'][HTTP::getip()]['title'] = self::$header['title'];
		modules::manifest_set(array('mod_page', HTTP::getip(), 'title'), self::$header['title']);
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
	 * Completed special system variables
	 * @access public
	 * @param string $tmp Template for needed complete
	 * @return array|mixed|NUll Result completed
     */
	final public static function lcud($tmp) {
		$tmp = self::callback_array("#\{L_sprintf\(([\"|']|)([a-zA-Z0-9\-_]+)(\\1)\[([a-zA-Z0-9\-_]*?)\],(.*?)\)\}#", ("templates::slangf"), $tmp);
		$tmp = self::callback_array("#\{L_sprintf\(()([a-zA-Z0-9\-_]+)()\[([a-zA-Z0-9\-_]*?)\],(.*?)\)\}#", ("templates::slangf"), $tmp);
		$tmp = self::callback_array("#\{L_sprintf\(([\"|']|)(.+?)(\\1),(.*?)\)\}#", ("templates::slangf"), $tmp);
		$tmp = self::callback_array("#\{L_sprintf\(()(.+?)(),(.*?)\)\}#", ("templates::slangf"), $tmp);
		$tmp = self::callback_array("#\{L_([\"|']|)([a-zA-Z0-9\-_]+)(\\1)\[([a-zA-Z0-9\-_]*?)\]\}#", ("templates::lang"), $tmp);
		$tmp = self::callback_array("#\{L_()([a-zA-Z0-9\-_]+)()\[([a-zA-Z0-9\-_]*?)\]\}#", ("templates::lang"), $tmp);
		$tmp = self::callback_array("#\{L_()([a-zA-Z0-9\-_]+)()\[(.*?)\]\}#", ("templates::lang"), $tmp);
		$tmp = self::callback_array("#\{L_([\"|']|)(.+?)(\\1)\}#", ("templates::lang"), $tmp);
		$tmp = self::callback_array("#\{L_()(.+?)()\}#", ("templates::lang"), $tmp);
		$tmp = self::callback_array("#\{C_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#", ("templates::config"), $tmp);
		$tmp = self::callback_array("#\{C_([a-zA-Z0-9\-_]+)\}#", ("templates::config"), $tmp);
		$tmp = self::callback_array("#\{U_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#", ("templates::user"), $tmp);
		$tmp = self::callback_array("#\{U_([a-zA-Z0-9\-_]+)\}#", ("templates::user"), $tmp);
		$tmp = self::callback_array("#\{D_([a-zA-Z0-9\-_]+)\}#", ("templates::define"), $tmp);
		$tmp = self::callback_array("#\{S_data=['\"](.+?)['\"](|,['\"](.*?)['\"])\}#", ("templates::sys_date"), $tmp);
		$tmp = self::callback_array("#\{S_([a-zA-Z0-9\-_]+)\}#", ("templates::systems"), $tmp);
		$tmp = self::callback_array("#\{M_\[(.+?)\]\}#", ("templates::checkMobile"), $tmp);
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
		self::complited($data, $header);
		$h = self::$tmp;
		self::$tmp = "";
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
	 * View error page
	 * @access public
	 * @param string $data Error message
	 * @param string|array $header Title or array headers
     */
	final public static function error($data, $header = "") {
		if(!is_array($header)) {
			self::$header = array("title" => $header);
		} else {
			self::$header = $header;
		}
		if(is_array(self::$header) && !isset(self::$header['title'])) {
			self::$header['title'] = "";
		}
		if(isset(self::$header['code']) && self::$header['code']=="404") {
			$sapi_name = php_sapi_name();
			if($sapi_name == 'cgi' OR $sapi_name == 'cgi-fcgi') {
				header('Status: 404 Not Found');
			}
			header('HTTP/1.1 404 Not Found');
		}
		self::$blocks = array_merge(self::$blocks, array("error" => $data, "title" => (isset(self::$header['title']) ? self::$header['title'] : "")));
		self::$tmp = self::completed_assign_vars("info");
		self::display();
		exit();
	}

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

	/**
	 * Style error template
	 * @param string $msg Error message
     */
	final private static function ErrorTemplate($msg, $file) {
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
		$ret = '<!--[if lt IE 7 ]> <html class="ie6"> <![endif]-->'.PHP_EOL.'<!--[if IE 7 ]> <html class="ie7"> <![endif]-->'.PHP_EOL.'<!--[if IE 8 ]> <html class="ie8"> <![endif]-->'.PHP_EOL.'<!--[if IE 9 ]> <html class="ie9"> <![endif]-->'.PHP_EOL.'<!--[if (gt IE 9)|!(IE)]><!--> <html class="no-ie"> <!--<![endif]-->';
		return $ret;
	}

	/**
	 * Display done completed page
	 * @access public
     */
	final public static function display() {
	global $lang;
		$time = self::time();
		if(!file_exists(ROOT_PATH."".self::$dir_skins.DS.self::$skins.DS.self::$mainTpl.".".self::$typeTpl) && !file_exists(ROOT_PATH."".self::$dir_skins.DS.self::$mainSkins.DS.self::$mainTpl.".".self::$typeTpl)) {
			self::ErrorTemplate("error templates", ROOT_PATH."".self::$dir_skins.DS.self::$skins.DS.self::$mainTpl.".".self::$typeTpl);
			return;
		}
		if(file_exists(ROOT_PATH."".self::$dir_skins.DS.self::$skins.DS."lang".DS."tpl.".ROOT_EX)) {
			include_once(ROOT_PATH."".self::$dir_skins.DS.self::$skins.DS."lang".DS."tpl.".ROOT_EX);
		}
		$h = self::completed_assign_vars(self::$mainTpl, (!empty(self::$mainSkins) ? self::$mainSkins : "null"));
		if(file_exists(ROOT_PATH."".self::$dir_skins.DS.self::$skins.DS."login.".self::$typeTpl)) {
			$l = file_get_contents(ROOT_PATH."".self::$dir_skins.DS.self::$skins.DS."login.".self::$typeTpl);
			$l = iconv("cp1251", modules::get_config('charset'), $l);
			$h = str_replace("{login}", $l, $h);
		}
		$head = "";
		if(isset(self::$module['head']['before'])) {
			$head .= self::$module['head']['before'];
		}
		if(strpos($h, "{create_js}")!==false) {
			$head .= headers(self::$header, false, true);
			$h = str_replace("{create_js}", create_js(), $h);
		} else {
			$head .= headers(self::$header);
		}
		if(isset(self::$module['head']['after'])) {
			$head .= self::$module['head']['after'];
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
		if(strpos($h, "{meta_tt}")!==false) {
			if(isset(self::$header['meta_body'])) {
				$mtt = meta(self::$header['meta_body']);
				$h = str_replace("{meta_tt}", $mtt, $h);
				unset($mtt);
			} else {
				$h = str_replace("{meta_tt}", meta(), $h);
			}
		}
		if(isset($_GET['jajax']) || (getenv('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest' && isset($_GET['jajax']))) {
			unset($h);
			$thead = "<div id=\"pretitle\">{L_sitename}</div>";
			if(isset(self::$header['title'])) {
				$thead = "<div id=\"pretitle\">".self::$header['title']."</div>";
			}
			$h = $thead.$body;
		} else {
			$h = str_replace("{content}", $body, $h);
		}
		$tmp = modules::manifest_get(array("temp", "menu"));
		if(is_array($tmp) && sizeof($tmp)>0) {
			self::$module['menu'] = array_merge(self::$module['menu'], $tmp);
		}
		unset($tmp);
		if(sizeof(self::$module['menu'])>0) {
			foreach(self::$module['menu'] as $name => $val) {
				if(!is_array($name) && !is_array($val)) {
					$h = str_replace("{".$name."}", self::comp_datas($val), $h);
				} elseif(!is_array($name) && is_array($val)) {
					$values = array_values($val);
					$value = "";
					for($i=0;$i<sizeof($values);$i++) {
						$value .= $values[$i]['value'];
					}
					$h = str_replace("{".$name."}", self::comp_datas($value), $h);
				}
			}
			self::$module['menu'] = array();
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
		}
		$h = self::minify($h);
		$h = self::callback_array("#\{<html>\}#", ("templates::multipleHead"), $h);
		if(!preg_match("#<html.*?lang=['\"](.+?)['\"].*?>#is", $h)) {
			$rLang = Route::param('lang');
			if(!$rLang && !empty($rLang)) {
				$lang = $rLang;
			} else {
				$lang = config::Select("lang");
			}
			$h = preg_replace("#<html(.*?)>#is", "<html lang=\"".$lang."\"$1>", $h);
		}
		$h = cardinalEvent::execute("templates::display", $h);
		HTTP::echos($h);
		unset($h, $body, $lang);
		self::clean();
		if(function_exists("memory_get_usage")) {
			echo "<!-- ".round((memory_get_usage()/1024/1024), 2)." MB -->";
		}
		self::$time += self::time()-$time;
	}

	final private static function linkToHeader($tpl) {
		$linkAll = array();
		preg_match_all("#<link.*?href=['\"](.+?)['\"].*?>#is", $tpl, $link);
		if(isset($link[1]) && is_Array($link[1]) && sizeof($link[1])>0) {
			$link[1] = array_unique($link[1]);
			$link[1] = array_values($link[1]);
			for($i=0;$i<sizeof($link[1]);$i++) {
				if(strpos($link[1][$i], ".ico")===false && strpos($link[1][$i], ".png")===false && strpos($link[1][$i], ".jpg")===false && strpos($link[1][$i], ".jpeg")===false) {
					header("link: <".$link[1][$i].">; rel=preload; as=style", false);
				} else {
					unset($link[1][$i]);
				}
			}
			$linkAll = array_merge($linkAll, $link[1]);
		}
		preg_match_all("#<script.*?src=['\"](.+?)['\"].*?>#is", $tpl, $link);
		if(isset($link[1]) && is_Array($link[1]) && sizeof($link[1])>0) {
			$link[1] = array_unique($link[1]);
			$link[1] = array_values($link[1]);
			for($i=0;$i<sizeof($link[1]);$i++) {
				if(strpos($link[1][$i], ".ico")===false && strpos($link[1][$i], ".png")===false && strpos($link[1][$i], ".jpg")===false && strpos($link[1][$i], ".jpeg")===false) {
					header("link: <".$link[1][$i].">; rel=preload; as=script", false);
				} else {
					unset($link[1][$i]);
				}
			}
			$linkAll = array_merge($linkAll, $link[1]);
		}
		if(file_exists(PATH_UPLOADS."manifest".DS) && is_dir(PATH_UPLOADS."manifest".DS) && is_writable(PATH_UPLOADS."manifest".DS)) {
			preg_match_all("#<img.*?src=['\"](.+?)['\"].*?>#is", $tpl, $link);
			if(isset($link[1]) && is_Array($link[1]) && sizeof($link[1])>0) {
				$link[1] = array_unique($link[1]);
				$link[1] = array_values($link[1]);
				$linkAll = array_merge($linkAll, $link[1]);
			}
			for($i=0;$i<sizeof($linkAll);$i++) {
				$time = time();
				if(strpos($linkAll[$i], "".$time)!==false) {
					$linkAll[$i] = str_replace($time, "", $linkAll[$i]);
				}
				if(strpos($linkAll[$i], "&")!==false && strpos($linkAll[$i], "&amp;")===false) {
					$linkAll[$i] = str_replace("&", "&amp;", $linkAll[$i]);
				}
			}
			$linkAll = array_unique($linkAll);
			$linkAll = array_values($linkAll);
			if(isset($linkAll) && is_Array($linkAll) && sizeof($linkAll)>0) {
				$linkAll = serialize($linkAll);
				$md5 = var_export($linkAll, true);
				$md5 = md5($md5);
				if(!file_exists(PATH_UPLOADS."manifest".DS.$md5.".txt")) {
					file_put_contents(PATH_UPLOADS."manifest".DS.$md5.".txt", $linkAll);
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
		self::$module = array("head" => array(), "body" => array(), "blocks" => array(), "menu" => array());
		self::$editor = array();
		self::$header = null;
		self::$tmp = "";
		self::$skins = "";
	}

}

?>
