<?php
/*
 *
 * @version 3.2
 * @copyright 2014-2016 KilleR for Cardinal Engine
 *
 * Version Engine: 3.2
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
final class templates {

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
	 * @var
     */
	private static $header;
	/**
	 * @var string
     */
	private static $tmp = "";
	/**
	 * @var bool|string
     */
	private static $skins = "";
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
	public static $gzip_activ = false;
	/**
	 * @var int
     */
	public static $time = 0;

	/**
	 * templates constructor.
	 * @access private
     */
	public function __construct() {
		if(!modules::get_config('gzip_output')) {
			self::$gzip = modules::get_config('gzip_output');
		}
		self::$skins = modules::get_config('skins', 'skins');
		$test_shab = modules::get_config('skins', 'test_shab');
		unset($test_shab);
		if(!empty($test_shab) && in_array(HTTP::getip(), modules::get_config('ip_test_shab'))) {
			self::$skins = $test_shab;
		}
		if(defined("MOBILE") && MOBILE && modules::get_config('skins', 'mobile')) {
			self::$skins = modules::get_config('skins', 'mobile');
		}
	}

	/**
	 * Safe template from clone
	 * @access private
	 * @return bool Ban from clone class
	 */
	private function __clone() {
		return false;
	}

	/**
	 * Call function as object method
	 * @access public
	 * @param string $name Name method for static call
	 * @param array $params Params for static call
	 * @return mixed Result work static method
     */
	public function __call($name, array $params) {
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
	public static function __callStatic($name, array $params) {
		$new = __METHOD__;
		return self::$new($name, $params);
	}

	/**
	 * If skin setting - reset default directory skin or return default
	 * @access public
	 * @param string $skin Directory skin name
	 * @return string default or set directory skin
     */
	public static function dir_skins($skin = "") {
		if(!empty($skin)) {
			self::$dir_skins = $skin;
		} else {
			return self::$dir_skins;
		}
	}

	/**
	 * Set skin
	 * @access public
	 * @param string $skin
     */
	public static function set_skins($skin) {
		self::$skins = $skin;
	}

	/**
	 * Get skin
	 * @access public
	 * @return bool|string
     */
	public static function get_skins() {
		return self::$skins;
	}

	/**
	 * Return UNIX-time with microseconds
	 * @access private
	 * @return mixed
     */
	private static function time() {
		return microtime();
	}

	/**
	 * Set array datas for template
	 * @access public
	 * @param array $array Array data for template
	 * @param string $block Block data for cycle
	 * @param string $view Unique id for block data
     */
	public static function assign_vars($array, $block = "", $view = "") {
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

	/**
	 * Set data for template
	 * @access public
	 * @param string $name Name data for template
	 * @param string $value Value data for template
	 * @param string $block Block data for create array
     */
	public static function assign_var($name, $value, $block = "") {
		if(empty($block)) {
			self::$blocks[$name] = $value;
		} else {
			self::$blocks[$block][$name] = $value;
		}
	}

	/**
	 * Set datas for menu
	 * @access public
	 * @param string $name Name menu on template
	 * @param string $html HTML-code for template
	 * @param string $block Block menu
     */
	public static function set_menu($name, $html = "", $block = "") {
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
	public static function select_menu($name, $block) {
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
	public static function add_modules($data, $where) {
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
	private static function callback_array($pattern, $func, $data) {
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
	private static function foreachs($array) {
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
	private static function user($array) {
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
	private static function routeparam($arr) {
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
	private static function route($array) {
		if(isset($array[1]) && isset($array[2])) {
			$route = Route::get($array[1]);
			if(!is_bool($route)) {
				$params = array();
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
	private static function systems($array) {
		$ret = $array[0];
		switch($array[1]) {
			case "rand":
				$ret = mrand();
			break;
			case "time":
				$ret = time();
			break;
			default:
				$ret = $array[0];
			break;
		}
		return $ret;
	}

	/**
	 * Get config data
	 * @access private
	 * @param array $array Params for get config
	 * @return bool Return data in config or original line
     */
	private static function config($array) {
		if(isset($array[2])) {
			$isset = config::Select($array[1], $array[2]);
		} else {
			$isset = config::Select($array[1]);
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
	private static function lang($array) {
		if(isset($array[2])) {
			$isset = modules::get_lang($array[1], $array[2]);
		} else {
			$isset = modules::get_lang($array[1]);
		}
		if(!empty($isset)) {
			return $isset;
		} else {
			return $array[0];
		}
	}

	/**
	 * Line replaced on params
	 * @access private
	 * @param string $text Original line
	 * @param array $arr Array data for replacing
	 * @return string Return replaced string
     */
	private static function sprintf($text, $arr=array()) {
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
	private static function slangf($array) {
		if(isset($array[3])) {
			$decode = $array[3];
			$vLang = modules::get_lang($array[1], $array[2]);
		} else {
			$decode = $array[2];
			$vLang = modules::get_lang($array[1]);
		}
		if(!empty($vLang)) {
			if(strpos(base64_decode($decode), ",") !==false) {
				$arrays = explode(",", base64_decode($decode));
				return self::sprintf($vLang, $arrays);
			} else {
				return sprintf($vLang, base64_decode($decode));
			}
		} else {
			return "{L_".$array[1]."}";
		}
	}

	/**
	 * Get constant
	 * @access private
	 * @param array $array Get constant
	 * @return string Return data in constant or original line
     */
	private static function define($array) {
		if(defined($array[1])) {
			return constant($array[1]);
		} else {
			return $array[0];
		}
	}

	/**
	 * Rebuild DateTime with and without start time
	 * @access private
	 * @param array $data Array data for rebuild view time
	 * @return bool|string Return DateTime to template
     */
	private static function sys_date($data) {
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
	private static function ParseTemp($tpl, $file) {
		$del = false;
		if($file==self::$dir_skins.DS.self::$skins.DS."null") {
			$del = true;
		}
		$file = str_replace(array("/", DS, "-", ".."), array("-", "_", "_", "_"), $file);
		$file = ROOT_PATH."core".DS."cache".DS."tmp".DS.$file.".".md5($file).".php";
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
	private static function RebuildOffPhp($tpl) {
		$tpl = preg_replace("#<!-- FOREACH (.+?) -->#", '[foreach block=\\1]', $tpl);
		$tpl = preg_replace("#<!-- ENDFOREACH (.+?) -->#", '[/foreach]', $tpl);
		$tpl = preg_replace("#<!-- ENDFOREACH -->#", '[/foreach]', $tpl);
		$tpl = preg_replace("#<!-- IF (.+?) -->#", "[if \\1]", $tpl);
		$tpl = preg_replace("#<!-- ELSE -->#", "[else]", $tpl);
		$tpl = preg_replace("#<!-- ELSEIF (.+?) -->#", "[else \\1]", $tpl);
		$tpl = preg_replace("#<!-- ENDIF -->#", "[/if]", $tpl);
		
		$tpl = preg_replace("#\{% L_sprintf\(([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\],(.*?)\) %\}#", '{L_sprintf(\\1[\\2],\\3)}', $tpl);
		$tpl = preg_replace("#\{% L_sprintf\(([a-zA-Z0-9\-_]+),(.*?)\) %\}#", '{L_sprintf(\\1,\\2)}', $tpl);
		$tpl = preg_replace("#\{% L_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\] %\}#", '{L_\\1[\\2]}', $tpl);
		$tpl = preg_replace("#\{% L_([a-zA-Z0-9\-_]+) %\}#", '{L_\\1}', $tpl);
		$tpl = preg_replace("#\{% C_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\] %\}#", '{C_\\1[\\2]}', $tpl);
		$tpl = preg_replace("#\{% C_([a-zA-Z0-9\-_]+) %\}#", '{C_\\1}', $tpl);
		$tpl = preg_replace("#\{% U_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\] %\}#", '{U_\\1[\\2]}', $tpl);
		$tpl = preg_replace("#\{% U_([a-zA-Z0-9\-_]+) %\}#", '{U_\\1}', $tpl);
		$tpl = preg_replace("#\{% D_([a-zA-Z0-9\-_]+) %\}#", '{D_\\1}', $tpl);
		$tpl = preg_replace("#\{% R_\[(.+?)\]\[(.+?)\] %\}#", '{R_[\\1][\\2]}', $tpl);
		$tpl = preg_replace("#\{% RP\[(.+?)\] %\}#", '{RP[\\1]}', $tpl);
		
		$tpl = preg_replace("#\{% ([a-zA-Z0-9\-_]+)\.([a-zA-Z0-9\-_]+) %\}#is", '{\\1.\\2}', $tpl);
		$tpl = preg_replace("#\{% ([a-zA-Z0-9\-_]+) %\}#is", '{\\1}', $tpl);
		
		
		$tpl = preg_replace("#\{\# ([a-zA-Z0-9\-_]+)\.([a-zA-Z0-9\-_]+) \#\}#is", '{\\1.\\2}', $tpl);
		$tpl = preg_replace("#\{\# ([a-zA-Z0-9\-_]+) \#\}#is", '{\\1}', $tpl);
		return $tpl;
	}

	/**
	 * Rebuild template in php-file
	 * @access private
	 * @param string $tpl Original template
	 * @param string $file Execute file template
	 * @return mixed Result rebuild and including file
     */
	private static function ParsePHP($tpl, $file = "") {
		if(!config::Select("ParsePHP")) {
			return self::RebuildOffPhp($tpl);
		}
		if(empty($file) || !file_exists(ROOT_PATH."core".DS."cache".DS."tmp".DS) || !is_dir(ROOT_PATH."core".DS."cache".DS."tmp".DS) || !is_writable(ROOT_PATH."core".DS."cache".DS."tmp".DS)) {
			return $tpl;
		}
		if(strpos($tpl, "<?xml")===false) {
			$safe = array(
				"<?php" => "&lt;?php",
				"<?" => "&lt;?",
				"?>" => "?&gt;",
			);
			$tpl = str_replace(array_keys($safe), array_values($safe), $tpl);
		} else {
			$safe = array(
				"<?xml" => '<?php echo \'<?xml\'; ?>',
			);
			$tpl = str_replace(array_keys($safe), array_values($safe), $tpl);
		}
		$tpl = preg_replace("#<!-- FOREACH (.+?) -->#", '<?php if(isset($data[\'\\1\']) && is_array($data[\'\\1\']) && sizeof($data[\'\\1\'])>0) { foreach($data[\'\\1\'] as $\\1) { ?>', $tpl);
		$tpl = preg_replace("#<!-- ENDFOREACH (.+?) -->#", '<?php } } ?>', $tpl);
		$tpl = preg_replace("#<!-- ENDFOREACH -->#", '<?php } } ?>', $tpl);
		$tpl = preg_replace("#<!-- IF (.+?) -->#", "<?php if(\\1) { ?>", $tpl);
		$tpl = preg_replace("#<!-- ELSE -->#", "<?php } else { ?>", $tpl);
		$tpl = preg_replace("#<!-- ELSEIF (.+?) -->#", "<?php } elseif(\\1) { ?>", $tpl);
		$tpl = preg_replace("#<!-- ENDIF -->#", "<?php } ?>", $tpl);
		
		$tpl = preg_replace("#\{% L_sprintf\(([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\],(.*?)\) %\}#", 'templates::slangf(array(null, \'\\1\', \'\\2\'))', $tpl);
		$tpl = preg_replace("#\{% L_sprintf\(([a-zA-Z0-9\-_]+),(.*?)\) %\}#", 'templates::slangf(array(null, \'\\1\'))', $tpl);
		$tpl = preg_replace("#\{% L_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\] %\}#", 'templates::lang(array(null, \'\\1\', \'\\2\'))', $tpl);
		$tpl = preg_replace("#\{% L_([a-zA-Z0-9\-_]+) %\}#", 'templates::lang(array(null, \'\\1\'))', $tpl);
		$tpl = preg_replace("#\{% C_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\] %\}#", 'config::Select(\'\\1\', \'\\2\')', $tpl);
		$tpl = preg_replace("#\{% C_([a-zA-Z0-9\-_]+) %\}#", 'config::Select(\'\\1\')', $tpl);
		$tpl = preg_replace("#\{% U_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\] %\}#", 'templates::user(array(null, \'\\1\', \'\\2\'))', $tpl);
		$tpl = preg_replace("#\{% U_([a-zA-Z0-9\-_]+) %\}#", 'templates::user(array(null, \'\\1\'))', $tpl);
		$tpl = preg_replace("#\{% D_([a-zA-Z0-9\-_]+) %\}#", 'templates::define(array(null, \'\\1\'))', $tpl);
		$tpl = preg_replace("#\{% R_\[(.+?)\]\[(.+?)\] %\}#", 'templates::route(array(null, \'\\1\', \'\\2\'))', $tpl);
		$tpl = preg_replace("#\{% RP\[(.+?)\] %\}#", 'templates::routeparam(array(null, \'\\1\'))', $tpl);
		
		$tpl = preg_replace("#\{\@ ([a-zA-Z0-9\-_]+)\.([a-zA-Z0-9\-_]+) \@\}#is", '(isset($\\1[\'\\2\']) ? $\\1[\'\\2\'] : \'\')', $tpl);
		$tpl = preg_replace("#\{% ([a-zA-Z0-9\-_]+)\.([a-zA-Z0-9\-_]+) %\}#is", '$data[\'\\1\'][\'\\2\']', $tpl);
		$tpl = preg_replace("#\{% ([a-zA-Z0-9\-_]+) %\}#is", '$data[\'\\1\']', $tpl);
		
		$tpl = preg_replace("#\{L_sprintf\(([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\],(.*?)\)\}#", '<?php echo templates::slangf(array(null, \'\\1\', \'\\2\')); ?>', $tpl);
		$tpl = preg_replace("#\{L_sprintf\(([a-zA-Z0-9\-_]+),(.*?)\)\}#", '<?php echo templates::slangf(array(null, \'\\1\')); ?>', $tpl);
		$tpl = preg_replace("#\{L_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#", '<?php echo templates::lang(array(null, \'\\1\', \'\\2\')); ?>', $tpl);
		$tpl = preg_replace("#\{L_([a-zA-Z0-9\-_]+)\}#", '<?php echo templates::lang(array(null, \'\\1\')); ?>', $tpl);
		$tpl = preg_replace("#\{C_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#", '<?php echo config::Select(\'\\1\', \'\\2\'); ?>', $tpl);
		$tpl = preg_replace("#\{C_([a-zA-Z0-9\-_]+)\}#", '<?php echo config::Select(\'\\1\'); ?>', $tpl);
		$tpl = preg_replace("#\{U_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#", '<?php echo templates::user(array(null, \'\\1\', \'\\2\')); ?>', $tpl);
		$tpl = preg_replace("#\{U_([a-zA-Z0-9\-_]+)\}#", '<?php echo templates::user(array(null, \'\\1\')); ?>', $tpl);
		$tpl = preg_replace("#\{D_([a-zA-Z0-9\-_]+)\}#", '<?php echo templates::define(array(null, \'\\1\')); ?>', $tpl);
		$tpl = preg_replace("#\{R_\[(.+?)\]\[(.+?)\]\}#", '<?php echo templates::route(array(null, \'\\1\', \'\\2\')); ?>', $tpl);
		$tpl = preg_replace("#\{\@ R_\[(.+?)\]\[(.+?)\] \@\}#", '<?php echo templates::route(array(null, \'\\1\', \\2)); ?>', $tpl);
		$tpl = preg_replace("#\{RP\[(.+?)\]\}#", '<?php echo templates::routeparam(array(null, \'\\1\')); ?>', $tpl);
		
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
	private static function ecomp($tmp, $file = "") {
		if(strpos($tmp, "///***")!==false&&strpos($tmp, "***///")!==false) {
			$tmp = nsubstr($tmp, 0, nstrpos($tmp, "///***")).nsubstr($tmp, nstrpos($tmp, "***///")+6, nstrlen($tmp));
		}
		$tmp = self::ParsePHP($tmp, $file);
		$tmp = self::callback_array("#\{include templates=['\"](.*?)['\"]\}#", ("templates::include_tpl"), $tmp);
		$tmp = self::callback_array("#\{include module=['\"](.*?)['\"]\}#", ("templates::include_module"), $tmp);
		$tmp = preg_replace("~\{\#is_last\[(\"|)(.*?)(\"|)\]\}~", "\\1", $tmp);
		$tmp = self::callback_array("#\\[(not-group)=(.+?)\\](.+?)\\[/not-group\\]#is", ("templates::group"), $tmp);
		$tmp = self::callback_array("#\\[(group)=(.+?)\\](.+?)\\[/group\\]#is", ("templates::group"), $tmp);
		$tmp = self::callback_array("#\{L_sprintf\(([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\],(.*?)\)\}#", ("templates::slangf"), $tmp);
		$tmp = self::callback_array("#\{L_sprintf\(([a-zA-Z0-9\-_]+),(.*?)\)\}#", ("templates::slangf"), $tmp);
		$tmp = self::callback_array("#\{L_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#", ("templates::lang"), $tmp);
		$tmp = self::callback_array("#\{L_([a-zA-Z0-9\-_]+)\}#", ("templates::lang"), $tmp);
		$tmp = self::callback_array("#\{C_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#", ("templates::config"), $tmp);
		$tmp = self::callback_array("#\{C_([a-zA-Z0-9\-_]+)\}#", ("templates::config"), $tmp);
		$tmp = self::callback_array("#\{U_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#", ("templates::user"), $tmp);
		$tmp = self::callback_array("#\{U_([a-zA-Z0-9\-_]+)\}#", ("templates::user"), $tmp);
		$tmp = self::callback_array("#\{D_([a-zA-Z0-9\-_]+)\}#", ("templates::define"), $tmp);
		$tmp = self::callback_array("#\{R_\[(.+?)\]\[(.+?)\]\}#", ("templates::route"), $tmp);
		$tmp = self::callback_array("#\{RP\[(.+?)\]\}#", ("templates::routeparam"), $tmp);
		$tmp = str_replace("{reg_link}", config::Select('link', 'reg'), $tmp);
		$tmp = str_replace("{login_link}", config::Select('link', 'login'), $tmp);
		$tmp = str_replace("{logout-link}", config::Select('link', 'logout'), $tmp);
		$tmp = str_replace("{lost_link}", config::Select('link', 'lost'), $tmp);
		$tmp = str_replace("{login}", modules::get_user('username'), $tmp);
		$tmp = str_replace("{addnews-link}", config::Select('link', 'add'), $tmp);
		$tmp = str_replace("{lostpassword-link}", config::Select('link', 'recover'), $tmp);
		$tmp = self::callback_array("#\{UL_(.*?)\[(.*?)\]\}#", ("templates::level"), $tmp);
		
		$tmp = self::callback_array('#\[page=(.*?)\]([^[]*)\[/page\]#i', ("templates::nowpage"), $tmp);
		$tmp = self::callback_array('#\[not-page=(.*?)\]([^[]*)\[/not-page\]#i', ("templates::npage"), $tmp);
		
		$tmp = self::callback_array('#\[if (.+?)\](.*?)\[else \\1\](.*?)\[/if \\1\]#i', ("templates::is"), $tmp);
		$tmp = self::callback_array('~\[if (.+?)\]([^[]*)\[/if \\1\]~iU', ("templates::is"), $tmp);
		
		$tmp = self::callback_array("#\\[if (.+?)\\](.*?)\\[else\\](.*?)\\[/if\\]#i", ("templates::is"), $tmp);
		$tmp = self::callback_array('~\[if (.+?)\]([^[]*)\[/if\]~iU', ("templates::is"), $tmp);
		$tmp = self::callback_array("#\{S_data=['\"](.+?)['\"],['\"](.*?)['\"]\}#", ("templates::sys_date"), $tmp);
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
	private static function group($array) {
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
	public static function ajax($array) {
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
	private static function ajax_click($array) {
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
	private static function level($array) {
		$ret = "false";
		$data = "true";
		$array[2] = str_replace("\"", "", $array[2]);
		return userlevel::check($array[1], $array[2]);
	}

	/**
	 * IF
	 * @access private
	 * @param array $array Array data
	 * @param bool|false $elseif Check elseif or not
	 * @return array|bool|string Return result conditions
     */
	private static function is($array, $elseif=false) {
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
	}

	/**
	 * Reset count element in array
	 * @access private
	 * @param array $array Count elements in array
     */
	private static function foreach_set($array) {
		self::$foreach = array_merge(self::$foreach, array("count" => $array[1]));
	}

	/**
	 * Return count elements in array
	 * @access private
	 * @param array $array Needed array for count elements in he'm
	 * @return int
     */
	private static function countforeach($array) {
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
	private static function replace_tmp($array) {
		if(isset(self::$blocks[$array[1]][$array[2]])) {
			return self::$blocks[$array[1]][$array[2]];
		} else {
			return $array[0];
		}
	}

	/**
	 * Include other template in main template
	 * @access private
	 * @param array $array Array data for including
	 * @return array|mixed|NUll|string Return part template for insert in main template
     */
	private static function include_tpl($array) {
		if(strpos($array[1], ",") !== false) {
			$file = explode(",", $array[1]);
		} else {
			$file = array($array[1]);
		}
		if(strpos($file[0], ".tpl") === false) {
			$file[0] = $file[0].".tpl";
		}
		if(!isset($file[1])) {
			$dir = ROOT_PATH."".self::$dir_skins.DS.self::$skins.DS.$file[0];
		} elseif(isset($file[1]) && !empty($file[1])) {
			$dir = ROOT_PATH."".self::$dir_skins.DS.$file[1].DS.$file[0];
		} else {
			$dir = ROOT_PATH."".self::$dir_skins.DS.$file[0];
		}
		if(file_Exists($dir)) {
			$files = file_get_contents($dir);
			return self::comp_datas($files, $file[0]);
		} else {
			return $array[0];
		}
	}

	/**
	 * Include module
	 * @access private
	 * @param array $array Array data for initialize module-file
	 * @return mixed Result work module or original line
     */
	private static function include_module($array) {
		if(strpos($array[1], ".php") === false) {
			$array[1] = $array[1].".php";
		}
		if(strpos($array[1], ",") !== false) {
			$exp = explode(",", $array[1]);
			$ret = $exp[1];
			$array[1] = $exp[0];
		} else {
			$ret = $array[0];
		}
		$class = str_replace(array(".class", ".php"), "", $array[1]);
		if(!file_exists(ROOT_PATH."core".DS."modules".DS.$array[1]) && !file_exists(ROOT_PATH."core".DS."modules".DS."autoload".DS.$array[1])) {
			return $ret;
		}
		if(!class_exists($class)) {
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
	public function change_blocks($name, $value = "", $func = "add") {
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
	private static function count_blocks($array) {
		return sizeof(self::$blocks[$array[2]]);
	}

	/**
	 * "Not In Page". Check user without this page
	 * @access private
	 * @param array $array Array data
	 * @return mixed Return part template if user without this page
     */
	private static function npage($array) {
	global $manifest;
		if(strpos($array[1], "|")!==false) {
			$search = explode("|", $array[1]);
		} else {
			$search = array($array[1]);
		}
		if(!in_array($manifest['mod_page'][HTTP::getip()]['page'], $search)) {
			return $array[2];
		}
	}

	/**
	 * "In Page". Check user on this page
	 * @access private
	 * @param array $array Array data
	 * @return mixed Return part template if user on this page
     */
	private static function nowpage($array) {
	global $manifest;
		if(strpos($array[1], "|")!==false) {
			$search = explode("|", $array[1]);
		} else {
			$search = array($array[1]);
		}
		if(in_array($manifest['mod_page'][HTTP::getip()]['page'], $search)) {
			return $array[2];
		}
	}

	/**
	 * @access private
	 * @param $tpl
	 * @param string $file
	 * @param bool|false $test
	 * @return array|mixed|NUll
     */
	private static function comp_datas($tpl, $file="null", $test = false) {
		$tpl = self::ParsePHP($tpl, self::$dir_skins.DS.self::$skins.DS.$file);
		$tpl = self::callback_array("#\{include templates=['\"](.*?)['\"]\}#", ("templates::include_tpl"), $tpl);
		$tpl = self::callback_array("#\{include module=['\"](.*?)['\"]\}#", ("templates::include_module"), $tpl);
		$tpl = preg_replace(array('~\{\#\#(.+?)}~', '~\{\#(.+?)}~', '~\{\_(.+?)}~'), "{\\1}", $tpl);
		$tpl = self::callback_array("~\{is_last\[(\"|)(.+?)(\"|)\]\}~", ("templates::count_blocks"), $tpl);
		$tpl = self::callback_array("#\{C_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#", ("templates::config"), $tpl);
		$tpl = self::callback_array("#\{C_([a-zA-Z0-9\-_]+)\}#", ("templates::config"), $tpl);
		$tpl = self::callback_array("#\{U_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#", ("templates::user"), $tpl);
		$tpl = self::callback_array("#\{U_([a-zA-Z0-9\-_]+)\}#", ("templates::user"), $tpl);
		$tpl = self::callback_array("#\{D_([a-zA-Z0-9\-_]+)\}#", ("templates::define"), $tpl);
		$tpl = self::callback_array("#\{UL_(.*?)\[(.*?)\]\}#", ("templates::level"), $tpl);
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
		
		$tpl = self::callback_array('#\[for ([0-9]+) to ([0-9]+)(| step=([0-9]+))\](.+?)\[/for\]#is', function($data) {
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
		}, $tpl);
		$tpl = self::callback_array("#\\[ajax\\]([\s\S]*?)\\[else\\]([\s\S]*?)\\[/ajax\\]#i", ("templates::ajax"), $tpl);
		$tpl = self::callback_array("#\\[ajax\\]([\s\S]*?)\\[/ajax\\]#i", ("templates::ajax"), $tpl);
		$tpl = self::callback_array("#\\[ajax_click\\]([\s\S]*?)\\[/ajax_click\\]#i", ("templates::ajax_click"), $tpl);
		$tpl = self::callback_array("#\\[!ajax_click\\]([\s\S]*?)\\[/!ajax_click\\]#i", ("templates::ajax_click"), $tpl);
		$tpl = self::callback_array("#\\[!ajax\\]([\s\S]*?)\\[/!ajax\\]#i", ("templates::ajax"), $tpl);

		$tpl = self::callback_array('#\[if (.*?)\]([\s\S]*?)\[else \\1\]([\s\S]*?)\[/if \\1\]#i', ("templates::is"), $tpl);
		$tpl = self::callback_array('#\[if (.*?)\]([\s\S]*?)\[/if \\1\]#i', ("templates::is"), $tpl);
		
		$tpl = self::callback_array("#\\[if (.*?)\\]([\s\S]*?)\\[else\\]([\s\S]*?)\\[/if\\]#i", ("templates::is"), $tpl);
		$tpl = self::callback_array("#\\[if (.*?)\\]([\s\S]*?)\\[/if\\]#i", ("templates::is"), $tpl);

		$tpl = self::callback_array("#\{S_data=['\"](.+?)['\"],['\"](.*?)['\"]\}#", ("templates::sys_date"), $tpl);
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

	/**
	 * @access public
	 * @param $file
	 * @param bool|false $no_skin
     */
	public static function load_template($file, $no_skin = false) {
		$time = self::time();
		if($no_skin) {
			if(file_exists(ROOT_PATH."".self::$dir_skins.DS.$file.".tpl")) {
				self::$tmp = file_get_contents(ROOT_PATH."".self::$dir_skins.DS.$file.".tpl");
			}
		} else {
			if(file_exists(ROOT_PATH."".self::$dir_skins.DS.self::$skins.DS.$file.".tpl")) {
				self::$tmp = file_get_contents(ROOT_PATH."".self::$dir_skins.DS.self::$skins.DS.$file.".tpl");
			}
		}
		self::$time += self::time()-$time;
	}

	/**
	 * @access public
	 * @param $file
	 * @param null $charset
	 * @param string $dir
	 * @return string
     */
	public static function load_templates($file, $charset = null, $dir = "null") {
		$time = self::time();
		if($dir == "null") {
			$tpl = file_get_contents(ROOT_PATH."".self::$dir_skins.DS.self::$skins.DS.$file.".tpl");
		} elseif($dir=="admin") {
			$tpl = file_get_contents(ROOT_PATH."admincp.php".DS."temp".DS.$file.".tpl");
		} elseif(empty($dir)) {
			$tpl = file_get_contents(ROOT_PATH."".self::$dir_skins.DS.$file.".tpl");
		} else {
			$tpl = file_get_contents(ROOT_PATH."".self::$dir_skins.DS.$dir.DS.$file.".tpl");
		}
		if(!empty($charset)) {
			$tpl = iconv($charset, modules::get_config("charset"), $tpl);
		}
		self::$time += self::time()-$time;
		return $tpl;
	}

	/**
	 * @access public
	 * @param $file
	 * @param string $dir
	 * @param bool|false $test
	 * @return array|mixed|NUll|string
     */
	public static function complited_assing_vars($file, $dir = "null", $test = false) {
		$time = self::time();
		if($dir == "null") {
			try {
				$tpl = file_get_contents(ROOT_PATH."".self::$dir_skins.DS.self::$skins.DS.$file.".tpl");
			} catch(Exception $ex) {
				echo "File \"".ROOT_PATH."".self::$dir_skins.DS.self::$skins.DS.$file.".tpl\" is not exists";
				die();
			}
		} elseif(empty($dir)) {
			try {
				$tpl = file_get_contents(ROOT_PATH."".self::$dir_skins.DS.$file.".tpl");
			} catch(Exception $ex) {
				echo "File \"".ROOT_PATH."".self::$dir_skins.DS.$file.".tpl\" is not exists";
				die();
			}
		} else {
			try {
				$tpl = file_get_contents(ROOT_PATH."".self::$dir_skins.DS.$dir.DS.$file.".tpl");
			} catch(Exception $ex) {
				echo "File \"".ROOT_PATH."".self::$dir_skins.DS.$dir.DS.$file.".tpl\" is not exists";
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
	 * @access public
	 * @param $tmp
	 * @param null $header
     */
	public static function complited($tmp, $header = null) {
	global $manifest, $user;
		$time = self::time();
		if(!is_array($header)) {
			self::$header = array(
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
		} else {
			self::$header = $header;
		}
		$manifest['mod_page'][getenv("REMOTE_ADDR")]['title'] = self::$header['title'];
		modules::manifest_set(array('mod_page', getenv("REMOTE_ADDR"), 'title'), self::$header['title']);
		$tmp = self::comp_datas($tmp);
		self::$tmp = $tmp;
		self::$time += self::time()-$time;
	}

	/**
	 * @access private
	 * @param $header
     */
	private static function change_head($header) {
		if(!is_array($header)) {
			self::$header = array("title" => $header);
		} else {
			self::$header = array_merge($header, self::$header);
		}
	}

	/**
	 * @access public
	 * @param $tmp
	 * @return array|mixed|NUll
     */
	public static function lcud($tmp) {
		$tmp = self::callback_array("#\{L_sprintf\(([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\],(.*?)\)\}#", ("templates::slangf"), $tmp);
		$tmp = self::callback_array("#\{L_sprintf\(([a-zA-Z0-9\-_]+),(.*?)\)\}#", ("templates::slangf"), $tmp);
		$tmp = self::callback_array("#\{L_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#", ("templates::lang"), $tmp);
		$tmp = self::callback_array("#\{L_([a-zA-Z0-9\-_]+)\}#", ("templates::lang"), $tmp);
		$tmp = self::callback_array("#\{C_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#", ("templates::config"), $tmp);
		$tmp = self::callback_array("#\{C_([a-zA-Z0-9\-_]+)\}#", ("templates::config"), $tmp);
		$tmp = self::callback_array("#\{U_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#", ("templates::user"), $tmp);
		$tmp = self::callback_array("#\{U_([a-zA-Z0-9\-_]+)\}#", ("templates::user"), $tmp);
		$tmp = self::callback_array("#\{D_([a-zA-Z0-9\-_]+)\}#", ("templates::define"), $tmp);
		$tmp = self::callback_array("#\{S_data=['\"](.+?)['\"],['\"](.*?)['\"]\}#", ("templates::sys_date"), $tmp);
		$tmp = self::callback_array("#\{S_([a-zA-Z0-9\-_]+)\}#", ("templates::systems"), $tmp);
	return $tmp;
	}

	/**
	 * @access public
	 * @param $data
	 * @param string $header
	 * @return array|mixed|NUll|string
     */
	public static function view($data, $header = "") {
		self::complited($data, $header);
		$h = self::$tmp;
		self::$tmp = "";
		$h = str_replace("{THEME}", config::Select("default_http_local").self::$dir_skins."/".self::$skins, $h);
		return self::ecomp($h);
	}

	/**
	 * @access public
	 * @param $tmp
	 * @param string $header
     */
	public static function templates($tmp, $header = "") { return self::complited($tmp, $header);}

	/**
	 * @access public
	 * @param $data
	 * @param string $header
     */
	public static function error($data, $header = "") {
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
		self::$tmp = self::complited_assing_vars("info");
		self::display();
		exit();
	}

	/**
	 * @access public
	 * @param $name
	 * @param $var
     */
	public static function set_block($name, $var) {
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
	private static function minify($html, $force = false) {
		if(!$force && !modules::get_config('tpl_minifier')) {
			return $html;
		}
		// TODO: Match <code> and <pre> too - in separate arrays
		preg_match_all('/(<script[^>]*?>.*?<\/script>)/si', $html, $pre);
		preg_match_all('/(<textarea[^>]*?>.*?<\/textarea>)/si', $html, $textarea);
		$i=0;
		while(preg_match('/(<script[^>]*?>.*?<\/script>)/si', $html)) {
			$html = preg_replace('/(<script[^>]*?>.*?<\/script>)/si', '#pre'.$i.'#', $html, 1);
			$i++;
		}
		$i=0;
		while(preg_match('/(<textarea[^>]*?>.*?<\/textarea>)/si', $html)) {
			$html = preg_replace('/(<textarea[^>]*?>.*?<\/textarea>)/si', '#textarea'.$i.'#', $html, 1);
			$i++;
		}
		$html = preg_replace('#<!-[^\[].+?->#', '', $html);//ToDo: WTF?!
		$html = preg_replace('/[\r\n\t]+/', ' ', $html);
		$html = preg_replace('/>[\s]*</', '><', $html); // Strip spacechars between tags
		$html = preg_replace('/[\s]+/', ' ', $html); // Replace several spacechars with one space
		if(!empty($pre[0])) {
			$i=0;
			foreach($pre[0] as $tag) {
				$tag = preg_replace('/^\ *\/\/[^\<]*?$/m', ' ', $tag); // Strips comments - except those that contains HTML comment inside
				$tag = preg_replace('/[\ \t]{2,}/', ' ', $tag); // Replace several spaces by one
				$tag = preg_replace('/\s{2,}/', "\r\n", $tag); // Replace several linefeeds by one
				$html = preg_replace('/#pre'.$i.'#/', $tag, $html, 1);
				unset($pre[0][$i]);
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
	 * @access public
     */
	public static function display() {
	global $lang;
		$time = self::time();
		if(!file_exists(ROOT_PATH."".self::$dir_skins.DS.self::$skins.DS."main.tpl")) {
			echo "error templates";
			return;
		}
		if(file_exists(ROOT_PATH."".self::$dir_skins.DS.self::$skins.DS."lang".DS."tpl.php")) {
			include_once(ROOT_PATH."".self::$dir_skins.DS.self::$skins.DS."lang".DS."tpl.php");
		}
		$h = self::complited_assing_vars("main");
		$l = file_get_contents(ROOT_PATH."".self::$dir_skins.DS.self::$skins.DS."login.tpl");
		$l = iconv("cp1251", modules::get_config('charset'), $l);
		$h = str_replace("{login}", $l, $h);
		$head = "";
		if(isset(self::$module['head']['before'])) {
			$head .= self::$module['head']['before'];
		}
		if(strpos($h, "{create_js}")!==false) {
			$head .= headers(self::$header, false, true);
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
		$h = str_replace("{content}", $body, $h);
		if(isset(self::$header['meta_body'])) {
			$mtt = meta(self::$header['meta_body']);
			$h = str_replace("{meta_tt}", $mtt, $h);
			unset($mtt);
		} else {
			$h = str_replace("{meta_tt}", meta(), $h);
		}
		if(isset($_GET['jajax']) || (getenv('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest' && isset($_GET['jajax']))) {
			unset($h);
			$thead = "<script type=\"text/javascript\" src=\"{C_default_http_host}js/gooan.js\"></script><div id=\"pretitle\">{L_sitename}</div>";
			if(isset(self::$header['title'])) {
				$thead = "<div id=\"pretitle\">".self::$header['title']."</div>";
			}
			$h = $thead.$body;
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
		HTTP::echos(self::minify($h));
		unset($this, $h, $body, $lang);
		self::clean();
		if(function_exists("memory_get_usage")) {
			echo "<!-- ".round((memory_get_usage()/1024/1024), 2)." -->";
		}
		self::$time += self::time()-$time;
	}

	/**
	 * @access public
     */
	public static function clean() {
		self::$blocks = array();
		self::$foreach = array("count" => 0, "all" => array());
		self::$module = array("head" => array(), "body" => array(), "blocks" => array(), "menu" => array());
		self::$editor = array();
		self::$header = null;
		self::$tmp = "";
		self::$skins = "";
	}

	/**
	 * @access public
     */
	public function __destruct() {
		unset($this);
	} 

}

?>