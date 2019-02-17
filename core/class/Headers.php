<?php
if (!defined("IS_CORE")) {
	echo "403 ERROR";
	die();
}

class Headers {

	private $configMetaData = false;

	public function create_js() {
		global $manifest;
		$sRet = "";
		if (($jscss = Arr::get($manifest, 'jscss', false)) !== false && sizeof($manifest['jscss']) > 0) {
			if (($js = Arr::get($jscss, 'js')) !== false) {
				$js = execEvent("before_jscss_print_js", $js);
				if (isset($js['link']) && is_array($js['link']) && sizeof($js['link']) > 0) {
					foreach ($js['link'] as $v) {
						$sRet .= "<script type=\"text/javascript\" src=\"" . $v['url'] . "\"" . (isset($v['defer']) && $v['defer'] == true ? " defer=\"defer\"" : "") . (isset($v['cross']) && $v['cross'] ? " crossorigin=" . ($v['cross'] === true ? "\"crossorigin\"" : $v['cross']) : "") . "></script>\n";
					}
				}
				if (isset($js['full']) && is_array($js['full']) && sizeof($js['full']) > 0) {
					foreach ($js['full'] as $v) {
						$sRet .= "<script type=\"text/javascript\"" . (isset($v['defer']) && $v['defer'] == true ? " defer=\"defer\"" : "") . ">" . $v['url'] . "</script>\n";
					}
				}
			}
		}
		unset($all, $js, $user);
		return $sRet;
	}

	public function create_css() {
		global $manifest;
		$sRet = "";
		if (($jscss = Arr::get($manifest, 'jscss', false)) !== false && sizeof($manifest['jscss']) > 0) {
			if (($css = Arr::get($jscss, 'css')) !== false) {
//isset($manifest['jscss']['css'])
				$css = execEvent("before_jscss_print_css", $css);
				if (isset($css['link']) && is_array($css['link']) && sizeof($css['link']) > 0) {
					foreach ($css['link'] as $v) {
						$sRet .= "<link href=\"" . $v['url'] . "\" " . (isset($v['defer']) && $v['defer'] == true ? "rel=\"preload\" as=\"style\" onload=\"this.rel = 'stylesheet'\"" : "rel=\"stylesheet\" type=\"text/css\"") . (isset($v['cross']) && $v['cross'] ? " crossorigin=" . ($v['cross'] === true ? "\"crossorigin\"" : $v['cross']) : "") . ">\n";
					}
				}
				if (isset($css) && isset($css['full']) && is_array($css['full']) && sizeof($css['full']) > 0) {
					foreach ($css['full'] as $v) {
						$sRet .= "<style type=\"text/css\">" . $v['url'] . "</style>\n";
					}
				}
			}
		}
		unset($all, $js, $user);
		return $sRet;
	}

	private function faviconBuilder($link, $size = "16x16") {
		$type = explode(".", $link);
		$type = end($type);
		$types = HTTP::getContentTypes();
		$type = $types[$type];
		$type2 = explode("/", $type);
		$type2 = end($type2);
		$header = "<link rel=\"shortcut icon\" href=\"" . $link . "\" type=\"image/" . $type . "\" />\n";
		$header .= "<link rel=\"shortcut icon\" href=\"" . $link . "\" type=\"image/" . $type2 . "\" sizes=\"" . $size . "\" />\n";
		$header .= "<link rel=\"icon\" type=\"image/" . $type2 . "\" href=\"" . $link . "\" sizes=\"" . $size . "\" />\n";
		$header .= "<link rel=\"icon\" type=\"image/vnd.microsoft.icon\" href=\"" . $link . "\" sizes=\"" . $size . "\" />\n";
		return $header;
	}

	public function getFavicon($link, array $size = array()) {
		$html = "";
		$size[] = "";
		$size[] = "16x16";
		$size = array_values($size);
		$keys = array();
		for ($i = 0; $i < sizeof($size); $i++) {
			if (file_exists(ROOT_PATH . $link . $size[$i] . ".ico")) {
				$keys[ROOT_PATH . $link . $size[$i] . ".ico"] = array("url" => get_site_path(ROOT_PATH . $link . $size[$i] . ".ico"), "size" => $size[$i]);
			}
			if (file_exists(ROOT_PATH . $link . $size[$i] . ".png")) {
				$keys[ROOT_PATH . $link . $size[$i] . ".png"] = array("url" => get_site_path(ROOT_PATH . $link . $size[$i] . ".png"), "size" => $size[$i]);
			}
			if (file_exists(ROOT_PATH . $link . $size[$i] . ".jpg")) {
				$keys[ROOT_PATH . $link . $size[$i] . ".jpg"] = array("url" => get_site_path(ROOT_PATH . $link . $size[$i] . ".jpg"), "size" => $size[$i]);
			}
			if (file_exists(ROOT_PATH . $link . $size[$i] . ".jpeg")) {
				$keys[ROOT_PATH . $link . $size[$i] . ".jpeg"] = array("url" => get_site_path(ROOT_PATH . $link . $size[$i] . ".jpeg"), "size" => $size[$i]);
			}
		}
		$keys = array_values($keys);
		for ($i = 0; $i < sizeof($keys); $i++) {
			$html .= $this->faviconBuilder($keys[$i]['url'], (empty($keys[$i]['size']) ? "16x16" : $keys[$i]['size']));
		}
		if (sizeof($keys) > 0) {
			$e = end($keys);
			$html .= "<link rel=\"apple-touch-icon-precomposed\" href=\"" . $e['url'] . "\" />\n";
			$html .= "<link rel=\"apple-touch-icon\" href=\"" . $e['url'] . "\" />\n";
			$html .= "<meta name=\"msapplication-TileImage\" content=\"" . $e['url'] . "\" />\n";
		}
		return $html;
	}

	private static $isAdmin = false;

	function __construct() {
		if (config::Select("deactive_site_adminbar") === false && isset($_COOKIE[COOK_ADMIN_USER]) && isset($_COOKIE[COOK_ADMIN_PASS]) && userlevel::get("admin") && Arr::get($_GET, "noShowAdmin", false) === false && !defined("IS_NOSHOWADMIN") && self::$isAdmin === false) {
			modules::regCssJs(array(
				"fontawesome-font" => "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css",
				"admin-bar-css" => get_site_path(PATH_SKINS) . "core/admin.min.css",
			), "css", true);
			modules::regCssJs(get_site_path(PATH_SKINS) . "core/admin.min.js", "js", true, "admin-bar-js");
			self::$isAdmin = true;
		}
	}

	function builder($array = array(), $clear = false, $no_js = false, $no_css = false) {
		$header = "";
		$getMeta = Arr::get($array, 'meta', false);
		$sitename = htmlspecialchars(lang::get_lang("sitename"));
		$sitename = cardinalEvent::execute("before_show_sitename", $sitename);
		$activeAdmin = false;
		if (self::$isAdmin === true) {
			$activeAdmin = true;
		}
		if (!Arr::get($array, 'title', false) && empty($array['title'])) {
			$array['title'] = $sitename;
		}
		$array['title'] = cardinalEvent::execute("before_show_title", $array['title']);
		$header .= "\t<title>" . $array['title'] . "</title>\n";

		$header .= "<meta name=\"generator\" content=\"Cardinal " . VERSION . "\" />\n";

		if (!Arr::get($array, 'author', false)) {
			$array['author'] = "Cardinal " . VERSION;
		}
		$array['author'] = cardinalEvent::execute("before_show_author", $array['author']);

		$header .= "<meta name=\"author\" content=\"" . $array['author'] . "\" />\n";
		$header .= "<meta name=\"copyright\" content=\"" . $sitename . "\" />\n";

		if ($getMeta && !array_key_exists("robots", $getMeta) && defined("DEVELOPER_MODE")) {
			$robots = "noindex, nofollow";
		} else if ($getMeta && array_key_exists("robots", $getMeta)) {
			$robots = $array['meta']['robots'];
		}
		if (empty($robots)) {
			$robots = "all";
		}
		$robots = cardinalEvent::execute("before_show_robots", $robots);
		$header .= "<meta name=\"robots\" content=\"" . $robots . "\" />\n";

		$header .= $this->getFavicon(DS . "favicon", array(
			"32x32",
			"64x64",
			"128x128",
		));
		$header .= $this->getFavicon(DS . "uploads" . DS . "icon" . DS . "favicon-", array(
			"32x32",
			"64x64",
			"128x128",
		));

		$imageCheck = file_exists(ROOT_PATH . "logo.gif") || file_exists(ROOT_PATH . "logo.jpg") || file_exists(ROOT_PATH . "logo.jpeg") || file_exists(ROOT_PATH . "logo.png") || file_exists(ROOT_PATH . "uploads" . DS . "logo-for-site.gif") || file_exists(ROOT_PATH . "uploads" . DS . "logo-for-site.jpg") || file_exists(ROOT_PATH . "uploads" . DS . "logo-for-site.jpeg") || file_exists(ROOT_PATH . "uploads" . DS . "logo-for-site.png");
		if ($imageCheck) {
			if (file_exists(ROOT_PATH . "logo.gif")) {
				$imageLink = "{C_default_http_host}logo.gif";
			} else if (file_exists(ROOT_PATH . "logo.jpg")) {
				$imageLink = "{C_default_http_host}logo.jpg";
			} else if (file_exists(ROOT_PATH . "logo.png")) {
				$imageLink = "{C_default_http_host}logo.png";
			} else if (file_exists(ROOT_PATH . "uploads" . DS . "logo-for-site.gif")) {
				$imageLink = "{C_default_http_host}uploads/logo-for-site.gif";
			} else if (file_exists(ROOT_PATH . "uploads" . DS . "logo-for-site.jpg")) {
				$imageLink = "{C_default_http_host}uploads/logo-for-site.jpg";
			} else if (file_exists(ROOT_PATH . "uploads" . DS . "logo-for-site.jpeg")) {
				$imageLink = "{C_default_http_host}uploads/logo-for-site.jpeg";
			} else if (file_exists(ROOT_PATH . "uploads" . DS . "logo-for-site.png")) {
				$imageLink = "{C_default_http_host}uploads/logo-for-site.png";
			} else {
				$imageCheck = false;
			}
		}
		if ($imageCheck && !empty($imageLink)) {
			$header .= "<meta property=\"og:image\" content=\"" . $imageLink . "?" . time() . "\" />\n";
			$header .= "<meta itemprop=\"image\" content=\"" . $imageLink . "?" . time() . "\" />\n";
			$header .= "<link rel=\"apple-touch-startup-image\" href=\"" . $imageLink . "?" . time() . "\">\n";
		}

		if (!$clear) {
			$viewport = cardinalEvent::execute("before_show_viewport", config::Select("viewport"));
			$header .= '<meta name="viewport" content="' . $viewport . '" />' . "\n";
			$header .= '<base href="{C_default_http_host}" />' . "\n";
			$header .= '<meta http-equiv="imagetoolbar" content="no" />' . "\n";
			$header .= '<meta http-equiv="url" content="{C_default_http_host}">' . "\n";
			$header .= '<meta http-equiv="cleartype" content="on">' . "\n";
			$header .= '<meta http-equiv="X-UA-Compatible" content="IE=edge">' . "\n";
			$header .= '<!-- saved from url=(0014)about:internet -->' . "\n";
			$header .= '<meta name="apple-mobile-web-app-capable" content="yes">' . "\n";
			$header .= '<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">' . "\n";
			$header .= '<meta name="apple-mobile-web-app-title" content="' . $sitename . '">' . "\n";
			$header .= '<meta name="format-detection" content="telephone=no">' . "\n";
			$header .= '<meta name="format-detection" content="address=no">' . "\n";
			$header .= '<meta name="google" value="notranslate">' . "\n";
			$header .= '<meta name="skype_toolbar" content="skype_toolbar_parser_compatible">' . "\n";
			$header .= '<meta name="msapplication-tap-highlight" content="no">' . "\n";
			$header .= '<meta name="application-name" content="' . $sitename . '">' . "\n";
			$header .= '<meta name="renderer" content="webkit">' . "\n";
			$header .= '<meta name="x5-fullscreen" content="true">' . "\n";
			$header .= '<meta name="rating" content="General">' . "\n";
			$support = lang::support();
			$support = cardinalEvent::execute("before_show_support_lang", $support);
			for ($i = 1; $i < sizeof($support); $i++) {
				$clearLang = nsubstr($support[$i], 4, -3);
				$header .= '<link rel="alternate" href="{C_default_http_host}' . $clearLang . '/" hreflang="' . $clearLang . '">' . "\n";
			}
			$param = array();
			$dprm = Route::param();
			foreach ($dprm as $k => $v) {
				if (is_object($v)) {
					$v = get_class($v);
				}
				$param[] = "\"" . $k . "\": \"" . $v . "\"";
			}
			unset($dprm);
			$header .= "<script type=\"text/javascript\">\n" .
			"	var username = \"{U_username}\";\n" .
			"	var default_link = \"{C_default_http_host}\";\n" .
			"	var tskins = \"" . templates::get_skins() . "\";\n" .
			"	var SystemTime = \"" . time() . "\";\n" .
			"	var loadedPage = \"" . Route::getLoaded() . "\";\n" .
			"	var loadedParam = {" . implode(",", $param) . "};\n" .
				"</script>\n";
		}
		$canonical = cardinalEvent::execute("before_show_canonical", Arr::get($getMeta, 'canonical'));
		if ($canonical) {
			$header .= "<link rel=\"canonical\" href=\"" . $canonical . "\" />\n";
		}
		if (!$no_js) {
			execEvent("before_jscss_js_print");
			$header .= $this->create_js($clear);
		}
		if (!$no_css) {
			execEvent("before_jscss_css_print");
			$header .= $this->create_css($clear);
		}
		$link_rss = "";
		if (!file_exists(ROOT_PATH . "rss.xml")) {
			$rss = Route::Search("rss");
			if ($rss) {
				$link = Route::get("rss");
				$link_rss = $link->uri(array());
			}
		} else {
			$rss = true;
		}
		if ($rss && !empty($link_rss)) {
			$header .= "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"" . $sitename . "\" href=\"{C_default_http_host}" . $link_rss . "\" />\n";
			$header .= "<meta name=\"msapplication-TileColor\" content=\"#e0161d\"/>\n" .
				"<meta name=\"msapplication-notification\" content=\"frequency=30;polling-uri=http://notifications.buildmypinnedsite.com/?feed={C_default_http_host}" . $link_rss . "&amp;id=1;polling-uri2=http://notifications.buildmypinnedsite.com/?feed={C_default_http_host}" . $link_rss . "&amp;id=2;polling-uri3=http://notifications.buildmypinnedsite.com/?feed={C_default_http_host}" . $link_rss . "&amp;id=3;polling-uri4=http://notifications.buildmypinnedsite.com/?feed={C_default_http_host}" . $link_rss . "&amp;id=4;polling-uri5=http://notifications.buildmypinnedsite.com/?feed={C_default_http_host}" . $link_rss . "&amp;id=5; cycle=1\"/>\n\n";
		}
		if ($getMeta) {
			$getMeta = cardinalEvent::execute("before_show_meta", $getMeta);
		}
		if (($type = Arr::get($getMeta, 'type_meta', false)) !== false) {
			$is_use = true;
			$header .= "<span itemscope itemtype=\"" . $type . "\">\n";
			unset($array['meta']['type_meta']);
		} else {
			$is_use = false;
		}

		if (($type = Arr::get($getMeta, 'og', false)) !== false && is_array($type)) {
			foreach ($type as $name => $val) {
				$header .= "<meta itemprop=\"" . $name . "\" content=\"" . $val . "\" />\n";
			}
		}
		if (($type = Arr::get($getMeta, 'ogpr', false)) !== false && is_array($type)) {
			foreach ($type as $name => $val) {
				$header .= "<meta property=\"" . $name . "\" content=\"" . $val . "\" />\n";
			}
		}
		if (($type = Arr::get($getMeta, 'link', false)) !== false && is_array($type)) {
			foreach ($type as $name => $val) {
				$header .= "<link rel=\"" . $name . "\" href=\"" . $val . "\" />\n";
			}
		}
		if (($this->configMetaData = config::Select("configMetaData")) !== false) {
			$this->configMetaData = json_decode($this->configMetaData, true);
			if (isset($this->configMetaData['meta'])) {
				$this->configMetaData['meta'] = array_values($this->configMetaData['meta']);
				$metaData = array();
				for ($i = 0; $i < sizeof($this->configMetaData['meta']); $i++) {
					if (!isset($this->configMetaData['meta'][$i]) || !isset($this->configMetaData['meta'][$i]['name']) || !isset($this->configMetaData['meta'][$i]['content'])) {
						continue;
					}
					$metaData[$this->configMetaData['meta'][$i]['name']] = $this->configMetaData['meta'][$i]['content'];
				}
				$getMeta = array_merge($getMeta, $metaData);
				unset($metaData);
			}
			cardinalEvent::addListener("templates::display", array($this, "configMetaDatas"));
		}
		if ($getMeta) {
			foreach ($getMeta as $name => $val) {
				if (is_array($val) || ($name == "robots" && defined("DEVELOPER_MODE"))) {
					continue;
				}
				$header .= "<meta name=\"" . $name . "\" content=\"" . $val . "\" />\n";
			}
		}
		if ($is_use) {
			$header .= "</span>";
		}
		if (isset($array['meta'])) {
			$array['meta'] = cardinalEvent::execute("after_show_meta", $getMeta);
		}
		if ($activeAdmin) {
			$links = array();
			$this->loadMenuAdmin($links);
			sort($links);
			execEventRef("admin_menu_sorted", $links);
			$level = User::get("level");
			$newMenu = array();
			foreach ($links as $name => $datas) {
				$types = array_values($datas);
				for ($i = 0; $i < sizeof($types); $i++) {
					for ($is = 0; $is < sizeof($types[$i]); $is++) {
						if (isset($types[$i][$is]['access']) && $types[$i][$is]['access'] != $level) {
							break;
						}
						if ($types[$i][$is]['type'] == "cat") {
							$newMenu[$name] = $types[$i][$is];
						} elseif (isset($newMenu[$name]) && $newMenu[$name]['link'] != $types[$i][$is]['link']) {
							$newMenu[$name]['items'][$types[$i][$is]['link']] = $types[$i][$is];
						}
					}
				}
			}
			$editPage = "";
			$editor = modules::manifest_get("editor");
			if ($editor !== false && Arr::get($editor, "class", false)) {
				$editPage = "{C_default_http_local}{D_ADMINCP_DIRECTORY}/?pages=" . $editor['class'] . (isset($editor['page']) ? "&" . $editor['page'] : "") . (defined("ROUTE_GET_URL") ? "&ref=" . urlencode(HTTP::getServer(ROUTE_GET_URL)) : "");
			}
			$menu = "<div class=\"adminCoreCardinal\"><a href=\"{C_default_http_local}\" class=\"logo\"></a>" . (config::Select("deactiveMainMenu") != "1" ? "<a href=\"{C_default_http_local}{D_ADMINCP_DIRECTORY}/{C_mainPageAdmin}\" class=\"linkToAdmin\">{L_'adminpanel'}</a>" : "") . "<div class=\"elems\">" . (!empty($editPage) ? "<div class=\"items\"><a href=\"" . $editPage . "\"><i class=\"fa-edit\"></i><span>{L_'Редактировать'}</span></a></div>" : "") . $this->menuAdminHeader($newMenu) . "<div class=\"user\"><span>{U_username}</span><div class=\"dropped\"><a href=\"{C_default_http_local}{D_ADMINCP_DIRECTORY}/?pages=Login&out\"><i class=\"fa-user-times\"></i>{L_'logout'}</a></div></div></div></div>";
			cardinalEvent::addListener("templates::display", array($this, "addAdminPanelToPage"), $menu);
		}
		unset($array);
		return $header;
	}

	function loadMenuAdmin(&$links, &$now = "") {
		$loadMenu = true;
		execEventRef("admin_menu_ready", $links, $loadMenu);
		if ($loadMenu) {
			if ($dh = dir(ADMIN_MENU)) {
				$i = 1;
				while (($file = $dh->read()) !== false) {
					if ($file != "index." . ROOT_EX && $file != "index.html" && $file != ".htaccess" && $file != "." && $file != "..") {
						include ADMIN_MENU . $file;
					}
				}
				$dh->close();
			}
		}
		$this->adminPanelVsort($links);
		$page_v = HTTP::getServer("REQUEST_URI");
		$local = config::Select("default_http_local");
		if (strlen($local) > 1) {
			$page_v = str_replace($local, "/", $page_v);
		}
		$now = str_replace(array(ADMINCP_DIRECTORY . "/?", ADMINCP_DIRECTORY . "/"), "", substr($page_v, 1, strlen($page_v)));
		if ($now === "") {
			$now = "pages=main";
		}
		execEventRef("admin_menu_loaded", $links, $now);
	}

	private function menuAdminHeader($arr, $isCat = false) {
		$menu = "";
		foreach ($arr as $v) {
			if (isset($v['items']) && sizeof($v['items']) == 1) {
				$item = current($v['items']);
				$v['link'] = $item['link'];
				unset($v['items']);
			}
			$cat = false;
			if (isset($v['items'])) {
				$cat = true;
			}
			$menu .= (!$isCat ? "<div class=\"items" . ($cat ? " hasDropped" : "") . "\">" : "") . "<a href=\"" . $v['link'] . "\" class=\"" . ($cat ? "subItem" : "") . "" . (isset($v['class']) ? " " . $v['class'] : "") . "\">" . (isset($v['icon']) && !empty($v['icon']) ? "<i class=\"" . $v['icon'] . "\"></i>" : "") . "<span>" . $v['title'] . "</span></a>";
			$menu .= ($cat ? "<div class=\"dropped\">" : "");
			if ($cat) {
				$menu .= $this->menuAdminHeader($v['items'], true);
			}
			$menu .= ($cat ? "</div>" : "") . (!$isCat ? "</div>\n" : "");
		}
		return $menu;
	}

	private function adminPanelVsort(&$array) {
		$arrs = array();
		foreach ($array as $key => $val) {
			asort($val);
			$arrs[$key] = $val;
		}
		$array = $arrs;
	}

	function addAdminPanelToPage($page, $data) {
		if (defined("ADMINCP_POSITION_BOTTOM")) {
			$data = preg_replace("#<html(.*?)>#", "<html$1 data-body=\"bottom\">", $data);
			$data = str_replace("adminCoreCardinal", "adminCoreCardinal bottom", $data);
		}
		if (preg_match('#<html(.*?)>#i', $data)) {
			$data = preg_replace_callback('#<html(.*?)>#i', array($this, "callBackAdminPanelToPage"), $data);
		} elseif (preg_match('/<html(.*?)>/', $data)) {
			$data = preg_replace('/<html(.*?)>/', '<html$1class="adminbarCardinal">', $data);
		} else {
			$data = str_replace('<html>', '<html class="adminbarCardinal">', $data);
		}
		$data = preg_replace("#<html(.*?)>#i", "<html$1 data-body=\"top\">", $data);
		$data = str_replace("</body>", templates::view($page) . "</body>", $data);
		return $data;
	}

	function callBackAdminPanelToPage($arr) {
		$ret = $arr[0];
		if (isset($arr[1])) {
			$or = $arr[1];
			if (preg_match('#class=[\'"].+?[\'"]#', $arr[1], $match)) {
				$arr[1] = preg_replace('#class=([\'"])(.+?)([\'"])#', "class=$1$2 adminbarCardinal$3", $arr[1]);
			} else {
				$arr[1] .= " class=\"adminbarCardinal\"";
			}
			$ret = str_replace($or, $arr[1], $arr[0]);
		}
		return $ret;
	}

	function configMetaDatas($tmp) {
		if ($this->configMetaData !== false) {
			if (isset($this->configMetaData['head'])) {
				$tmp = str_replace("</head>", $this->configMetaData['head'] . "</head>", $tmp);
			}
			if (isset($this->configMetaData['body'])) {
				$tmp = str_replace("</body>", $this->configMetaData['body'] . "</body>", $tmp);
			}
		}
		return $tmp;
	}
}