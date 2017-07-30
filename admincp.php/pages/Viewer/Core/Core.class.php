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
		return $this->$new($call, $args);
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

	protected function GetFullEditor($label, $name, $value, $lang = "ru", array $styles = array(), $id = "", $class = "col-sm-12") {
		if(empty($id)) {
			$id = "id".rand(0, PHP_INT_MAX);
		}
		$ret = '<div class="form-group"><label class="col-sm-12 control-label" for="'.$id.'">'.$label.'</label><div class="'.$class.'"><textarea name="'.$name.'" id="'.$id.'">'.$value.'</textarea></div></div>';
		//<script src="assets/xenon/js/tinymce/tinymce.min.js"></script>
		/*
		$(document).ready(function(){
	tinymce.init({
	  selector: 'textarea',
	  height: 500,
	  language : "$lang",
	  plugins: [
	        "advlist autolink lists link image charmap print preview anchor",
	        "searchreplace visualblocks code fullscreen",
	        "insertdatetime media table contextmenu paste imagetools"
	    ],
	    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
	  // imagetools_cors_hosts: ['www.tinymce.com', 'codepen.io'],
	  content_css: [
	    '/skins/Constroy/css/style.css?1495788912',
		'/skins/Constroy/css/fonts.css?1495788912'
	  ]
	});
});
		 */
	}
	
	private function vsort(&$array) {
		$arrs = array();
		foreach($array as $key => $val) {
			asort($val);
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
				db::doquery("SELECT `file` FROM `".PREFIX_DB."modules` WHERE `activ` LIKE \"yes\" AND `type` LIKE \"admincp\"", true);
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
	global $lang;
		if(!userlevel::get("admin") || !isset($_COOKIE[COOK_ADMIN_USER]) || !isset($_COOKIE[COOK_ADMIN_PASS])) {
			$ref = urlencode(str_replace(ROOT_PATH, "", cut(getenv("REQUEST_URI"), "/".ADMINCP_DIRECTORY."/")));
			location("{C_default_http_host}".ADMINCP_DIRECTORY."/?pages=Login".(!empty($ref) ? "&ref=".$ref : ""));
			return;
		}
		if(Arr::get($_GET, "setLanguage", false) && strpos(HTTP::getServer("HTTP_REFERER"), config::Select("default_http_host"))!==false && strpos(HTTP::getServer("HTTP_REFERER"), ADMINCP_DIRECTORY)!==false) {
			$support = lang::support();
			for($i=0;$i<sizeof($support);$i++) {
				$support[$i] = nsubstr($support[$i], 4, -3);
			}
			if(in_array(Arr::get($_GET, "setLanguage"), $support)) {
				HTTP::set_cookie("langSet", Arr::get($_GET, "setLanguage"));
			}
			location(htmlspecialchars_decode(HTTP::getServer("HTTP_REFERER")));die();
		}
		$this->ParseLang();
		Route::RegParam("lang", "ru");
		$routeLang = HTTP::getServer(ROUTE_GET_URL);
		preg_match("#/([a-zA-Z]+)/#", $routeLang, $arr);
		if(isset($arr[1])) {
			$support = lang::support();
			for($i=0;$i<sizeof($support);$i++) {
				$support[$i] = nsubstr($support[$i], 4, -3);
			}
			if(in_array($arr[1], $support)) {
				lang::set_lang($arr[1]);
				lang::init_lang();
				Route::RegParam("lang", $arr[1]);
			}
		} else if(isset($_COOKIE['langSet'])) {
			lang::set_lang($_COOKIE['langSet']);
			lang::init_lang();
			Route::RegParam("lang", $_COOKIE['langSet']);
		}
		if(!$print) {
			$echo = (templates::complited_assing_vars($echo, null));
		}
		if(isset($_POST['jajax'])) {
			HTTP::echos(templates::view($echo));
			return;
		}
		$dir = ROOT_PATH."core".DS."media".DS."smiles".DS;
		if(is_dir($dir)) {
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
			if(isset($datas['item']) && is_array($datas['item'])) {
				if(sizeof($datas['item'])>1) {
					$type = "cat";
				} else {
					unset($datas['item']);
					$type = "item";
				}
			} else {

				$type = "check";
			}
			$datas = array_values($datas);
			for($i=0;$i<sizeof($datas);$i++) {
				for($is=0;$is<sizeof($datas[$i]);$is++) {
					if(isset($datas[$i][$is]['access']) && !$datas[$i][$is]['access']) {
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
						"type" => $type,
						"type_st" => ($type=="cat"&&$datas[$i][$is]['type']=="cat" ? "start" : ""),
						"type_end" => ($type=="cat"&&$count==$is&&$datas[$i][$is]['type']=="item" ? "end" : ""),
						"icon" => (isset($datas[$i][$is]['icon']) ? $datas[$i][$is]['icon'] : " "),
					), "menu", "m".$all.$i.$is);
				}
			}
			$all++;
		}
		templates::assign_var("nowLangText", "{L_Languages}&nbsp;".nucfirst(lang::get_lg()));
		templates::assign_var("nowLangImg", "http://www.nivea.ua/img/flags/small/flag-".lang::get_lg().".png");
		$support = lang::support();
		for($i=0;$i<sizeof($support);$i++) {
			$cutLang = nsubstr($support[$i], 4, -3);
			$lang = nucfirst($cutLang);
			templates::assign_vars(array("img" => "http://www.nivea.ua/img/flags/small/flag-".$cutLang.".png", "langMenu" => $cutLang, "lang" => "{L_Languages}&nbsp;".$lang), "langListSupport", "lang".($i+1));
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