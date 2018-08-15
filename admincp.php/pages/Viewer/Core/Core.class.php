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
	private $headTitle = "";
	private static $modules = array();
	private static $js = array();
	private static $css = array();
	private static $content = array("before" => "", "after" => "");

	public static function addContent($data) {
		self::$content['before'] .= $data;
	}

	public static function addContentBefore($data) {
		self::$content['before'] .= $data;
	}

	public static function addContentAfter($data) {
		self::$content['after'] .= $data;
	}
	
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
		$dir = ADMIN_LANGS.lang::get_lg().DS;
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
	
	protected function title($titles = "", $andHead = false) {
		if(!empty($titles)) {
			if($andHead) {
				$this->headTitle = $titles;
			}
			$this->title = $titles;
		} else {
			return $this->title;
		}
	}
	
	protected function headTitle($titles = "") {
		if(!empty($titles)) {
			$this->headTitle = $titles;
		} else {
			return $this->headTitle.(!empty($this->headTitle) ? " &rsaquo; " : "")."Admin Panel for {L_sitename}";
		}
	}
	
	protected function ParseDirSkins($dir, $getData = false, $parse = 1) {
		$skins = array();
		$default_headers = array(
			'Name' => 'Name',
			'Description' => 'Description',
			'Image' => 'Image',
			'Changelog' => 'Changelog',
			"Author" => "Author",
			'Version' => 'Version',
			"Screenshots" => "Screenshots",
		);
		if(is_array($getData)) {
			$default_headers = array_merge($default_headers, $getData);
		}
		if(is_dir($dir)) {
			if($dh = dir($dir)) {
				while(($file = $dh->read()) !== false) {
					if($file=="." || $file=="..") {
						continue;
					}
					if(is_dir($dir.$file) && $parse == 1) {
						$arrs = $this->ParseDirSkins($dir.$file.DS, $getData, 2);
						$skins = array_merge($skins, $arrs);
					} else if($parse == 2 && strpos($file, "main.tpl")!==false) {
						if($getData) {
							if(file_exists($dir.DS."main.tpl")) {
								$files = $this->get_file_data($dir.DS."main.tpl", $default_headers);
								if(!isset($files['Name'])) {
									$files['Name'] = basename($dir);
								}
							} else {
								$files = array(
									'Name' => basename($dir),
								);
							}
							if(file_exists($dir.DS."info.".ROOT_EX)) {
								$data = require_once($dir.DS."info.".ROOT_EX);
								$files = array_merge($files, $data);
							}
							$files['dir'] = $dir;
							$files['orName'] = basename($dir);
							$files['file'] = $file;
							$file = $files;
						}
						$skins = array_merge($skins, array($file));
					}
				}
			$dh->close();
			}
		}
		return $skins;
	}

	private function get_file_data($file, $default_headers, $default = false) {
		$fp = fopen($file, 'r');
		$file_data = fread($fp, 8192);
		fclose($fp);
		$file_data = str_replace("\r", "\n", $file_data);
		$ret = array();
		foreach($default_headers as $field => $regex) {
			if(preg_match('/^[ \t\/*#@]*'.preg_quote($regex, '/').':(.*)$/mi', $file_data, $match) && isset($match[1])) {
				$ret[$field] = trim(preg_replace("/\s*(?:\*\/|\?>).*/", '', $match[1]));
			} else if($default!==false) {
				$ret[$field] = '';
			}
		}
		return $ret;
	}
	
	protected function ParseSkins($dir = "", $name = "skins", $sub_name = "") {
		if(empty($dir)) {
			$dir = PATH_SKINS;
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

	private function loadMenu() {
		$links = array();
		$now = "";
		$l = new Headers();
		$l->loadMenuAdmin($links, $now);
		$all = 0;
		foreach($links as $name => $datas) {
			if(isset($datas['item']) && is_array($datas['item'])) {
				$newArr = array();
				for($is=0;$is<sizeof($datas['item']);$is++) {
					if(isset($datas['item'][$is]['access']) && $datas['item'][$is]['access']===true) {
						$newArr[] = $datas['item'][$is];
					}
				}
				$datas['item'] = array_values($newArr);
				if(sizeof($datas['item'])>1) {
					$type = "cat";
				} elseif(sizeof($datas['item'])==1) {
					$datas['item'][0]['icon'] = (isset($datas['cat'][0]['icon']) ? $datas['cat'][0]['icon'] : "");
					$datas['cat'] = $datas['item'];
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
						continue;
					}
					if(sizeof($datas[$i])==1) {
						$count = 0;
					} else {
						$count = sizeof($datas[$i])-1;
					}
					$is_now = str_replace(array("{C_default_http_host}", ADMINCP_DIRECTORY."/?"), "", $datas[$i][$is]['link']);
					templates::assign_vars(array(
						"existSub" => ($type=="cat"&&sizeof($datas)>1 ? "true" : "false"),
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
	}
	
	private function CheckLoadPlugins($file) {
		if(is_bool($this->load_adminmodules)) {
			if(defined("WITHOUT_DB")) {
				if(file_exists(ADMIN_VIEWER."Core".DS."Plugins".DS."loader.".ROOT_EX)) {
					$adminCore = array();
					include(ADMIN_VIEWER."Core".DS."Plugins".DS."loader.".ROOT_EX);
					$this->load_adminmodules = $adminCore;
				} else if(file_exists(ADMIN_VIEWER."Core".DS."Plugins".DS."loader.default.".ROOT_EX)) {
					$adminCore = array();
					include(ADMIN_VIEWER."Core".DS."Plugins".DS."loader.default.".ROOT_EX);
					$this->load_adminmodules = $adminCore;
				}
			} elseif(!cache::Exists("load_adminmodules")) {
				$delete = str_replace(ROOT_PATH, "", ADMIN_VIEWER."Core".DS."Plugins".DS);
				db::doquery("SELECT `file` FROM {{modules}} WHERE `activ` LIKE \"yes\" AND `type` LIKE \"admincp\"", true);
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
		$dir = ADMIN_VIEWER."Core".DS."Plugins".DS;
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

	public static function addInfo($echo, $type = "info", $closed = false, $time = -1) {
		$users = $arr = array();
		$userNow = "";
		if(class_exists("User") && method_exists("User", "All")) {
			$users = User::All();
			$users = array_keys($users);
			$userNow = User::get("username");
		}
		for($i=0;$i<sizeof($users);$i++) {
			$dirToSave = PATH_CACHE_USERDATA;
			$pathToSave = $dirToSave."infoSystem-".$users[$i].".txt";
			if(file_exists($dirToSave) && is_readable($dirToSave) && file_exists($pathToSave) && is_readable($pathToSave)) {
				$file = file_get_contents($pathToSave);
				$file = json_decode($file, true);
				$arr = array_merge($arr, $file);
			}
			if($type!="success" && $type!="warning" && $type!="error" && $type!="info") {
				trigger_error("Error type for info");die();
			}
			if(isset($arr[$echo])) {
				$arr[$echo]['echo'] = $echo;
				$arr[$echo]['type'] = $type;
				$arr[$echo]['time'] = ($time>-1 ? ($time<time() ? time()+$time : $time) : time()+90);
				$arr[$echo]['closed'] = $closed;
			} else {
				$arr = array_merge($arr, array($echo => array("echo" => $echo, "type" => $type, "time" => ($time>-1 ? ($time<time() ? time()+$time : $time) : time()+90), "closed" => $closed, "code" => generate_uuid4())));
			}
			$arrs = json_encode($arr);
			if(file_exists($dirToSave) && !is_writable($dirToSave)) {
				@chmod($dirToSave, 0777);
			}
			if(file_exists($dirToSave) && is_readable($dirToSave) && file_exists($pathToSave) && !is_writable($pathToSave)) {
				@chmod($pathToSave, 0777);
			}
			file_put_contents($pathToSave, $arrs);
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
		$users = array();
		$userNow = "";
		if(class_exists("User") && method_exists("User", "get")) {
			$userNow = User::get("username");
		}
		$dirToSave = PATH_CACHE_USERDATA;
		$pathToSave = $dirToSave."infoSystem-".$userNow.".txt";
		if(isset($_GET['removeCode'])) {
			if(class_exists("User") && method_exists("User", "All")) {
				$users = User::All();
				$users = array_keys($users);
			}
			if(sizeof($users)==0 || !in_array($userNow, $users)) {
				return false;
			}
			if(file_exists($dirToSave) && !is_readable($dirToSave)) {
				@chmod($dirToSave, 0777);
			}
			if(!file_exists($pathToSave)) {
				return false;
			}
			if(!is_readable($pathToSave)) {
				@chmod($pathToSave, 0777);
			}
			$file = file_get_contents($pathToSave);
			$isUnset = false;
			$arrs = json_decode($file, true);
			foreach($arrs as $k => $arr) {
				if($arr['time'] < time()) {
					$isUnset = true;
					unset($arrs[$k]);
					continue;
				}
				if($arr['code'] == $_GET['removeCode']) {
					$isUnset = true;
					$arrs[$k]['hide'] = true;
				}
			}
			if($isUnset) {
				if(sizeof($arrs)>0) {
					$arrs = json_encode($arrs);
					if(file_exists($dirToSave) && !is_writable($dirToSave)) {
						@chmod($dirToSave, 0777);
					}
					if(file_exists($dirToSave) && is_readable($dirToSave) && file_exists($pathToSave) && !is_writable($pathToSave)) {
						@chmod($pathToSave, 0777);
					}
					file_put_contents($pathToSave, $arrs);
				} else {
					if(file_exists($dirToSave) && !is_writable($dirToSave)) {
						@chmod($dirToSave, 0777);
					}
					if(file_exists($dirToSave) && is_readable($dirToSave) && file_exists($pathToSave) && !is_writable($pathToSave)) {
						@chmod($pathToSave, 0777);
					}
					@unlink($pathToSave);
				}
			}
			HTTP::echos("done");
			return false;
		}
		if(Route::param("lang")!="") {
			$langs = Route::param("lang");
		} else {
			$langs = config::Select("lang");
		}
		templates::assign_var("langPanel", $langs);
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
		Route::RegParam("lang", config::Select("lang"));
		$routeLang = HTTP::getServer(ROUTE_GET_URL);
		preg_match("#^/([a-zA-Z]+)/#", $routeLang, $arr);
		if(isset($arr[1])) {
			$support = lang::support(true);
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
			$echo = (templates::completed_assign_vars($echo, null));
		}
		execEvent("admin_print_ready");
		if(isset($_POST['jajax']) || isset($_GET['jajax'])) {
			HTTP::echos(templates::view($echo));
			return;
		}
		templates::assign_var("count_Yui", "false");
		if(file_exists(PATH_CACHE."yui.txt")) {
			$datas = file_get_contents(PATH_CACHE."yui.txt");
			$data = json_decode($datas, true);
			if(sizeof($data)>0) {
				templates::assign_var("count_Yui", "true");
			}
		}
		templates::assign_var("count_unmoder", $this->unmoder());
		templates::assign_var("head_title", $this->headTitle());
		templates::assign_var("title_admin", $this->title());
		$this->loadMenu();
		templates::assign_var("nowLangText", "{L_Languages}&nbsp;".nucfirst(lang::get_lg()));
		templates::assign_var("nowLangImg", ADMIN_FLAGS_UI.lang::get_lg().".png");
		$support = lang::support(true);
		for($i=0;$i<sizeof($support);$i++) {
			$lang = nucfirst($support[$i]);
			templates::assign_vars(array("img" => ADMIN_FLAGS_UI.$support[$i].".png", "langMenu" => $support[$i], "lang" => "{L_Languages}&nbsp;".$lang), "langListSupport", "lang".($i+1));
		}
		$this->ReadPlugins();
		if(sizeof(self::$modules)>0 && isset(self::$modules['before'])) {
			foreach(self::$modules['before'] as $name => $func) {
				call_user_func($func, false);
			}
		}
		$echos = templates::view(templates::completed_assign_vars("main", null));
		if(sizeof(self::$modules)>0 && isset(self::$modules['after'])) {
			foreach(self::$modules['after'] as $name => $func) {
				$echos = call_user_func($func, $echos);
			}
		}
		$js_echo = "";
		if(sizeof(self::$js)>0) {
			$js = array_values(self::$js);
			for($o=0;$o<sizeof($js);$o++) {
				$js_echo .= '<script src="'.$js[$o].'" type="text/javascript"></script>';
			}
		}
		$echos = str_replace("{js_list}", execEvent("admin_print_script", $js_echo), $echos);
		$css_echo = "";
		if(sizeof(self::$css)>0) {
			$css = array_values(self::$css);
			for($o=0;$o<sizeof($css);$o++) {
				$css_echo .= '<link rel="stylesheet" type="text/css" href="'.$css[$o].'">';
			}
		}
		$echos = str_replace("{css_list}", execEvent("admin_print_style", $css_echo), $echos);
		$ret = "";
		$info = array();
		if(file_exists($dirToSave) && is_readable($dirToSave) && file_exists($pathToSave) && is_readable($pathToSave)) {
			$file = file_get_contents($pathToSave);
			$info = json_decode($file, true);
		}
		$info = execEvent("admin_core_prints_info", $info);
		if(sizeof($info)>0) {
			$isUnset = false;
			foreach($info as $k => $arr) {
				if(isset($arr['hide']) && $arr['hide']===true) {
					continue;
				}
				if($arr['time']<time()) {
					$isUnset = true;
					unset($info[$k]);
					continue;
				}
				$class = array("update-nag");
				if(isset($arr['block']) || (isset($arr['closed']) && $arr['closed'])) {
					$class[] = "block";
				}
				if(isset($arr['type'])) {
					$class[] = $arr['type'];
				} else {
					$class[] = "info";
				}
				if(isset($arr['closed']) && $arr['closed']) {
					$class[] = "is-dismissible";
				}
				$rt = '<div><div class="'.implode(" ", $class).'"><span>'.$arr['echo'].'</span>'.(isset($arr['closed']) && $arr['closed'] ? '<button type="button" class="dismiss" data-code="'.$arr['code'].'"><span class="text">{L_"Скрыть это уведомление"}.</span></button>' : '').'</div></div>';
				$ret .= $rt;
			}
			if($isUnset) {
				if(sizeof($info)>0) {
					$arrs = json_encode($info);
					if(file_exists($dirToSave) && !is_writable($dirToSave)) {
						@chmod($dirToSave, 0777);
					}
					if(file_exists($dirToSave) && is_readable($dirToSave) && file_exists($pathToSave) && !is_writable($pathToSave)) {
						@chmod($pathToSave, 0777);
					}
					file_put_contents($pathToSave, $arrs);
				} else {
					if(file_exists($dirToSave) && !is_writable($dirToSave)) {
						@chmod($dirToSave, 0777);
					}
					if(file_exists($dirToSave) && is_readable($dirToSave) && file_exists($pathToSave) && !is_writable($pathToSave)) {
						@chmod($pathToSave, 0777);
					}
					@unlink($pathToSave);
				}
			}
		}
		if(strpos($echos, "{info}")!==false) {
			$echos = str_Replace("{info}", $ret, $echos);
		} else {
			$echos = $ret.$echos;
		}
		if(strpos($echo, "{contentForAdmin}")!==false) {
			$echo = str_replace("{contentForAdmin}", self::$content['before'], $echo);
		} else {
			$echo = self::$content['before'].$echo.self::$content['after'];
		}
		if(strpos($echo, "{contentForAdmin}")!==false) {
			$echo .= self::$content['after'];
		}
		$echoView = templates::view($echo);
		if(empty($echoView) && $force) {
			$echoView = $echo;
		}
		$configTinymce = execEvent("configTinymce", false);
		if($configTinymce===false) {
			$sublink = array();
			if(isset($_GET['pages']) || Route::param("in_page", false)) {
				if(($sublinke = Route::param("in_page", false))!==false) {
					$sublink[] = $sublinke;
				} else if(isset($_GET['pages'])) {
					$sublink[] = $_GET['pages'];
				}
			}
			if(isset($_GET['type']) || Route::param("type", false)) {
				if(($sublinke = Route::param("type", false))!==false) {
					$sublink[] = $sublinke;
				} else if(isset($_GET['type'])) {
					$sublink[] = $_GET['pages'];
				}
			}
			$sublink = implode("-", $sublink);
			if(file_exists(PATH_CACHE_USERDATA."configTinymce-".$sublink.".json")) {
				$configTinymce = file_get_contents(PATH_CACHE_USERDATA."configTinymce-".$sublink.".json");
			} else if(file_exists(PATH_CACHE_USERDATA."configTinymce.json")) {
				$configTinymce = file_get_contents(PATH_CACHE_USERDATA."configTinymce.json");
			} else {
				$configTinymce = file_get_contents(PATH_MEDIA."configTinymce.json");
			}
		}
		$lang = lang::support(true);
		$lang = array_map("nucfirst", $lang);
		$echos = str_replace("{langSupport}", json_encode($lang), $echos);
		$echos = str_replace("{configTinymce}", $configTinymce, $echos);
		$echoView = execEvent("print_before_admin").execEvent("print_admin", $echoView);
		$echoView .= execEvent("print_after_admin");
		echo execEvent("printed_admin", str_replace("{main_admin}", $echoView, $echos));
	}
	
}

?>