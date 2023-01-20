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
		$name = get_class();
		$cl = new $name();
		return call_user_func_array(array($cl, $call), $args);
	}
	
	public function __call($call, array $args) {
		return call_user_func_array(array($this, $call), $args);
	}
	
	protected function ParseLang() {
		global $lang;
		if(!is_array($lang)) {
			$lang = array();
		}
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
		return true;
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
		return true;
	}
	
	protected function headTitle($titles = "") {
		if(!empty($titles)) {
			$this->headTitle = $titles;
		} else {
			return $this->headTitle;
		}
		return true;
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

	protected function get_file_data($file, $default_headers, $default = false) {
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
		return true;
	}

	private function loadMenu() {
		$links = array();
		$now = "";
		$l = new Headers();
		$l->loadMenuAdmin($links, $now);
		$all = 0;
		$findActive = false;
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
					$is_now = str_replace(array("{C_default_http_host}", ADMINCP_DIRECTORY."/?", "{D_ADMINCP_DIRECTORY}/?", ADMINCP_DIRECTORY."/", "{D_ADMINCP_DIRECTORY}/"), "", $datas[$i][$is]['link']);
					// var_dump($active);
					$active = !empty($is_now) && ($now==$is_now || strpos($now, $is_now."&")!==false || strpos($is_now, $now."&")!==false) && !$findActive;
					if($active) {
						$findActive = true;
						// var_dump($active);
					}
					templates::assign_vars(array(
						"existSub" => ($type=="cat"&&sizeof($datas)>1 ? "true" : "false"),
						"value" => $datas[$i][$is]['title'],
						"link" => $datas[$i][$is]['link'],
						"is_now" => $active ? "1" : "0",
						"type" => $type,
						"type_st" => ($type=="cat"&&$datas[$i][$is]['type']=="cat" ? "start" : ""),
						"type_end" => ($type=="cat"&&$count==$is&&$datas[$i][$is]['type']=="item" ? "end" : ""),
						"icon" => (isset($datas[$i][$is]['icon']) ? $datas[$i][$is]['icon'] : " "),
						"styleLi" => (isset($datas[$i][$is]['styleLi']) ? $datas[$i][$is]['styleLi'] : ""),
						"styleA" => (isset($datas[$i][$is]['styleA']) ? $datas[$i][$is]['styleA'] : ""),
						"styleI" => (isset($datas[$i][$is]['styleI']) ? $datas[$i][$is]['styleI'] : ""),
						"styleSpan" => (isset($datas[$i][$is]['styleSpan']) ? $datas[$i][$is]['styleSpan'] : ""),
					), "menu", "m".$all.$i.$is);
				}
			}
			$all++;
		}
		// die();
	}
	
	private function CheckLoadPlugins($file) {
		if(is_bool($this->load_adminmodules)) {
			if(file_exists(ADMIN_VIEWER."Core".DS."Plugins".DS."loader.".ROOT_EX)) {
				$adminCore = array();
				include(ADMIN_VIEWER."Core".DS."Plugins".DS."loader.".ROOT_EX);
				$this->load_adminmodules = $adminCore;
			} else if(file_exists(ADMIN_VIEWER."Core".DS."Plugins".DS."loader.default.".ROOT_EX)) {
				$adminCore = array();
				include(ADMIN_VIEWER."Core".DS."Plugins".DS."loader.default.".ROOT_EX);
				$this->load_adminmodules = $adminCore;
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

	public static function addInfo($echo, $type = "info", $closed = false, $time = -1, $block = false) {
		$defaults = array(
			"echo" => "",
			"type" => "info",
			"closed" => false,
			"time" => 10,
			"block" => false,
		);
		if(is_array($echo)) {
			$echos = "";
			$echo = array_merge($defaults, $echo);
			if(isset($echo['type'])) { $type = $echo['type']; }
			if(isset($echo['closed'])) { $closed = $echo['closed']; }
			if(isset($echo['time'])) { $time = $echo['time']; }
			if(isset($echo['block'])) { $block = $echo['block']; }
			if(isset($echo['echo'])) { $echos = $echo['echo']; }
			unset($echo);
			$echo = $echos;
		}
		if($echo==="") {
			trigger_error("First param is not set");die();
		}
		if($type!="success" && $type!="warning" && $type!="error" && $type!="info") {
			trigger_error("Error type for info");die();
		}
		$r = new Request();
		$arr = $r->session->get("infoForAdmin", array());
		if(!is_array($arr)) {
			$arr = array();
		}
		$code = md5($echo);
		if(!isset($arr[$code])) {
			$arr = array_merge($arr, array(
				$code => array(
					"echo" => $echo,
					"type" => $type,
					"time" => ($time>-1 ? ($time<time() ? time()+$time : $time) : time()+10),
					"closed" => $closed,
					"code" => $code,
					"block" => false,
				)
			));
		} else {
			$arr[$code]['time'] = ($time>-1 ? ($time<time() ? time()+$time : $time) : time()+10);
			$arr[$code]['closed'] = $closed;
			$arr[$code]['type'] = $type;
			$arr[$code]['block'] = $block;
		}
		$r->session->add("infoForAdmin", $arr);
		return $arr;
	}

	function headers($echo) {
		$header = "";
		$h = new Headers();
		$header .= $h->getFavicon(DS."favicon", array(
			"32x32",
			"64x64",
			"128x128",
		));
		$header .= $h->getFavicon(DS."uploads".DS."icon".DS."favicon-", array(
			"32x32",
			"64x64",
			"128x128",
		));
		$imageCheck = file_exists(ROOT_PATH."logo.gif") || file_exists(ROOT_PATH."logo.jpg") || file_exists(ROOT_PATH."logo.jpeg") || file_exists(ROOT_PATH."logo.png") || file_exists(ROOT_PATH."uploads".DS."logo-for-site.gif") || file_exists(ROOT_PATH."uploads".DS."logo-for-site.jpg") || file_exists(ROOT_PATH."uploads".DS."logo-for-site.jpeg") || file_exists(ROOT_PATH."uploads".DS."logo-for-site.png");
		if($imageCheck) {
			if(file_exists(ROOT_PATH."logo.gif")) {
				$imageLink = config::Select("default_http_host")."logo.gif";
			} else if(file_exists(ROOT_PATH."logo.jpg")) {
				$imageLink = config::Select("default_http_host")."logo.jpg";
			} else if(file_exists(ROOT_PATH."logo.png")) {
				$imageLink = config::Select("default_http_host")."logo.png";
			} else if(file_exists(ROOT_PATH."uploads".DS."logo-for-site.gif")) {
				$imageLink = config::Select("default_http_host")."uploads/logo-for-site.gif";
			} else if(file_exists(ROOT_PATH."uploads".DS."logo-for-site.jpg")) {
				$imageLink = config::Select("default_http_host")."uploads/logo-for-site.jpg";
			} else if(file_exists(ROOT_PATH."uploads".DS."logo-for-site.jpeg")) {
				$imageLink = config::Select("default_http_host")."uploads/logo-for-site.jpeg";
			} else if(file_exists(ROOT_PATH."uploads".DS."logo-for-site.png")) {
				$imageLink = config::Select("default_http_host")."uploads/logo-for-site.png";
			} else {
				$imageCheck = false;
			}
		}
		if($imageCheck && !empty($imageLink)) {
			$header .= "<meta property=\"og:image\" content=\"".$imageLink."?".time()."\" />\n";
			$header .= "<meta itemprop=\"image\" content=\"".$imageLink."?".time()."\" />\n";
			$header .= "<link rel=\"apple-touch-startup-image\" href=\"".$imageLink."?".time()."\">\n";
		}
		$header .= "<meta name=\"apple-mobile-web-app-title\" content=\"".(!empty($headTitle) ? $headTitle." &rsaquo; " : "")."Admin Panel for ".lang::get_lang("sitename")."\">\n";
		$header .= "<meta name=\"apple-mobile-web-app-capable\" content=\"yes\">\n";
		return str_replace("{header}", $header, $echo);
	}

	public static function loadTemplateInSkins($file) {
		$f = file_get_contents(PATH_TEMPLATE.$file.".".templates::changeTypeTpl());
		return templates::view($f);
	}

	public function checkLogin() {
		return isset($_COOKIE[COOK_ADMIN_USER]) && isset($_COOKIE[COOK_ADMIN_PASS]) && userlevel::get("admin");
	}
	
	public function Prints($echo, $print = false, $force = false) {
		global $lang;
		if(!$this->checkLogin()) {
			$ref = urlencode(str_replace(ROOT_PATH, "", cut(getenv("REQUEST_URI"), "/".ADMINCP_DIRECTORY."/")));
			location("{C_default_http_host}".ADMINCP_DIRECTORY."/?pages=Login".(!empty($ref) ? "&ref=".$ref : ""));
			return false;
		}
		if(isset($_GET['removeCode'])) {
            $isUnset = false;
			$r = new Request();
			$arrs = $r->session->get("infoForAdmin", array());
			if(!is_array($arrs)) {
				$arrs = array();
			}
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
					$r->session->add("infoForAdmin", $arrs);
				} else {
					$r->session->delete("infoForAdmin", $arrs);
				}
			}
			HTTP::echos("done");
			return false;
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
		$uriNow = HTTP::getServer("REQUEST_URI");
		$link = config::Select("default_http_local").ADMINCP_DIRECTORY."/";
		$uriNow = substr($uriNow, strlen($link));
		$consolePage = config::Select("consolePageAdmin");
		$mainPage = config::Select("mainPageAdmin");

		$linkRedirect = "";
		if(empty($uriNow) && ($mainPage!=$consolePage || $uriNow!=$consolePage)) {
			$linkRedirect = $mainPage;
		} else if(empty($uriNow) && $mainPage==$consolePage && ($uriNow!="?pages=main" || stripos($uriNow, "pages=login")!==false)) {
			$linkRedirect = $mainPage;
		} else if(empty($uriNow) && $mainPage==$consolePage) {
			$linkRedirect = $mainPage;
		}
		if(!empty($linkRedirect)) {
			location($link.$linkRedirect);die();
		}
		$routeLang = HTTP::getServer(ROUTE_GET_URL);
		preg_match("#^/([a-zA-Z]+)/#", $routeLang, $arr);
		if(isset($_COOKIE['langSet'])) {
			$support = lang::support(true);
			if(in_array($_COOKIE['langSet'], $support)) {
				lang::set_lang($_COOKIE['langSet']);
				lang::init_lang();
				Route::RegParam("lang", $_COOKIE['langSet']);
				config::Set("lang", $_COOKIE['langSet']);
			}
		} else if(isset($arr[1])) {
			$support = lang::support(true);
			if(in_array($arr[1], $support)) {
				lang::set_lang($arr[1]);
				lang::init_lang();
				Route::RegParam("lang", $arr[1]);
				config::Set("lang", $arr[1]);
			}
		}
		if(Route::param("lang")!="") {
			$langs = Route::param("lang");
		} else {
			$langs = config::Select("lang");
		}
		templates::assign_var("langPanel", $langs);
		$this->ParseLang();
		Route::RegParam("lang", config::Select("lang"));
		if(!$print) {
			$echo = (templates::completed_assign_vars($echo, null));
		}
		execEvent("admin_print_ready");
		if(isset($_POST['jajax']) || isset($_GET['jajax'])) {
			HTTP::echos(templates::view($echo));
			return false;
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
		$headTitle = execEvent("print_admin_head_title", $this->headTitle());
		templates::assign_var("head_title", (!empty($headTitle) ? $headTitle." &rsaquo; " : "")."Admin Panel for {L_sitename}");
		templates::assign_var("title_admin", execEvent("print_admin_title", $this->title()));
		$this->loadMenu();
		templates::assign_var("nowLangText", "{L_Languages}&nbsp;".nucfirst(lang::get_lg()));
		templates::assign_var("nowLangImg", ADMIN_FLAGS_UI.lang::get_lg().".png");
		$support = lang::support(true);
		for($i=0;$i<sizeof($support);$i++) {
			$langs = nucfirst($support[$i]);
			templates::assign_vars(array("img" => ADMIN_FLAGS_UI.$support[$i].".png", "langMenu" => $support[$i], "lang" => "{L_Languages}&nbsp;".$langs), "langListSupport", "lang".($i+1));
		}
		$this->ReadPlugins();
		if(sizeof(self::$modules)>0 && isset(self::$modules['before'])) {
			foreach(self::$modules['before'] as $name => $func) {
				call_user_func($func, false);
			}
		}
		$echos = templates::view(templates::completed_assign_vars("main", null));
		$echos = $this->headers($echos);
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
		$r = new Request();
		$info = $r->session->get("infoForAdmin", array());
		if(!is_array($info)) {
			$info = array();
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
				$rt = '<div><div class="'.implode(" ", $class).'"'.(isset($arr['caller']) ? ' data-caller="'.$arr['caller'].'"' : "").'><span>'.$arr['echo'].'</span>'.(isset($arr['closed']) && $arr['closed'] ? '<button type="button" class="dismiss" data-code="'.$arr['code'].'"><span class="text">{L_"Скрыть это уведомление"}.</span></button>' : '').'</div></div>';
				$ret .= $rt;
			}
			if($isUnset) {
				if(sizeof($info)>0) {
					$r->session->add("infoForAdmin", $info);
				} else {
					$r->session->delete("infoForAdmin", $info);
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
				} else if(isset($_GET['type']) && isset($_GET['pages'])) {
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
		return true;
	}
	
}

?>