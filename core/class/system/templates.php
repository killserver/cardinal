<?php
/*
*
* Version Engine: 1.25.3
* Version File: 3
*
* 3.0
* add fix foreach block. before - if given string in foreaching data - viewing error in page
* 3.0.5
* add fix lost password in templates and fix old call config
* 3.1
* fix userlevel data
* 3.2
* fix error in minify for position in page
*
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die;
}

final class templates {

	public static $blocks = array();
	private static $foreach = array("count" => 0, "all" => array());
	private static $module = array("head" => array(), "body" => array(), "blocks" => array(), "menu" => array());
	private static $editor = array();
	private static $header;
	private static $tmp = "";
	private static $skins = "";
	public static $gzip = true;
	public static $gzip_activ = false;
	public static $time = 0;

	public function __construct() {
		if(!modules::get_config('gzip_output')) {
			self::$gzip = modules::get_config('gzip_output');
		}
		self::$skins = modules::get_config('skins', 'skins');
		$test_shab = modules::get_config('skins', 'test_shab');
		if(!empty($test_shab) && in_array(HTTP::getip(), modules::get_config('ip_test_shab'))) {
			self::$skins = $test_shab;
		}
		if(defined("MOBILE") && MOBILE && modules::get_config('skins', 'mobile')) {
			self::$skins = modules::get_config('skins', 'mobile');
		}
	}

	public function __call($name,array $params) {
		$new = __METHOD__;
		return self::$new($name, $params);
	}

	public static function __callStatic($name,array $params) {
		$new = __METHOD__;
		return self::$new($name, $params);
	}

	public static function set_skins($skin) {
		self::$skins = $skin;
	}

	public static function get_skins() {
		return self::$skins;
	}

	private static function time() {
		return microtime();
	}

	public static function assign_vars($array, $block = null, $view = null) {
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

	public static function assign_var($name, $value, $block = null) {
		if(empty($block)) {
			self::$blocks[$name] = $value;
		} else {
			self::$blocks[$block][$name] = $value;
		}
	}

	public static function set_menu($name, $html = null, $block = null) {
		if(empty($block)) {
			self::$module['menu'][$name] = $html;
		} else {
			self::$module['menu'][$name][$block] = array("name" => $block, "value" => $html);
		}
	}

	public static function select_menu($name, $block) {
		if(isset(self::$module['menu'][$name][$block])) {
			return self::$module['menu'][$name][$block]['value'];
		} else {
			return false;
		}
	}

	public static function add_modules($data, $where) {
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

	private static function foreachs($array) {
		if(!isset(self::$blocks[$array[1]])) {
			return;
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
			$dd = preg_replace_callback("#\\[if (.*?)\\]([\s\S]*?)\\[else\\]([\s\S]*?)\\[/if\\]#i", ("templates::is"), $dd);
			$dd = preg_replace_callback("#\\[if (.*?)\\]([\s\S]*?)\\[/if\\]#i", ("templates::is"), $dd);
			$dd = preg_replace_callback('#\[foreachif (.*?)\]([\s\S]*?)\[else \\1\]([\s\S]*?)\[/foreachif \\1\]#i', ("templates::is"), $dd);
			$dd = preg_replace_callback('#\[foreachif (.*?)\]([\s\S]*?)\[/foreachif \\1\]#i', ("templates::is"), $dd);
			$dd = preg_replace_callback("#\\[foreachif (.*?)\\]([\s\S]*?)\\[else\\]([\s\S]*?)\\[/foreachif\\]#i", ("templates::is"), $dd);
			$dd = preg_replace_callback("#\\[foreachif (.*?)\\]([\s\S]*?)\\[/foreachif\\]#i", ("templates::is"), $dd);
			$num++;
			$rnum--;
			$tt .= str_replace('\n', "\n", $dd);
		}
	return $tt;
	}

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
	
	private static function systems($array) {
		$return = $array[0];
		switch($array[1]) {
			case "rand":
				$return = mt_rand(1, mt_getrandmax());
			break;
			case "time":
				$return = time();
			break;
			default:
			$return = $array[0];
			break;
		}
		return $return;
	}

	private static function config($array) {
		if(isset($array[2])) {
			$isset = modules::get_config($array[1], $array[2]);
		} else {
			$isset = modules::get_config($array[1]);
		}
		if(!empty($isset)) {
			return $isset;
		} else {
			return $array[0];
		}
	}

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

	private static function sprintf($text, $arr=array()) {
		for($i=0;$i<sizeof($arr); $i++) {
			$text = str_replace("%s[".($i+1)."]", $arr[$i], $text);
		}
		return $text;
	}

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

	private static function define($array) {
		if(defined($array[1])) {
			return constant($array[1]);
		} else {
			return $array[0];
		}
	}

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

	private static function ecomp($tmp) {
		$tmp = preg_replace_callback("#\{include templates=['\"](.*?)['\"]\}#", ("templates::include_tpl"), $tmp);
		$tmp = preg_replace_callback("#\{include module=['\"](.*?)['\"]\}#", ("templates::include_module"), $tmp);
		$tmp = preg_replace("~\{\#is_last\[(\"|)(.*?)(\"|)\]\}~", "\\1", $tmp);
		$tmp = preg_replace_callback("#\\[(not-group)=(.+?)\\](.+?)\\[/not-group\\]#is", ("templates::group"), $tmp);
		$tmp = preg_replace_callback("#\\[(group)=(.+?)\\](.+?)\\[/group\\]#is", ("templates::group"), $tmp);
		$tmp = preg_replace_callback("#\{L_sprintf\(([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\],(.*?)\)\}#", ("templates::slangf"), $tmp);
		$tmp = preg_replace_callback("#\{L_sprintf\(([a-zA-Z0-9\-_]+),(.*?)\)\}#", ("templates::slangf"), $tmp);
		$tmp = preg_replace_callback("#\{L_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#", ("templates::lang"), $tmp);
		$tmp = preg_replace_callback("#\{L_([a-zA-Z0-9\-_]+)\}#", ("templates::lang"), $tmp);
		$tmp = preg_replace_callback("#\{C_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#", ("templates::config"), $tmp);
		$tmp = preg_replace_callback("#\{C_([a-zA-Z0-9\-_]+)\}#", ("templates::config"), $tmp);
		$tmp = preg_replace_callback("#\{U_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#", ("templates::user"), $tmp);
		$tmp = preg_replace_callback("#\{U_([a-zA-Z0-9\-_]+)\}#", ("templates::user"), $tmp);
		$tmp = preg_replace_callback("#\{D_([a-zA-Z0-9\-_]+)\}#", ("templates::define"), $tmp);
		$tmp = str_replace("{reg_link}", config::Select('link', 'reg'), $tmp);
		$tmp = str_replace("{login_link}", config::Select('link', 'login'), $tmp);
		$tmp = str_replace("{logout-link}", config::Select('link', 'logout'), $tmp);
		$tmp = str_replace("{lost_link}", config::Select('link', 'lost'), $tmp);
		$tmp = str_replace("{login}", modules::get_user('username'), $tmp);
		$tmp = str_replace("{addnews-link}", config::Select('link', 'add'), $tmp);
		$tmp = str_replace("{lostpassword-link}", config::Select('link', 'recover'), $tmp);
		$tmp = preg_replace_callback("#\{UL_(.*?)\[(.*?)\]\}#", ("templates::level"), $tmp);
		
		$tmp = preg_replace_callback('#\[page=(.*?)\]([^[]*)\[/page\]#i', ("templates::nowpage"), $tmp);
		$tmp = preg_replace_callback('#\[not-page=(.*?)\]([^[]*)\[/not-page\]#i', ("templates::npage"), $tmp);
		
		$tmp = preg_replace_callback('#\[if (.+?)\](.*?)\[else \\1\](.*?)\[/if \\1\]#i', ("templates::is"), $tmp);
		$tmp = preg_replace_callback('~\[if (.+?)\]([^[]*)\[/if \\1\]~iU', ("templates::is"), $tmp);
		
		$tmp = preg_replace_callback("#\\[if (.+?)\\](.*?)\\[else\\](.*?)\\[/if\\]#i", ("templates::is"), $tmp);
		$tmp = preg_replace_callback('~\[if (.+?)\]([^[]*)\[/if\]~iU', ("templates::is"), $tmp);
		$tmp = preg_replace_callback("#\{S_data=['\"](.+?)['\"],['\"](.*?)['\"]\}#", ("templates::sys_date"), $tmp);
		$tmp = preg_replace_callback("#\{S_([a-zA-Z0-9\-_]+)\}#", ("templates::systems"), $tmp);
		return $tmp;
	}

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

	public static function load_template($file, $no_skin = false) {
		$time = self::time();
		if($no_skin) {
			if(file_exists(ROOT_PATH."skins/".$file.".tpl")) {
				self::$tmp = file_get_contents(ROOT_PATH."skins/".$file.".tpl");
			}
		} else {
			if(file_exists(ROOT_PATH."skins/".self::$skins."/".$file.".tpl")) {
				self::$tmp = file_get_contents(ROOT_PATH."skins/".self::$skins."/".$file.".tpl");
			}
		}
		self::$time += self::time()-$time;
	}

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

	private static function level($array) {
		$ret = "false";
		$data = "true";
		$array[2] = str_replace("\"", "", $array[2]);
		return userlevel::check($array[1], $array[2]);
	}

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
		if(!isset($type)) return;
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

	private static function foreach_set($array) {
		self::$foreach = array_merge(self::$foreach, array("count" => $array[1]));
	}

	private static function countforeach($array) {
		if(isset(self::$foreach["all"][$array[1]])) {
			$data = self::$foreach["all"][$array[1]]+1;
		} else {
			$data = 1;
		}
		return $data;
	}

	private static function replace_tmp($array) {
		if(isset(self::$blocks[$array[1]][$array[2]])) {
			return self::$blocks[$array[1]][$array[2]];
		} else {
			return $array[0];
		}
	}

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
			$dir = ROOT_PATH."skins/".self::$skins."/".$file[0];
		} elseif(isset($file[1]) && !empty($file[1])) {
			$dir = ROOT_PATH."skins/".$file[1]."/".$file[0];
		} else {
			$dir = ROOT_PATH."skins/".$file[0];
		}
		if(file_Exists($dir)) {
			$files = file_get_contents($dir);
			return self::comp_datas($files, $file[0]);
		} else {
			return $array[0]."1";
		}
	}

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
		if(!file_exists(ROOT_PATH."core/modules/".$array[1]) && !file_exists(ROOT_PATH."core/modules/autoload/".$array[1])) {
			return $ret;
		}
		if(!class_exists($class)) {
			return $ret;
		}
		$mod = new $class();
		return $mod->start();
	}

	public function change_blocks($name, $value = null, $func="add") {
		if($func=="add" || $func=="edit") {
			self::$blocks[$name] = $value;
		} elseif($func=="delete") {
			unset(self::$blocks[$name]);
		}
	}

	private static function count_blocks($array) {
		return sizeof(self::$blocks[$array[2]]);
	}
	
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

	private static function comp_datas($tpl, $file="null", $test = false) {
		$tpl = preg_replace_callback("#\{include templates=['\"](.*?)['\"]\}#", ("templates::include_tpl"), $tpl);
		$tpl = preg_replace_callback("#\{include module=['\"](.*?)['\"]\}#", ("templates::include_module"), $tpl);
		$tpl = preg_replace(array('~\{\#\#(.+?)}~', '~\{\#(.+?)}~', '~\{\_(.+?)}~'), "{\\1}", $tpl);
		$tpl = preg_replace_callback("~\{is_last\[(\"|)(.+?)(\"|)\]\}~", ("templates::count_blocks"), $tpl);
		$tpl = preg_replace_callback("#\{C_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#", ("templates::config"), $tpl);
		$tpl = preg_replace_callback("#\{C_([a-zA-Z0-9\-_]+)\}#", ("templates::config"), $tpl);
		$tpl = preg_replace_callback("#\{U_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#", ("templates::user"), $tpl);
		$tpl = preg_replace_callback("#\{U_([a-zA-Z0-9\-_]+)\}#", ("templates::user"), $tpl);
		$tpl = preg_replace_callback("#\{D_([a-zA-Z0-9\-_]+)\}#", ("templates::define"), $tpl);
		$tpl = preg_replace_callback("#\{UL_(.*?)\[(.*?)\]\}#", ("templates::level"), $tpl);
		$tpl = preg_replace_callback('#\[page=(.*?)\](.*)\[/page\]#i', ("templates::nowpage"), $tpl);
		$tpl = preg_replace_callback('#\[not-page=(.*?)\](.*)\[/not-page\]#i', ("templates::npage"), $tpl);
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
				$tpls = preg_replace_callback("/{(.+?)\[(.+?)\]}/", ("templates::replace_tmp"), $tpls);
			}
		}
if(!$test) {
		$tpl = preg_replace_callback("#\{foreach\}([0-9]+)\{/foreach\}#i", ("templates::foreach_set"), $tpl);
		$tpl = preg_replace_callback("#\\[foreach block=(.+?)\\](.+?)\\[/foreach $1\\]#is", ("templates::foreachs"), $tpls);
		$tpl = preg_replace_callback("#\\[foreach block=(.+?)\\](.+?)\\[/foreach\\]#is", ("templates::foreachs"), $tpls);
		$tpl = preg_replace("#\{foreach\}([0-9]+)\{/foreach\}#i", "", $tpl);
		$tpl = preg_replace_callback("#\{count\[(.*?)\]\}#is", ("templates::countforeach"), $tpl);
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
				$tpl = preg_replace_callback("/{(.+?)\[(.+?)\]}/", ("templates::replace_tmp"), $tpl);
			}
		}
		$tpl = preg_replace_callback("#\\[ajax\\]([\s\S]*?)\\[else\\]([\s\S]*?)\\[/ajax\\]#i", ("templates::ajax"), $tpl);
		$tpl = preg_replace_callback("#\\[ajax\\]([\s\S]*?)\\[/ajax\\]#i", ("templates::ajax"), $tpl);
		$tpl = preg_replace_callback("#\\[ajax_click\\]([\s\S]*?)\\[/ajax_click\\]#i", ("templates::ajax_click"), $tpl);
		$tpl = preg_replace_callback("#\\[!ajax_click\\]([\s\S]*?)\\[/!ajax_click\\]#i", ("templates::ajax_click"), $tpl);
		$tpl = preg_replace_callback("#\\[!ajax\\]([\s\S]*?)\\[/!ajax\\]#i", ("templates::ajax"), $tpl);

		$tpl = preg_replace_callback('#\[if (.*?)\]([\s\S]*?)\[else \\1\]([\s\S]*?)\[/if \\1\]#i', ("templates::is"), $tpl);
		$tpl = preg_replace_callback('#\[if (.*?)\]([\s\S]*?)\[/if \\1\]#i', ("templates::is"), $tpl);
		
		$tpl = preg_replace_callback("#\\[if (.*?)\\]([\s\S]*?)\\[else\\]([\s\S]*?)\\[/if\\]#i", ("templates::is"), $tpl);
		$tpl = preg_replace_callback("#\\[if (.*?)\\]([\s\S]*?)\\[/if\\]#i", ("templates::is"), $tpl);
/*
ToDo: WTF?!
if($test) {
		$tpl = preg_replace_callback("#\{foreach\}([0-9]+)\{/foreach\}#i", ("templates::foreach_set"), $tpl);
		$tpl = preg_replace_callback("#\\[foreach block=(.+?)\\](.+?)\\[/foreach $1\\]#is", ("templates::foreachs"), $tpl);
		$tpl = preg_replace_callback("#\\[foreach block=(.+?)\\](.+?)\\[/foreach\\]#is", ("templates::foreachs"), $tpl);
		$tpl = preg_replace("#\{foreach\}([0-9]+)\{/foreach\}#i", "", $tpl);
		$tpl = preg_replace_callback("#\{count\[(.*?)\]\}#is", ("templates::countforeach"), $tpl);
}
*/
		$tpl = preg_replace_callback("#\{S_data=['\"](.+?)['\"],['\"](.*?)['\"]\}#", ("templates::sys_date"), $tpl);
		$tpl = preg_replace_callback("#\{S_([a-zA-Z0-9\-_]+)\}#", ("templates::systems"), $tpl);
		$tpl = preg_replace_callback("#\\[module_(.+?)\\](.+?)\\[/module_(.+?)\\]#i", ("templates::is"), $tpl);
		$tpl = preg_replace_callback("#\\[module_(.+?)\\](.+?)\\[/module_(.+?)\\]#i", ("templates::is"), $tpl);
		if(strpos($tpl, "[clear]") !== false) {
			foreach($array_use as $name => $val) {
				unset(self::$blocks[$name]);
			}
			unset($array_use);
			$tpl = str_replace("[clear]", "", $tpl);
		}
		return $tpl;
	}

	public static function load_templates($file, $charset = null, $dir = "null") {
		$time = self::time();
		if($dir == "null") {
			$tpl = file_get_contents(ROOT_PATH."skins/".self::$skins."/".$file.".tpl");
		} elseif($dir=="admin") {
			$tpl = file_get_contents(ROOT_PATH."admincp.php/temp/".$file.".tpl");
		} elseif(empty($dir)) {
			$tpl = file_get_contents(ROOT_PATH."skins/".$file.".tpl");
		} else {
			$tpl = file_get_contents(ROOT_PATH."skins/".$dir."/".$file.".tpl");
		}
		if(!empty($charset)) {
			$tpl = iconv($charset, modules::get_config("charset"), $tpl);
		}
		self::$time += self::time()-$time;
		return $tpl;
	}

	public static function complited_assing_vars($file, $dir = "null", $test = false) {
		$time = self::time();
		if($dir == "null") {
			$tpl = file_get_contents(ROOT_PATH."skins/".self::$skins."/".$file.".tpl");
		} elseif($dir=="admin") {
			$tpl = file_get_contents(ROOT_PATH."admincp.php/temp/".$file.".tpl");
		} elseif($dir=="book") {
			$tpl = file_get_contents(ROOT_PATH."book/skins/".$file.".tpl");
		} elseif(empty($dir)) {
			$tpl = file_get_contents(ROOT_PATH."skins/".$file.".tpl");
		} else {
			$tpl = file_get_contents(ROOT_PATH."skins/".$dir."/".$file.".tpl");
		}
		$tpl = self::comp_datas($tpl, $file, $test);
		$tpl = str_replace("{THEME}", "/skins/".self::$skins, $tpl);
		self::$time += self::time()-$time;
		return $tpl;
	}

	public static function complited($tmp, $header = null) {
	global $manifest, $user;
		$time = self::time();
		if(!is_array($header)) {
			self::$header = array("title" => $header);
		} else {
			self::$header = $header;
		}
		$manifest['mod_page'][getenv("REMOTE_ADDR")]['title'] = self::$header['title'];
		modules::manifest_set(array('mod_page', getenv("REMOTE_ADDR"), 'title'), self::$header['title']);
		$tmp = self::comp_datas($tmp);
		self::$tmp = $tmp;
		self::$time += self::time()-$time;
	}

	private static function change_head($header) {
		if(!is_array($header)) {
			self::$header = array("title" => $header);
		} else {
			self::$header = array_merge($header, self::$header);
		}
	}

	public static function lcud($tmp) {
		$tmp = preg_replace_callback("#\{L_sprintf\(([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\],(.*?)\)\}#", ("templates::slangf"), $tmp);
		$tmp = preg_replace_callback("#\{L_sprintf\(([a-zA-Z0-9\-_]+),(.*?)\)\}#", ("templates::slangf"), $tmp);
		$tmp = preg_replace_callback("#\{L_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#", ("templates::lang"), $tmp);
		$tmp = preg_replace_callback("#\{L_([a-zA-Z0-9\-_]+)\}#", ("templates::lang"), $tmp);
		$tmp = preg_replace_callback("#\{C_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#", ("templates::config"), $tmp);
		$tmp = preg_replace_callback("#\{C_([a-zA-Z0-9\-_]+)\}#", ("templates::config"), $tmp);
		$tmp = preg_replace_callback("#\{U_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#", ("templates::user"), $tmp);
		$tmp = preg_replace_callback("#\{U_([a-zA-Z0-9\-_]+)\}#", ("templates::user"), $tmp);
		$tmp = preg_replace_callback("#\{D_([a-zA-Z0-9\-_]+)\}#", ("templates::define"), $tmp);
		$tmp = preg_replace_callback("#\{S_data=['\"](.+?)['\"],['\"](.*?)['\"]\}#", ("templates::sys_date"), $tmp);
		$tmp = preg_replace_callback("#\{S_([a-zA-Z0-9\-_]+)\}#", ("templates::systems"), $tmp);
	return $tmp;
	}

	public static function view($data, $header = null) {
		self::complited($data, $header);
		$h = self::$tmp;
		$h = str_replace("{THEME}", "/skins/".self::$skins, $h);
		return self::ecomp($h);
	}

	public static function templates($tmp, $header = null) { return self::complited($tmp, $header);}

	public static function error($data, $header=null) {
		if(!is_array($header)) {
			self::$header = array("title" => $header);
		} else {
			self::$header = $header;
		}
		if(is_array($header) && !isset($header['title'])) {
			$header['title'] = "";
		}
		if(isset(self::$header['code']) && self::$header['code']=="404") {
			$sapi_name = php_sapi_name();
			if($sapi_name == 'cgi' OR $sapi_name == 'cgi-fcgi') {
				header('Status: 404 Not Found');
			}
			header('HTTP/1.1 404 Not Found');
		}
		self::$blocks = array_merge(self::$blocks, array("error" => $data, "title" => (isset($header['title']) ? $header['title'] : "")));
		self::$tmp = self::complited_assing_vars("info");
		self::display();
		exit();
	}

	public static function set_block($name, $var) {
		if(is_array($var) && sizeof($var)) {
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
	private static function minify($html) {
		if(!modules::get_config('tpl_minifier')) {
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

	public static function display() {
	global $lang;
		$time = self::time();
		if(!file_exists(ROOT_PATH."skins/".self::$skins."/main.tpl")) {
			echo "error templates";
			return;
		}
		if(file_exists(ROOT_PATH."skins/".self::$skins."/lang/tpl.php")) {
			include_once(ROOT_PATH."skins/".self::$skins."/lang/tpl.php");
		}
		$h = file_get_contents(ROOT_PATH."skins/".self::$skins."/main.tpl");
		$l = file_get_contents(ROOT_PATH."skins/".self::$skins."/login.tpl");
		$l = iconv("cp1251", modules::get_config('charset'), $l);
		$h = str_replace("{login}", $l, $h);
		$head = "";
		if(isset(self::$module['head']['before'])) {
			$head .= self::$module['head']['before'];
		}
		$head .= headers(self::$header);
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
		if(isset(self::$header['meta_body'])) {
			$mtt = meta(self::$header['meta_body']);
			$h = str_replace("{meta_tt}", $mtt, $h);
		} else {
			$h = str_replace("{meta_tt}", meta(), $h);
		}
		$h = str_replace("{content}", $body, $h);
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
		}
		$h = str_replace("{THEME}", "/skins/".self::$skins, $h);
		$h = self::ecomp($h);
		$find_preg = $replace_preg = array();
		if(sizeof(self::$editor)) {
			foreach(self::$editor as $key_find => $key_replace) {
				$find_preg[] = $key_find;
				$replace_preg[] = $key_replace;
			}
			$h = preg_replace($find_preg, $replace_preg, $h);
		}
		HTTP::echos(self::minify($h));
		unset($this);
		if(function_exists("memory_get_usage")) {
			echo "<!-- ".round((memory_get_usage()/1024/1024), 2)." -->";
		}
		self::$time += self::time()-$time;
	}
	
	public static function clean() {
		self::$blocks = array();
		self::$foreach = array("count" => 0, "all" => array());
		self::$module = array("head" => array(), "body" => array(), "blocks" => array(), "menu" => array());
		self::$editor = array();
		self::$header = null;
		//self::$tmp = "";
		//self::$skins = "";
	}

	public function __destruct() { 
		unset($this);
	} 

}

?>