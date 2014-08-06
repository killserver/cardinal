<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die;
}

final class templates {

	private $blocks = array();
	private $foreach = array("count" => 0, "all" => array());
	private $module = array("head" => array(), "body" => array(), "blocks" => array(), "menu" => array());
	private $editor = array();
	private $header;
	private $tmp = "";
	private $skins = "";
	public $gzip = true;
	public $time = 0;

	function __construct() {
		if(!modules::get_config('gzip_output')) {
			$this->gzip = modules::get_config('gzip_output');
		}
		$this->skins = modules::get_config('skins', 'skins');
		if(defined("MOBILE") && MOBILE) {
			$this->skins = modules::get_config('skins', 'mobile');
		}
	}

	function set_skins($skin) {
		$this->skins = $skin;
	}

	function get_skins() {
		return $this->skins;
	}

	private function time() {
		return microtime();
	}

	function assign_vars($array, $block = null, $view = null) {
		if(empty($block)) {
			foreach($array as $name => $value) {
				$this->blocks[$name] = $value;
			}
		} elseif(!empty($view)) {
			foreach($array as $name => $value) {
				$this->blocks[$block][$view][$name] = $value;
			}
		} else {
			foreach($array as $name => $value) {
				$this->blocks[$block][$name] = $value;
			}
		}
	}

	function assign_var($name, $value, $block = null) {
		if(empty($block)) {
			$this->blocks[$name] = $value;
		} else {
			$this->blocks[$block][$name] = $value;
		}
	}

	function set_menu($name, $html = null, $block = null) {
		if(empty($block)) {
			$this->module['menu'][$name] = $html;
		} else {
			$this->module['menu'][$name][$block] = array("name" => $block, "value" => $html);
		}
	}

	function select_menu($name, $block) {
		if(isset($this->module['menu'][$name][$block])) {
			return $this->module['menu'][$name][$block]['value'];
		} else {
			return false;
		}
	}

	function add_modules($data, $where) {
		$where = explode("|", $where);
		if($where[0]=="head") {
			if(empty($this->module['head'][$where[1]])) {
				$this->module['head'][$where[1]] = $data;
			} else {
				$this->module['head'][$where[1]] = $this->module['head'][$where[1]].$data;
			}
		} elseif($where[0]=="body") {
			if(empty($this->module['body'][$where[1]])) {
				$this->module['body'][$where[1]] = $data;
			} else {
				$this->module['body'][$where[1]] = $this->module['body'][$where[1]].$data;
			}
		}
	}

	private function foreachs($array) {
		if(!isset($this->blocks[$array[1]])) {
			return;
		}
		$data = $this->blocks[$array[1]];
		$key = array_keys($data);
		$text = ($array[2]);
		$tt = "";
		$num = 1;
		$all = sizeof($data)-1;
		$rnum = $all+1;
		$this->foreach['all'][$array[1]] = $all;
		for($i=0;$i<=$all;$i++) {
			if(isset($this->foreach['count']) && $this->foreach['count']!=0 && $num >= $this->foreach['count']) {
				$num = 1;
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
			$dd = preg_replace_callback("#\\[if (.*?)\\]([\s\S]*?)\\[else\\]([\s\S]*?)\\[/if\\]#i", array(&$this, "is"), $dd);
			$dd = preg_replace_callback("#\\[if (.*?)\\]([\s\S]*?)\\[/if\\]#i", array(&$this, "is"), $dd);
			$dd = preg_replace_callback("#\\[foreachif (.*?)\\]([\s\S]*?)\\[else\\]([\s\S]*?)\\[/foreachif\\]#i", array(&$this, "is"), $dd);
			$dd = preg_replace_callback("#\\[foreachif (.*?)\\]([\s\S]*?)\\[/foreachif\\]#i", array(&$this, "is"), $dd);
			$num++;
			$rnum--;
			$tt .= str_replace('\n', "\n", $dd);
		}
		unset($text, $data, $nams, $vals, $this->foreach['count'], $this->blocks[$array[1]]);
	return $tt;
	}

	private function user($array) {
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

	private function config($array) {
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

	private function lang($array) {
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

	private function sprintf($text, $arr=array()) {
		for($i=0;$i<sizeof($arr); $i++) {
			$text = str_replace("%s[".($i+1)."]", $arr[$i], $text);
		}
		return $text;
	}

	private function slangf($array) {
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
				return $this->sprintf($vLang, $arrays);
			} else {
				return sprintf($vLang, base64_decode($decode));
			}
		} else {
			return "{L_".$array[1]."}";
		}
	}

	private function define($array) {
		if(defined($array[1])) {
			return constant($array[1]);
		} else {
			return $array[0];
		}
	}

	private function ecomp($tmp) {
		$tmp = preg_replace("~\{\#is_last\[(\"|)(.*?)(\"|)\]\}~", "\\1", $tmp);
		$tmp = preg_replace_callback("#\\[(not-group)=(.+?)\\](.+?)\\[/not-group\\]#is", array(&$this, "group"), $tmp);
		$tmp = preg_replace_callback("#\\[(group)=(.+?)\\](.+?)\\[/group\\]#is", array(&$this, "group"), $tmp);
		$tmp = preg_replace_callback("#\{L_sprintf\(([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\],(.*?)\)\}#", array(&$this, "slangf"), $tmp);
		$tmp = preg_replace_callback("#\{L_sprintf\(([a-zA-Z0-9\-_]+),(.*?)\)\}#", array(&$this, "slangf"), $tmp);
		$tmp = preg_replace_callback("#\{L_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#", array(&$this, "lang"), $tmp);
		$tmp = preg_replace_callback("#\{L_([a-zA-Z0-9\-_]+)\}#", array(&$this, "lang"), $tmp);
		$tmp = preg_replace_callback("#\{C_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#", array(&$this, "config"), $tmp);
		$tmp = preg_replace_callback("#\{C_([a-zA-Z0-9\-_]+)\}#", array(&$this, "config"), $tmp);
		$tmp = preg_replace_callback("#\{U_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#", array(&$this, "user"), $tmp);
		$tmp = preg_replace_callback("#\{U_([a-zA-Z0-9\-_]+)\}#", array(&$this, "user"), $tmp);
		$tmp = preg_replace_callback("#\{D_([a-zA-Z0-9\-_]+)\}#", array(&$this, "define"), $tmp);
		$tmp = str_replace("{reg_link}", modules::get_config('link', 'reg'), $tmp);
		$tmp = str_replace("{login_link}", modules::get_config('link', 'login'), $tmp);
		$tmp = str_replace("{logout-link}", modules::get_config('link', 'logout'), $tmp);
		$tmp = str_replace("{lost_link}", modules::get_config('link', 'lost'), $tmp);
		$tmp = str_replace("{login}", modules::get_user('username'), $tmp);
		$tmp = str_replace("{addnews-link}", modules::get_config('link', 'add'), $tmp);
		$tmp = preg_replace_callback("#\{UL_(.*?)\[(.*?)\]\}#", array(&$this, "level"), $tmp);
		$tmp = preg_replace_callback("#\\[if (.+?)\\](.*?)\\[else\\](.*?)\\[/if\\]#i", array(&$this, "is"), $tmp);
		$tmp = preg_replace_callback('~\[if (.+?)\]([^[]*)\[/if\]~iU', array(&$this, "is"), $tmp);
		return $tmp;
	}

	private function group($array) {
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

	function load_template($file, $no_skin = false) {
		$time = $this->time();
		if($no_skin) {
			if(file_exists(ROOT_PATH."skins/".$file.".tpl")) {
				$this->tmp = file_get_contents(ROOT_PATH."skins/".$file.".tpl");
			}
		} else {
			if(file_exists(ROOT_PATH."skins/".$this->skins."/".$file.".tpl")) {
				$this->tmp = file_get_contents(ROOT_PATH."skins/".$this->skins."/".$file.".tpl");
			}
		}
		$this->time += $this->time()-$time;
	}

	function ajax($array) {
		if(strpos($array[0], "!ajax") !== false) {
			if(getenv('HTTP_X_REQUESTED_WITH') != 'XMLHttpRequest' || isset($_GET['jajax'])) {
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

	private function level($array, $elseif=false) {
		$else=false;
		$good = true;
		$ret = (isset($array[3]) ? (!$elseif ? $array[3] : false) : "false");
		if(isset($array[1]) && strpos($array[1], "||") !== false) {
			$data = explode("||", $array[1]);
			$array[1] = $data[0];
			$else = $this->is(array($array, $data[1], $array[2], $ret), true);
		}
		if(isset($array[1]) && strpos($array[1], "&&") !== false) {
			$data = explode("&&", $array[1]);
			$array[1] = $data[0];
			$good = $this->is(array($array, $data[1], $array[2], $ret), true);
		}
		if(!$elseif) {
			$data = "true";
		} else {
			$data = true;
		}
		$array[1] = str_replace("\"", "", $array[1]);
		if(strpos($array[1], "[") !== false) {
			preg_match("#(.+)\[(.+)\]#i", $array[1], $rep);
			if((userlevel::check($rep[1], $rep[2]) || $else) && $good) {
				return $data;
			} else {
				return $ret;
			}
		} else {
			if((userlevel::check($array[1]) || $else) && $good) {
				return $data;
			} else {
				return $ret;
			}
		}
	}

	private function is($array, $elseif=false) {
		$else=false;
		$good = true;
		$ret = (isset($array[3]) ? (!$elseif ? $array[3] : false) : "");
		if(isset($array[1]) && strpos($array[1], "||") !== false) {
			$data = explode("||", $array[1]);
			$array[1] = $data[0];
			$else = $this->is(array($array, $data[1], $array[2], $ret), true);
		}
		if(isset($array[1]) && strpos($array[1], "&&") !== false) {
			$data = explode("&&", $array[1]);
			$array[1] = $data[0];
			$good = $this->is(array($array, $data[1], $array[2], $ret), true);
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
			if((!empty($e) || isset($this->blocks[$e]) || $else) && $good) {
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

	private function foreach_set($array) {
		$this->foreach = array_merge($this->foreach, array("count" => $array[1]));
	}

	private function countforeach($array) {
		$data = $this->foreach["all"][$array[1]]+1;
		unset($this->foreach["all"][$array[1]]);
		return $data;
	}

	private function replace_tmp($array) {
		if(isset($this->blocks[$array[1]][$array[2]])) {
			return $this->blocks[$array[1]][$array[2]];
		} else {
			return $array[0];
		}
	}

	private function include_tpl($array) {
		if(strpos($array[1], ",") !== false) {
			$file = explode(",", $array[1]);
		} else {
			$file = array($array[1]);
		}
		if(strpos($file[0], ".tpl") === false) {
			$file[0] = $file[0].".tpl";
		}
		if(!isset($file[1])) {
			$dir = ROOT_PATH."skins/".$this->skins."/".$file[0];
		} elseif(isset($file[1]) && !empty($file[1])) {
			$dir = ROOT_PATH."skins/".$file[1]."/".$file[0];
		} else {
			$dir = ROOT_PATH."skins/".$file[0];
		}
		if(file_Exists($dir)) {
			$files = file_get_contents($dir);
			return $this->comp_datas($files, $file[0]);
		} else {
			return $array[0];
		}
	}

	private function include_module($array) {
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
		if(!file_exists(ROOT_PATH."core/modules/".$array[1])) {
			return $ret;
		}
		//include(ROOT_PATH."core/modules/".$array[1]);
		if(!class_exists($class)) {
			return $ret;
		}
		$mod = new $class();
		return $mod->start();
	}

	public function change_blocks($name, $value = null, $func="add") {
		if($func=="add" || $func=="edit") {
			$this->blocks[$name] = $value;
		} elseif($func=="delete") {
			unset($this->blocks[$name]);
		}
	}

	private function count_blocks($array) {
		return sizeof($this->blocks[$array[2]]);
	}

	private function comp_datas($tpl, $file="null", $test = false) {
		$tpl = preg_replace_callback("#\\{include templates=['\"](.*?)['\"]\\}#i", array(&$this, "include_tpl"), $tpl);
		$tpl = preg_replace_callback("#\\{include module=['\"](.*?)['\"]\\}#i", array(&$this, "include_module"), $tpl);
		$tpl = preg_replace(array('~\{\#\#(.+?)}~', '~\{\#(.+?)}~', '~\{\_(.+?)}~'), "{\\1}", $tpl);
		$tpl = preg_replace_callback("~\{is_last\[(\"|)(.+?)(\"|)\]\}~", array(&$this, "count_blocks"), $tpl);
		$tpl = preg_replace_callback("#\{C_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#", array(&$this, "config"), $tpl);
		$tpl = preg_replace_callback("#\{C_([a-zA-Z0-9\-_]+)\}#", array(&$this, "config"), $tpl);
		$tpl = preg_replace_callback("#\{U_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#", array(&$this, "user"), $tpl);
		$tpl = preg_replace_callback("#\{U_([a-zA-Z0-9\-_]+)\}#", array(&$this, "user"), $tpl);
		$tpl = preg_replace_callback("#\{D_([a-zA-Z0-9\-_]+)\}#", array(&$this, "define"), $tpl);
		$tpl = preg_replace_callback("#\{UL_(.*?)\[(.*?)\]\}#", array(&$this, "level"), $tpl);

		$this->blocks = array_merge($this->blocks, modules::manifest_get(array("temp", "block")));
		$tpls = $tpl;
		foreach($this->blocks as $name => $val) {
			if(is_array($name)) {
				continue;
			}
			if(!is_array($val) && strpos($tpl, "{".$name."}") !== false) {
				$tpls = str_replace("{".$name."}", $val, $tpls);
			} else {
				$tpls = preg_replace_callback("/{(.+?)\[(.+?)\]}/", array(&$this, "replace_tmp"), $tpls);
			}
		}
if(!$test) {
		$tpl = preg_replace_callback("#\{foreach\}([0-9]+)\{/foreach\}#i", array(&$this, "foreach_set"), $tpl);
		$tpl = preg_replace_callback("#\\[foreach block=(.+?)\\](.+?)\\[/foreach\\]#is", array(&$this, "foreachs"), $tpls);
		$tpl = preg_replace("#\{foreach\}([0-9]+)\{/foreach\}#i", "", $tpl);
		$tpl = preg_replace_callback("#\{count\[(.*?)\]\}#is", array(&$this, "countforeach"), $tpl);
}
		$array_use = array();
		foreach($this->blocks as $name => $val) {
			if(is_array($name)) {
				continue;
			}
			if(!is_array($val) && strpos($tpl, "{".$name."}") !== false) {
				$tpl = str_replace("{".$name."}", $val, $tpl);
				$array_use[$name] = $val;
			} else {
				$tpl = preg_replace_callback("/{(.+?)\[(.+?)\]}/", array(&$this, "replace_tmp"), $tpl);
			}
		}
		$tpl = preg_replace_callback("#\\[ajax\\]([\s\S]*?)\\[else\\]([\s\S]*?)\\[/ajax\\]#i", array(&$this, "ajax"), $tpl);
		$tpl = preg_replace_callback("#\\[ajax\\]([\s\S]*?)\\[/ajax\\]#i", array(&$this, "ajax"), $tpl);
		$tpl = preg_replace_callback("#\\[!ajax\\]([\s\S]*?)\\[/!ajax\\]#i", array(&$this, "ajax"), $tpl);

		$tpl = preg_replace_callback("#\\[if (.*?)\\]([\s\S]*?)\\[else\\]([\s\S]*?)\\[/if\\]#i", array(&$this, "is"), $tpl);
		$tpl = preg_replace_callback("#\\[if (.*?)\\]([\s\S]*?)\\[/if\\]#i", array(&$this, "is"), $tpl);
if($test) {
		$tpl = preg_replace_callback("#\{foreach\}([0-9]+)\{/foreach\}#i", array(&$this, "foreach_set"), $tpl);
		$tpl = preg_replace_callback("#\\[foreach block=(.+?)\\](.+?)\\[/foreach\\]#is", array(&$this, "foreachs"), $tpl);
		$tpl = preg_replace("#\{foreach\}([0-9]+)\{/foreach\}#i", "", $tpl);
		$tpl = preg_replace_callback("#\{count\[(.*?)\]\}#is", array(&$this, "countforeach"), $tpl);
}
		$tpl = preg_replace_callback("#\\[module_(.+?)\\](.+?)\\[/module_(.+?)\\]#i", array(&$this, "is"), $tpl);
		$tpl = preg_replace_callback("#\\[module_(.+?)\\](.+?)\\[/module_(.+?)\\]#i", array(&$this, "is"), $tpl);
		if(strpos($tpl, "[clear]") !== false) {
			foreach($array_use as $name => $val) {
				unset($this->blocks[$name]);
			}
			unset($array_use);
			$tpl = str_replace("[clear]", "", $tpl);
		}
		return $tpl;
	}

	function complited_assing_vars($file, $dir = "null", $test = false) {
		$time = $this->time();
		if($dir == "null") {
			$tpl = file_get_contents(ROOT_PATH."skins/".$this->skins."/".$file.".tpl");
		} elseif($dir=="admin") {
			$tpl = file_get_contents(ROOT_PATH."admincp.php/temp/".$file.".tpl");
		} elseif(empty($dir)) {
			$tpl = file_get_contents(ROOT_PATH."skins/".$file.".tpl");
		} else {
			$tpl = file_get_contents(ROOT_PATH."skins/".$dir."/".$file.".tpl");
		}
		$tpl = $this->comp_datas($tpl, $file, $test);
		$tpl = str_replace("{THEME}", "/skins/".$this->skins, $tpl);
		$this->time += $this->time()-$time;
		return $tpl;
	}

	function complited($tmp, $header = null) {
	global $manifest, $user;
		$time = $this->time();
		if(!is_array($header)) {
			$this->header = array("title" => $header);
		} else {
			$this->header = $header;
		}
		$manifest['mod_page'][getenv("REMOTE_ADDR")]['title'] = $this->header['title'];
		//if(empty($tmp)) {
		//	$tmp = $this->tmp;
			unset($this->tmp);
		//}
		$tmp = $this->comp_datas($tmp);
		$this->tmp = $tmp;
		$this->time += $this->time()-$time;
	}

	private function change_head($header) {
		if(!is_array($header)) {
			$this->header = array("title" => $header);
		} else {
			$this->header = array_merge($header, $this->header);
		}
	}

	function lcud($tmp) {
		$tmp = preg_replace_callback("#\{L_sprintf\(([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\],(.*?)\)\}#", array(&$this, "slangf"), $tmp);
		$tmp = preg_replace_callback("#\{L_sprintf\(([a-zA-Z0-9\-_]+),(.*?)\)\}#", array(&$this, "slangf"), $tmp);
		$tmp = preg_replace_callback("#\{L_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#", array(&$this, "lang"), $tmp);
		$tmp = preg_replace_callback("#\{L_([a-zA-Z0-9\-_]+)\}#", array(&$this, "lang"), $tmp);
		$tmp = preg_replace_callback("#\{C_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#", array(&$this, "config"), $tmp);
		$tmp = preg_replace_callback("#\{C_([a-zA-Z0-9\-_]+)\}#", array(&$this, "config"), $tmp);
		$tmp = preg_replace_callback("#\{U_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#", array(&$this, "user"), $tmp);
		$tmp = preg_replace_callback("#\{U_([a-zA-Z0-9\-_]+)\}#", array(&$this, "user"), $tmp);
		$tmp = preg_replace_callback("#\{D_([a-zA-Z0-9\-_]+)\}#", array(&$this, "define"), $tmp);
	return $tmp;
	}

	function view($data, $header = null) {
		$this->complited($data, $header);
		$h = $this->tmp;
		return $this->ecomp($h);
	}

	function templates($tmp, $header = null) { return $this->complited($tmp, $header);}

	function error($data, $header=null) {
		if(!is_array($header)) {
			$this->header = array("title" => $header);
		} else {
			$this->header = $header;
		}
		if(!isset($header['title'])) {
			$header['title'] = "";
		}
		if(isset($this->header['code']) && $this->header['code']=="404") {
			$sapi_name = php_sapi_name();
			if($sapi_name == 'cgi' OR $sapi_name == 'cgi-fcgi') {
				header('Status: 404 Not Found');
			}
			header('HTTP/1.1 404 Not Found');
		}
		$this->blocks = array_merge($this->blocks, array("error" => $data, "title" => $header['title']));
		$this->tmp = $this->complited_assing_vars("info");
		$this->display();
		exit();
	}

	function set_block($name, $var) {
		if(is_array($var) && sizeof($var)) {
			foreach($var as $key => $vars) {
				$this->set_block($key, $vars);
			}
		} else {
			$this->editor[$name] = $var;
		}
	}

	// Gorlum's minifier BOF
	/**
	* Minifies template w/i PHP code by removing extra spaces
	* @access private
	*/
	private function minify($html) {
		if(!modules::get_config('tpl_minifier')) {
			return $html;
		}

		// TODO: Match <code> and <pre> too - in separate arrays
		preg_match_all('/(<script[^>]*?>.*?<\/script>)/si', $html, $pre);
		$html = preg_replace('/(<script[^>]*?>.*?<\/script>)/si', '#pre#', $html);
		//$html = preg_replace('#<!-[^\[].+->#', '', $html);
		//$html = preg_replace('/[\r\n\t]+/', ' ', $html);
		$html = preg_replace('/>[\s]*</', '><', $html); // Strip spacechars between tags
		$html = preg_replace('/[\s]+/', ' ', $html); // Replace several spacechars with one space
		if(!empty($pre[0])) {
			foreach($pre[0] as $tag) {
				$tag = preg_replace('/^\ *\/\/[^\<]*?$/m', ' ', $tag); // Strips comments - except those that contains HTML comment inside
				$tag = preg_replace('/[\ \t]{2,}/', ' ', $tag); // Replace several spaces by one
				$tag = preg_replace('/\s{2,}/', "\r\n", $tag); // Replace several linefeeds by one
				$html = preg_replace('/#pre#/', $tag, $html,1);
			}
		}
	return $html;
	}
	// Gorlum's minifier EOF

	function display() {
	global $lang;
		$time = $this->time();
		if(!file_exists(ROOT_PATH."skins/".$this->skins."/main.tpl")) {
			echo "error templates";
			return;
		}
		include_once(ROOT_PATH."skins/".$this->skins."/lang/tpl.php");
		$h = file_get_contents(ROOT_PATH."skins/".$this->skins."/main.tpl");
		$l = file_get_contents(ROOT_PATH."skins/".$this->skins."/login.tpl");
		$l = iconv("cp1251", modules::get_config('charset'), $l);
		$h = str_replace("{login}", $l, $h);
		$head = "";
		if(isset($this->module['head']['before'])) {
			$head .= $this->module['head']['before'];
		}
		$head .= headers($this->header);
		if(isset($this->module['head']['after'])) {
			$head .= $this->module['head']['after'];
		}
		$h = str_replace("{headers}", $head, $h);

		$body = "";
		if(isset($this->module['body']['before'])) {
			$body .= $this->module['body']['before'];
		}
		$body .= $this->tmp;
		if(isset($this->module['body']['after'])) {
			$body .= $this->module['body']['after'];
		}
		if(isset($this->header['meta_body'])) {
			$mtt = meta($this->header['meta_body']);
			$h = str_replace("{meta_tt}", $mtt, $h);
		} else {
			$h = str_replace("{meta_tt}", meta(), $h);
		}
		$h = str_replace("{content}", $body, $h);
		if((getenv('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest' && isset($_GET['jajax'])) || isset($_GET['jajax'])) {
			unset($h);
			$h = $body;
		}
		$tmp = modules::manifest_get(array("temp", "menu"));
		if(sizeof($tmp)>0) {
			$this->module['menu'] = array_merge($this->module['menu'], $tmp);
		}
		unset($tmp);
		if(sizeof($this->module['menu'])>0) {
			foreach($this->module['menu'] as $name => $val) {
				if(!is_array($name) && !is_array($val)) {
					$h = str_replace("{".$name."}", $this->comp_datas($val), $h);
				} elseif(!is_array($name) && is_array($val)) {
					$values = array_values($val);
					$value = "";
					for($i=0;$i<sizeof($values);$i++) {
						$value .= $values[$i]['value'];
					}
					$h = str_replace("{".$name."}", $this->comp_datas($value), $h);
				}
			}
		}
		unset($this->module, $this->blocks);
		$h = str_replace("{THEME}", "/skins/".$this->skins, $h);
		$h = $this->ecomp($h);
		$find_preg = $replace_preg = array();
		if(sizeof($this->editor)) {
			foreach($this->editor as $key_find => $key_replace) {
				$find_preg[] = $key_find;
				$replace_preg[] = $key_replace;
			}
			$h = preg_replace($find_preg, $replace_preg, $h);
		}
		unset($this->editor);
		echo $this->minify($h);
		unset($this);
		if(function_exists("memory_get_usage")) {
			echo "<!-- ".round((memory_get_usage()/1024/1024), 2)." -->";
		}
		$this->time += $this->time()-$time;
	}

	public function __destruct() { 
		unset($this);
	} 

}

?>