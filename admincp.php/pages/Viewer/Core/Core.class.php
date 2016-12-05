<?php
/*
 *
 * @version 2015-10-07 17:50:38 1.25.6-rc3
 * @copyright 2014-2015 KilleR for Cardinal Engine
 *
 * Version Engine: 1.25.6-rc3
 * Version File: 3
 *
 * 3.1
 * fix admin templates
 * 3.2
 * add admin cookie
 * 3.3
 * add support referer. Good idea for save?
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}
class Core {
	
	private $count_unmoder = 0;
	private $load_adminmodules = false;
	private $title = "{L_adminpanel}";
	private static $modules = array();
	private static $js = array();
	private static $css = array();
	
	public static function __callStatic($call, $args) {
		$new = __METHOD__;
		return $this->$new($name, $params);
	}
	
	public function __call($call, array $args) {
		$new = __METHOD__;
		return $this->$new($call, $args);
	}
	
	protected function ParseLang() {
	global $lang;
		$dir = ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."Lang".DS.lang::get_lg().DS;
		if(is_dir($dir)) {
			if($dh = dir($dir)) {
				while(($file = $dh->read()) !== false) {
					if($file != "index.".ROOT_EX && $file != "index.html" && $file != "." && $file != ".." && strpos($file, ".".ROOT_EX) !== false) {
						require_once($dir.$file);
					}
				}
			$dh->close();
			}
		}
	}
	
	private function vsort(&$array) {
		$arrs = array();
		foreach($array as $key => $val) {
			sort($val);
			$arrs[$key] = $val;
		}
		$array = $arrs;
	}
	
	protected function unix($time) {
		return timespan($time);
	}
	
	protected function unmoder($tick = "") {
		if(!empty($tick)) {
			$this->count_unmoder = $tick;
		} else {
			return $this->count_unmoder;
		}
	}
	
	protected function title($titles = "") {
		if(!empty($titles)) {
			$this->title = $titles;
		} else {
			return $this->title;
		}
	}
	
	private function ParseDirSkins($dir, $parse = 1) {
		$skins = array();
		if(is_dir($dir)) {
			if($dh = dir($dir)) {
				while(($file = $dh->read()) !== false) {
					if(is_dir($dir.$file) && $parse == 1) {
						$arrs = $this->ParseDirSkins($dir.$file, 2);
						if(in_array("main.tpl", $arrs)) {
							$skins[] = $file;
						}
					} else if($parse == 2) {
						$skins[] = $file;
					}
				}
			$dh->close();
			}
		}
		return $skins;
	}
	
	protected function ParseSkins($dir = "", $name = "skins", $sub_name = "") {
		if(empty($dir)) {
			$dir = ROOT_PATH."skins".DS;
		}
		$skins = $this->ParseDirSkins($dir);
		$selected = config::Select("skins", $name);
		for($i=0;$i<sizeof($skins);$i++) {
			templates::assign_vars(array("skin" => $skins[$i], "selected" => ($selected==$skins[$i] ? "1" : "0")), "skin_list".$sub_name, "skin".$i);
		}
	}
	
	protected static function ModuleList($name, $func = "", $type = "before") {
		if($type == "before") {
			$type = "before";
		} else if($type == "after") {
			$type = "after";
		} else {
			return false;
		}
		if(is_array($name)) {
			$modulesn = array_values($name);
			$modulesv = array_values($name);
			for($k=0;$k<sizeof($modulesv);$k++) {
				if(is_callable($modulesv[$k])) {
					self::$modules[$type][$modulesn[$k]] = $modulesv[$k];
				}
			}
			return true;
		} else {
			if(is_callable($func)) {
				self::$modules[$type][$name] = $func;
				return true;
			} else {
				return self::$modules;
			}
		}
	}
	
	protected static function InsertList($name, $js = "", $type = "js") {
		if($type=="js") {
			if(is_array($name)) {
				$jssn = array_values($name);
				$jssv = array_values($name);
				for($o=0;$o<sizeof($jssv);$o++) {
					self::$js[$jssn[$o]] = $jssv[$o];
				}
				return true;
			} else if(is_string($name)) {
				self::$js[$name] = $js;
			} else {
				return self::$js;
			}
		} else if($type=="css") {
			if(is_array($name)) {
				$cssn = array_keys($name);
				$cssv = array_values($name);
				for($o=0;$o<sizeof($cssn);$o++) {
					self::$css[$cssn[$o]] = $cssv[$o];
				}
				return true;
			} else if(is_string($name)) {
				self::$css[$name] = $js;
			} else {
				return self::$css;
			}
		}
	}
	
	private function CheckLoadPlugins($file) {
		if(is_bool($this->load_adminmodules)) {
			if(defined("WITHOUT_DB")) {
				if(file_exists(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."Viewer".DS."Core".DS."Plugins".DS."loader.".ROOT_EX)) {
					$adminCore = array();
					include(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."Viewer".DS."Core".DS."Plugins".DS."loader.".ROOT_EX);
					$this->load_adminmodules = $adminCore;
				} else if(file_exists(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."Viewer".DS."Core".DS."Plugins".DS."loader.default.".ROOT_EX)) {
					$adminCore = array();
					include(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."Viewer".DS."Core".DS."Plugins".DS."loader.default.".ROOT_EX);
					$this->load_adminmodules = $adminCore;
				}
			} elseif(!cache::Exists("load_adminmodules")) {
				$delete = ADMINCP_DIRECTORY.DS."pages".DS."Viewer".DS."Core".DS."Plugins".DS;
				db::doquery("SELECT `file` FROM `modules` WHERE `activ` = \"yes\" AND `type` = \"admincp\"", true);
				$this->load_adminmodules = array();
				while($row = db::fetch_assoc()) {
					$this->load_adminmodules[str_replace($delete, "", $row['file'])] = true;
				}
				cache::Set("load_adminmodules", $this->load_adminmodules);
			} elseif(cache::Exists("load_adminmodules")) {
				$this->load_adminmodules = cache::Get("load_adminmodules");
			}
		}
		if(isset($this->load_adminmodules[$file])) {
			return true;
		} else {
			return false;
		}
	}
	
	private function ReadPlugins() {
		$dir = ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."Viewer".DS."Core".DS."Plugins".DS;
		if(file_exists($dir) && is_dir($dir)) {
			$files = array();
			$lengthEx = (-(strlen(ROOT_EX)+1));
			if($dh = dir($dir)) {
				$i=1;
				while(($file = $dh->read()) !== false) {
					if(substr($file, 0, 4) == "Core" && substr($file, $lengthEx) == ".".ROOT_EX && $file != "." && $file != ".." && $this->CheckLoadPlugins($file)) {
						$class = str_replace(substr($file, $lengthEx), "", $file);
						include_once($dir.$file);
						if(class_exists($class) && is_subclass_of($class, "Core")) {
							new $class();
						}
					}
				}
			$dh->close();
			}
		}
	}
	
	private function ReLoadLvl($arr) {
		$level = -1;
		for($y=0;$y<sizeof($arr);$y++) {
			for($t=0;$t<sizeof($arr[$y]);$t++) {
				if($level==-1 && isset($arr[$y]) && isset($arr[$y][$t]) && isset($arr[$y][$t]['access'])) {
					$level = $arr[$y][$t]['access'];
				} else {
					$arr[$y][$t]['access'] = $level;
				}
			}
		}
		return $arr;
	}
	
	protected function Prints($echo, $print = false, $force = false) {
	global $lang, $user, $in_page;
		if(!userlevel::get("admin") || !isset($_COOKIE[COOK_ADMIN_USER]) || !isset($_COOKIE[COOK_ADMIN_PASS])) {
			$ref = urlencode(str_replace(ROOT_PATH, "", cut(getenv("REQUEST_URI"), "/".ADMINCP_DIRECTORY."/")));
			location("{C_default_http_host}".ADMINCP_DIRECTORY."/?pages=Login".(!empty($ref) ? "&ref=".$ref : ""));
			return;
		}
		$this->ParseLang();
		if(!$print) {
			$echo = (templates::complited_assing_vars($echo, null));
		}
		if(isset($_POST['jajax'])) {
			HTTP::echos(templates::view($echo));
			return;
		}
		$dir = ROOT_PATH."core".DS."media".DS."smiles".DS;
		if(is_dir($dir)) {
			$files = array();
			if($dh = dir($dir)) {
				$i=1;
				while(($file = $dh->read()) !== false) {
					if(strpos($file, ".gif") !== false && $file != "." && $file != "..") {
						$sm = strtr($file, array(".gif" => ""));
						templates::assign_vars(array(
							"smile" => $sm,
						), "smiles", "smile_".$i);
						$i++;
					}
				}
			$dh->close();
			}
		}
		templates::assign_var("count_unmoder", $this->unmoder());
		templates::assign_var("title_admin", $this->title());
		$links = array();
		if($dh = dir(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."menu".DS)) {
			$i=1;
			while(($file = $dh->read()) !== false) {
				if($file != "index.".ROOT_EX && $file != "index.html" && $file != "." && $file != "..") {
					include_once(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."menu".DS.$file);
				}
			}
			$dh->close();
		}
		$this->vsort($links);
		$all=0;
		$level = modules::get_user('level');
		$page_v = getenv("REQUEST_URI");
		$now = str_replace(ADMINCP_DIRECTORY."/?", "", substr($page_v, 1, strlen($page_v)));
		foreach($links as $name => $datas) {
			for($i=0;$i<sizeof($datas);$i++) {
				for($is=0;$is<sizeof($datas[$i]);$is++) {
					if(isset($datas[$i][$is]['access']) && $datas[$i][$is]['access']!=$level) {
						break;
					}
					if(sizeof($datas[$i])==1) {
						$count = 0;
					} else {
						$count = sizeof($datas[$i])-1;
					}
					$is_now = str_replace(array("{C_default_http_host}", ADMINCP_DIRECTORY."/?"), "", $datas[$i][$is]['link']);
					templates::assign_vars(array(
						"value" => $datas[$i][$is]['title'],
						"link" => $datas[$i][$is]['link'],
						"is_now" => (($is_now==$now) ? "1" : "0"),
						"type_st" => ($datas[$i][$is]['type']=="cat" ? "start" : ""),
						"type_end" => ($count==$is&&$datas[$i][$is]['type']=="item" ? "end" : ""),
						"icon" => (isset($datas[$i][$is]['icon']) ? $datas[$i][$is]['icon'] : " "),
					), "menu", "m".$all.$i.$is);
				}
			}
			$all++;
		}
		$this->ReadPlugins();
		if(sizeof(self::$modules)>0 && isset(self::$modules['before'])) {
			foreach(self::$modules['before'] as $name => $func) {
				call_user_func($func, false);
			}
		}
		$echos = templates::view(templates::complited_assing_vars("main", null));
		if(sizeof(self::$modules)>0 && isset(self::$modules['after'])) {
			foreach(self::$modules['after'] as $name => $func) {
				$echos = call_user_func($func, $echos);
			}
		}
		$js_echo = "";
		if(sizeof(self::$js)>0) {
			$js = array_values(self::$js);
			for($o=0;$o<sizeof($js);$o++) {
				$html = new html();
				$js_echo .= $html->open("script")->type("text/javascript")->src($js[$o])->cont("")->close()->get_html();
			}
		}
		$echos = str_replace("{js_list}", $js_echo, $echos);
		$css_echo = "";
		if(sizeof(self::$css)>0) {
			$css = array_values(self::$css);
			for($o=0;$o<sizeof($css);$o++) {
				$html = new html();
				$css_echo .= $html->open("link", 2)->type("text/css")->href($css[$o])->rel("stylesheet")->cont("")->close()->get_html();
			}
		}
		$echos = str_replace("{css_list}", $css_echo, $echos);
		$echoView = templates::view($echo);
		if(empty($echoView) && $force) {
			$echoView = $echo;
		}
		echo str_replace("{main_admin}", $echoView, $echos);
	}
	
}

?>