<?php
/*
 *
 * @version 5.4
 * @copyright 2014-2016 KilleR for Cardinal Engine
 *
 * Version Engine: 5.4
 * Version File: 22
 *
 * 21.1
 * add var for js detected system time in unix format
 * 22.1
 * add css and rebuild js data to template
 * 22.2
 * rebuild local data
 * 22.3
 * fix and clear include modules js and css files
 * 22.3
 * add meta tags author and copyright
 * 22.4
 * add support minify js
 * 22.5
 * add creator forms and add rebuild vh and vw to px
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

function meta($array = array()) {
	templates::assign_vars(array(
		"url" => "{C_default_http_host}",
		"title" => "{L_sitename}",
	), "meta", "1");
	if(sizeof($array)>0) {
		for($i=0;$i<sizeof($array);$i++) {
			templates::assign_vars(array(
				"url" => $array[$i]['url'],
				"title" => $array[$i]['title'],
			), "meta", ($i+3));
		}
	}
	unset($array);
	$te = templates::lcud(templates::completed_assign_vars("meta", null));
return $te;
}

function create_js($clear = false) {
global $user, $manifest;
	$css = $js = array();
	$sRet = "";
	$js_list = modules::manifest_get(array("create_js", "min"));
	if(!config::Select("js_min")) {
		if(!$clear) {
			if(isset($user) && isset($user['id']) && $user['id']==1) {
				$js[] = 'http://ie.microsoft.com/testdrive/HTML5/CompatInspector/inspector.js';
			}
		}
		$dirs = read_dir(PATH_LOADED_CONTENT."js".DS, ".".ROOT_EX);
		sort($dirs);
		for($i=0;$i<sizeof($dirs);$i++) {
			if(file_exists(PATH_LOADED_CONTENT."js".DS.$dirs[$i]) && $dirs[$i]!="index.html" && $dirs[$i]!="index.php") {
				include_once(PATH_LOADED_CONTENT."js".DS.$dirs[$i]);
			}
		}
		if(is_array($js)) {
			for($i=0;$i<sizeof($js);$i++) {
				$sRet .= "<script type=\"text/javascript\" src=\"".$js[$i].AmperOr($js[$i]).time()."\"></script>\n";
			}
		}
		unset($dirs, $js);
		$dirs = read_dir(PATH_LOADED_CONTENT."css".DS, ".".ROOT_EX);
		sort($dirs);
		for($i=0;$i<sizeof($dirs);$i++) {
			if(file_exists(PATH_LOADED_CONTENT."css".DS.$dirs[$i]) && $dirs[$i]!="index.html" && $dirs[$i]!="index.php") {
				include_once(PATH_LOADED_CONTENT."css".DS.$dirs[$i]);
			}
		}
		if(is_array($css)) {
			for($i=0;$i<sizeof($css);$i++) {
				$sRet .= "<link href=\"".$css[$i].AmperOr($css[$i]).time()."\" rel=\"stylesheet\" />\n";
			}
		}
		unset($dirs, $css);
	} else {
		$js = modules::manifest_get(array("create_js", "mini"));
		if($js) {
			$js = implode(",", $js);
		}
		//$sRet = "<script type=\"text/javascript\" src=\"{C_default_http_host}core/class/min/index.php?g=general&charset=".config::Select("charset").(sizeof($js)>0 ? "&f=".implode(",", $js) : "")."&".time()."\"></script>\n";
		for($i=0;$i<sizeof($js);$i++) {
			$sRet = "<script type=\"text/javascript\" src=\"".$js[$i].AmperOr($js[$i]).time()."\"></script>\n";
		}
	}
	$all = modules::manifest_get(array("create_js", "full"));
	if(is_array($all)) {
		$all = array_values($all);
		if($all) {
			for($i=0;$i<sizeof($all);$i++) {
				$sRet .= "<script type=\"text/javascript\" src=\"".$all[$i].AmperOr($all[$i]).time()."\"></script>\n";
			}
		}
	}
	$all = modules::manifest_get(array("create_js", "js"));
	if(is_array($all)) {
		$all = array_values($all);
		if($all) {
			for($i=0;$i<sizeof($all);$i++) {
				$sRet .= "<script type=\"text/javascript\">".$all[$i]."</script>\n";
			}
		}
	}
	if(isset($js_list) && is_array($js_list) && sizeof($js_list)>0) {
		//$sRet .= "<script type=\"text/javascript\" async=\"async\" src=\"{C_default_http_host}core/class/min/index.php?g=general&charset=".config::Select("charset").(sizeof($js_list)>0 ? "&f=".implode(",", $js_list) : "")."&amp;".time()."\"></script>\n";
		for($i=0;$i<sizeof($js_list);$i++) {
			$sRet .= "<script type=\"text/javascript\" src=\"".$js_list[$i].AmperOr($js_list[$i]).time()."\"></script>\n";
		}
	}
	$all = modules::manifest_get(array("create_css", "full"));
	if(is_array($all)) {
		$all = array_values($all);
		if($all) {
			for($i=0;$i<sizeof($all);$i++) {
				$sRet .= "<link href=\"".$all[$i].AmperOr($all[$i]).time()."\" rel=\"stylesheet\" type=\"text/css\" />\n";
			}
		}
	}
	$all = modules::manifest_get(array("create_css", "css"));
	if(is_array($all)) {
		$all = array_values($all);
		if($all) {
			for($i=0;$i<sizeof($all);$i++) {
				$sRet .= "<style type=\"text/css\">".$all[$i]."</style>\n";
			}
		}
	}
	if(isset($_COOKIE[COOK_ADMIN_USER]) && isset($_COOKIE[COOK_ADMIN_PASS]) && userlevel::get("admin") && Arr::get($_GET, "noShowAdmin", false)===false) {
		$sRet .= '<script type="text/javascript" src="{C_default_http_local}skins/admin.min.js"></script>';
	}
	if(sizeof($manifest['jscss'])>0) {
		if(isset($manifest['jscss']['css']) && isset($manifest['jscss']['css']['link']) && is_array($manifest['jscss']['css']['link']) && sizeof($manifest['jscss']['css']['link'])>0) {
			foreach($manifest['jscss']['css']['link'] as $v) {
				$sRet .= "<link href=\"".$v."\" rel=\"stylesheet\" type=\"text/css\">\n";
			}
		}
		if(isset($manifest['jscss']['css']) && isset($manifest['jscss']['css']['full']) && is_array($manifest['jscss']['css']['full']) && sizeof($manifest['jscss']['css']['full'])>0) {
			foreach($manifest['jscss']['css']['full'] as $v) {
				$sRet .= "<style type=\"text/css\">".$v."</style>\n";
			}
		}
		if(isset($manifest['jscss']['js']) && isset($manifest['jscss']['js']['link']) && is_array($manifest['jscss']['js']['link']) && sizeof($manifest['jscss']['js']['link'])>0) {
			foreach($manifest['jscss']['js']['link'] as $v) {
				$sRet .= "<script type=\"text/javascript\" src=\"".$v."\"></script>\n";
			}
		}
		if(isset($manifest['jscss']['js']) && isset($manifest['jscss']['js']['full']) && is_array($manifest['jscss']['js']['full']) && sizeof($manifest['jscss']['js']['full'])>0) {
			foreach($manifest['jscss']['js']['full'] as $v) {
				$sRet .= "<script type=\"text/javascript\">".$v."</script>\n";
			}
		}
	}
	unset($all, $js, $user);
return $sRet;
}

// <meta type="og:image" content="http://site.ru/image.jpg">
// addSeo("image", "http://site.ru/image.jpg");
function addSeo($name, $val, $type = "main") {
global $seoBlock;
	if(!isset($seoBlock[$type]) || !is_array($seoBlock[$type])) {
		$seoBlock[$type] = array();
	}
	$seoBlock[$type][$name] = $val;
}

function releaseSeo($meta = array(), $return = false, $clear = true) {
global $seoBlock;
	$title = (isset($meta['title']) ? $meta['title'] : (isset($seoBlock['ogp']['title']) ? $seoBlock['ogp']['title'] : (isset($seoBlock['og']['title']) ? $seoBlock['og']['title'] : (isset($seoBlock['main']['title']) ? $seoBlock['main']['title'] : "{L_sitename}"))));
	$description = (isset($meta['description']) ? $meta['description'] : (isset($seoBlock['ogp']['description']) ? $seoBlock['ogp']['description'] : (isset($seoBlock['og']['description']) ? $seoBlock['og']['description'] : (isset($seoBlock['main']['description']) ? $seoBlock['main']['description'] : "{L_s_description}"))));
	$imageCheck = (isset($seoBlock['ogp']['image']) && (file_exists(ROOT_PATH.$seoBlock['ogp']['image']) || file_exists($seoBlock['ogp']['image']) || file_exists(config::Select("default_http_host").$seoBlock['ogp']['image']))) || (isset($seoBlock['main']['image_src']) && (file_exists(ROOT_PATH.$seoBlock['main']['image_src']) || file_exists($seoBlock['main']['image_src']) || file_exists(config::Select("default_http_host").$seoBlock['main']['image_src']))) || file_exists(ROOT_PATH."logo.jpg") || file_exists(ROOT_PATH."logo.png");
	$type = (isset($seoBlock['ogp']['type']) ? $seoBlock['ogp']['type'] : (isset($seoBlock['og']['type']) ? $seoBlock['og']['type'] : "website"));
	$link = (isset($meta['canonicalLink']) ? $meta['canonicalLink'] : (isset($meta['link']) ? $meta['link'] : (isset($seoBlock['og']['link']) ? $seoBlock['og']['link'] : (isset($seoBlock['ogp']['link']) ? $seoBlock['ogp']['link'] : (isset($seoBlock['main']['canonical']) ? $seoBlock['main']['canonical'] : (isset($seoBlock['main']['link']) ? $seoBlock['main']['link'] : (isset($seoBlock['main']['url']) ? $seoBlock['main']['url'] : "")))))));
	$keywords = (isset($meta['keywords']) ? $meta['keywords'] : (isset($seoBlock['ogp']['keywords']) ? $seoBlock['ogp']['keywords'] : (isset($seoBlock['og']['keywords']) ? $seoBlock['og']['keywords'] : (isset($seoBlock['main']['keywords']) ? $seoBlock['main']['keywords'] : ""))));
	$robots = (isset($meta['robots']) ? $meta['robots'] : (isset($seoBlock['ogp']['robots']) ? $seoBlock['ogp']['robots'] : (isset($seoBlock['og']['robots']) ? $seoBlock['og']['robots'] : (isset($seoBlock['main']['robots']) ? $seoBlock['main']['robots'] : "all"))));
	if($imageCheck) {
		if(isset($seoBlock['ogp']['image'])) {
			$cLink = substr($seoBlock['ogp']['image'], 0, 1);
			$imageLink = (strpos($cLink, "/")!==false ? "{C_default_http_host}".$seoBlock['ogp']['image'] : (strpos($seoBlock['ogp']['image'], "http")!==false ? $seoBlock['ogp']['image'] : ""));
		} else if(isset($seoBlock['og']['image'])) {
			$cLink = substr($seoBlock['og']['image'], 0, 1);
			$imageLink = (strpos($cLink, "/")!==false ? "{C_default_http_host}".$seoBlock['og']['image'] : (strpos($seoBlock['og']['image'], "http")!==false ? $seoBlock['og']['image'] : ""));
		} else if(isset($seoBlock['main']['image'])) {
			$cLink = substr($seoBlock['main']['image'], 0, 1);
			$imageLink = (strpos($cLink, "/")!==false ? "{C_default_http_host}".$seoBlock['main']['image'] : (strpos($seoBlock['main']['image'], "http")!==false ? $seoBlock['main']['image'] : ""));
		} else if(isset($seoBlock['main']['image_src'])) {
			$cLink = substr($seoBlock['main']['image_src'], 0, 1);
			$imageLink = (strpos($cLink, "/")!==false ? "{C_default_http_host}".$seoBlock['main']['image_src'] : (strpos($seoBlock['main']['image_src'], "http")!==false ? $seoBlock['main']['image_src'] : ""));
		} else if(file_exists(ROOT_PATH."logo.jpg")) {
			$imageLink = "{C_default_http_host}logo.jpg";
		} else if(file_exists(ROOT_PATH."logo.png")) {
			$imageLink = "{C_default_http_host}logo.png";
		} else {
			$imageCheck = false;
		}
	}
	$ogpr = array(
		"og:site_name" => "{L_sitename}",
		"og:url" => "{C_default_http_host}".$link,
		"og:title" => $title,
		"og:description" => $description,
		"og:type" => $type,
	);
	if($imageCheck && !empty($imageLink)) {
		$ogpr = array_merge($ogpr, array(
			"og:image" => $imageLink."?".time(),
		));
	}
	$og = array(
		"title" => $title,
		"description" => $description,
	);
	if($imageCheck && !empty($imageLink)) {
		$og = array_merge($og, array(
			"image" => $imageLink."?".time(),
		));
	}
	$meta = array(
		"og" => $og,
		"ogpr" => $ogpr,
		"title" => $title,
		"robots" => $robots,
		"description" => $description,
	);
	if(!empty($keywords)) {
		$meta = array_merge($meta, array(
			"keywords" => $keywords,
		));
	}
	$meta = array_merge($meta, array(
		"link" => array(
			"canonical" => "{C_default_http_host}".$link,
		),
	));
	if($imageCheck && !empty($imageLink)) {
		$meta = array_merge($meta, array(
			"link" => array(
				"image_src" => $imageLink."?".time(),
			),
		));
	}
	if($clear) {
		unset($seoBlock);
	}
	if($return) {
		return array("meta" => $meta);
	} else {
		templates::change_head(array("meta" => $meta));
	}
}

function createForm($inputs, $to = "", $head = "") {
	$form = "";
	if(!empty($head)) {
		$form .= "<h4>".$head."</h4>";
	}
	$form .= "<form method=\"post\"".(!empty($to) ? " action=\"".$to."\"" : "")." enctype=\"multipart/form-data\">";
	for($i=0;$i<sizeof($inputs);$i++) {
		if(!isset($inputs[$i])) {
			continue;
		}
		$form .= "<div".(isset($inputs[$i]['className']) ? " class=\"".$inputs[$i]['className']."\"" : "").">".(isset($inputs[$i]['name']) ? "<label for=\"input".$i."\">".$inputs[$i]['name']."</label>" : "").(isset($inputs[$i]['html']) && $inputs[$i]['html']=="textarea" ? "<textarea id=\"input".$i."\" name=\"inputData[".$i."]\"".(isset($inputs[$i]['attr']) ? " ".$inputs[$i]['attr'] : "")."></textarea>" : "<input id=\"input".$i."\" type=\"".(isset($inputs[$i]['type']) ? $inputs[$i]['type'] : "text")."\" name=\"inputData[".$i."]\"".(isset($inputs[$i]['placeholder']) ? " placeholder=\"".$inputs[$i]['placeholder']."\"" : "").(isset($inputs[$i]['required']) ? " required=\"required\"" : "")."".(isset($inputs[$i]['attr']) ? " ".$inputs[$i]['attr'] : "").">")."</div>";
	}
	$form .= "<div><input type=\"submit\"".(isset($inputs['submit']['value']) ? " value=\"".$inputs['submit']['value']."\"" : "")."></div>";
	$form .= "</form>";
	return $form;
}

function AmperOr($str) {
	return strpos($str, "?")===false ? "?" : "&";
}

function headers($array = array(), $clear = false, $no_js = false) {
	$header = "";
	if(isset($array['title'])) {
		$header .= "\t<title>".$array['title']."</title>\n";
	} else {
		$header .= "\t<title>{L_sitename}</title>\n";
	}
	$header .= "<meta name=\"generator\" content=\"Cardinal ".VERSION."\" />\n";
	$header .= "<meta name=\"author\" content=\"".(isset($array['author']) ? $array['author'] : "Cardinal ".VERSION)."\" />\n";
	$header .= "<meta name=\"copyright\" content=\"{L_sitename}\" />\n";
	if(!defined("DEVELOPER_MODE") && (!isset($array['meta']) || !array_key_exists("robots", $array['meta']))) {
		$header .= "<meta name=\"robots\" content=\"all\" />\n";
	} elseif(defined("DEVELOPER_MODE")) {
		$header .= "<meta name=\"robots\" content=\"noindex, nofollow\" />\n";
	}
	if(file_exists(ROOT_PATH."favicon.ico")) {
		$header .= "<link href=\"{C_default_http_host}favicon.ico\" rel=\"shortcut icon\" type=\"image/x-icon\" />\n";
		$header .= "<link rel=\"shortcut icon\" type=\"image/vnd.microsoft.icon\" href=\"{C_default_http_host}favicon.ico\" sizes=\"16x16\" />\n";
		$header .= "<link rel=\"icon\" type=\"image/x-icon\" href=\"{C_default_http_host}favicon.ico\" sizes=\"16x16\" />\n";
	}
if(!$clear) {
/*
<!--script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.js"></script><script type='text/javascript' src='http://simplemodal.googlecode.com/files/jquery.simplemodal.1.4.4.min.js'></script><script type="text/javascript" src="http://malsup.github.io/jquery.form.js"></script><script type="text/javascript" src="http://online-killer.com/skins/Kinore/js/jqueryui.js"></script><script type="text/javascript" src="http://online-killer.com/skins/Kinore/js/libs.js"></script><script type="text/javascript" src="http://online-killer.com/skins/Kinore/js/jquery.jmpopups-0.5.1.js"></script><script type="text/javascript" src="http://online-killer.com/skins/Kinore/js/spoiler.js"></script><script type="text/javascript" src="http://online-killer.com/skins/Kinore/js/tabs.js"></script><script type="text/javascript" src="http://online-killer.com/skins/Kinore/js/tabcontent.js"></script><script type="text/javascript" src="http://online-killer.com/skins/Kinore/js/md-socwidget.js"></script><script type="text/javascript">setTimeout(function(){ $('.box').fadeOut('fast') },10000);  //30000 = 30 секунд</script><script type="text/javascript">	var username = "";	var default_link = "http://online-killer.com/";	jQuery(function() {		jQuery('#tabs').tabs('#tabsText > li');	});</script><script type="text/javascript" src="http://online-killer.com/js/poll.core.js"></script><script type="text/javascript">jQuery(document).ready(function(){	loadpoll();});</script><script type="text/javascript" src="http://online-killer.com/js/ajax_core.js"></script><script type="text/javascript" src="http://online-killer.com/flash-js-tagcloud-swfobject.js"></script><meta name="application-name" content="" /><meta name="msapplication-TileColor" content="#e0161d" /><meta name="msapplication-notification" content="frequency=30;polling-uri=http://notifications.buildmypinnedsite.com/?feed=http://online-killer.com/rss.xml&amp;id=1;polling-uri2=http://notifications.buildmypinnedsite.com/?feed=http://online-killer.com/rss.xml&amp;id=2;polling-uri3=http://notifications.buildmypinnedsite.com/?feed=http://online-killer.com/rss.xml&amp;id=3;polling-uri4=http://notifications.buildmypinnedsite.com/?feed=http://online-killer.com/rss.xml&amp;id=4;polling-uri5=http://notifications.buildmypinnedsite.com/?feed=http://online-killer.com/rss.xml&amp;id=5; cycle=1" /-->
*/
	$skin = templates::get_skins();
	$param = array();
	$dprm = Route::param();
	foreach($dprm as $k => $v) {
		$param[] = "\"".$k."\":\"".$v."\"";
	}
	unset($dprm);
	$header .= '<meta name="viewport" content="'.config::Select("viewport").'" />'."\n";
	$header .= '<meta http-equiv="imagetoolbar" content="no" />'."\n";
	$header .= '<meta http-equiv="url" content="{C_default_http_host}">'."\n";
	$header .= '<meta http-equiv="cleartype" content="on">'."\n";
	$header .= '<meta http-equiv="X-UA-Compatible" content="IE=edge">'."\n";
	$header .= (defined("ENABLED_SUPPORTS") ? '<script type="text/javascript" src="{C_default_http_host}js/supports.min.js" async="true"></script>'."\n" : "");
	$header .= '<!-- saved from url=(0014)about:internet -->'."\n";
	$header .= '<meta name="apple-mobile-web-app-capable" content="yes">'."\n";
	$header .= '<meta name="format-detection" content="telephone=no">'."\n";
	$header .= '<meta name="format-detection" content="address=no">'."\n";
	$header .= '<meta name="google" value="notranslate">'."\n";
	$header .= '<meta name="skype_toolbar" content="skype_toolbar_parser_compatible">'."\n";
	$header .= '<meta name="msapplication-tap-highlight" content="no">'."\n";
	$header .= '<meta name="renderer" content="webkit">'."\n";
	$header .= '<meta name="x5-fullscreen" content="true">'."\n";
	$header .= '<meta name="rating" content="General">'."\n";
	$support = lang::support();
	for($i=1;$i<sizeof($support);$i++) {
		$clearLang = nsubstr($support[$i], 4, -3);
		$header .= '<link rel="alternate" href="{C_default_http_host}'.$clearLang.'/" hreflang="'.$clearLang.'">'."\n";
	}
	$header .= "<script type=\"text/javascript\">\n".
		"	var username = \"{U_username}\";\n".
		"	var default_link = \"{C_default_http_host}\";\n".
		"	var tskins = \"".$skin."\";\n".
		"	var SystemTime = \"".time()."\";\n".
		"	var loadedPage = \"".Route::getLoaded()."\";\n".
		"	var loadedParam = {".implode(",", $param)."};\n".
		((file_exists(ROOT_PATH."skins".DS.$skin.DS."skin.css") && Route::Search("css_skin")) ? " var cssRebuildLink = \"{R_[css_skin]}\";\n" : "").
		"</script>\n";
	if(file_exists(ROOT_PATH."skins".DS.$skin.DS."skin.css") && Route::Search("css_skin")) {
		$header .= '<div id="skinRebuilded"><script type="text/javascript" src="{C_default_http_host}js/skins.js" async="true" id="removedSkinRebuilded"></script></div>';
	}
}
	if(isset($array['meta']['canonical'])) {
		$header .= "<link rel=\"canonical\" href=\"".$array['meta']['canonical']."\" />\n";
		unset($array['meta']['canonical']);
	}
	if(!$no_js) {
		$header .= create_js($clear);
	}
	$link_rss = "";
	if(!file_exists(ROOT_PATH."rss.xml")) {
		$rss = Route::Name("rss");
		if($rss) {
			$link = Route::get("rss");
			$link_rss = $link->uri(array());
		}
	} else {
		$rss = true;
	}
/*if(isset($array['title']) && isset($array['meta']['watch']) && $user['id'] == 1) {
	$header .= "<link rel=\"alternate\" type=\"application/json+oembed\" href=\"{C_default_http_host}oembed?url={C_default_http_host}?watch%26v=".$array['meta']['watch']."&format=json\" title=\"".$array['title']."\" />\n";
	$header .= "<link rel=\"alternate\" type=\"text/xml+oembed\" href=\"{C_default_http_host}oembed?url={C_default_http_host}?watch%26v=".$array['meta']['watch']."&type=xml\" title=\"".$array['title']."\" />\n";
}*/

	if($rss && !empty($link_rss)) {
		$header .= "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"{L_sitename}\" href=\"{C_default_http_host}".$link_rss."\" />\n";
		$header .= "<meta name=\"application-name\" content=\"{L_sitename}\" />\n".
			"<meta name=\"msapplication-TileColor\" content=\"#e0161d\"/>\n".
			"<meta name=\"msapplication-notification\" content=\"frequency=30;polling-uri=http://notifications.buildmypinnedsite.com/?feed={C_default_http_host}".$link_rss."&amp;id=1;polling-uri2=http://notifications.buildmypinnedsite.com/?feed={C_default_http_host}".$link_rss."&amp;id=2;polling-uri3=http://notifications.buildmypinnedsite.com/?feed={C_default_http_host}".$link_rss."&amp;id=3;polling-uri4=http://notifications.buildmypinnedsite.com/?feed={C_default_http_host}".$link_rss."&amp;id=4;polling-uri5=http://notifications.buildmypinnedsite.com/?feed={C_default_http_host}".$link_rss."&amp;id=5; cycle=1\"/>\n\n";
	}
	if(isset($array['meta']['type_meta'])) {
		$is_use = true;
		$header .= "<span itemscope itemtype=\"".$array['meta']['type_meta']."\">\n";
		unset($array['meta']['type_meta']);
	} else {
		$is_use = false;
	}

	if(isset($array['meta']['og']) && is_array($array['meta']['og'])) {
		foreach($array['meta']['og'] as $name => $val) {
			$header .= "<meta itemprop=\"".$name."\" content=\"".$val."\" />\n";
		}
	}
	if(isset($array['meta']['ogpr']) && is_array($array['meta']['ogpr'])) {
		foreach($array['meta']['ogpr'] as $name => $val) {
			$header .= "<meta property=\"".$name."\" content=\"".$val."\" />\n";
		}
	}
	if(isset($array['meta']['link']) && is_array($array['meta']['link'])) {
		foreach($array['meta']['link'] as $name => $val) {
			$header .= "<link rel=\"".$name."\" href=\"".$val."\" />\n";
		}
	}
	if(isset($array['meta'])) {
		foreach($array['meta'] as $name => $val) {
			if(is_array($val) || ($name == "robots" && defined("DEVELOPER_MODE"))) {
				continue;
			}
			$header .= "<meta name=\"".$name."\" content=\"".$val."\" />\n";
		}
	}
	if($is_use) {
		$header .= "</span>";
	}
	if(isset($_COOKIE[COOK_ADMIN_USER]) && isset($_COOKIE[COOK_ADMIN_PASS]) && userlevel::get("admin") && Arr::get($_GET, "noShowAdmin", false)===false) {
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
		adminPanelVsort($links);
		$level = User::get("level");
		$menu = "";
		$newMenu = array();
		foreach($links as $name => $datas) {
			for($i=0;$i<sizeof($datas);$i++) {
				for($is=0;$is<sizeof($datas[$i]);$is++) {
					if(isset($datas[$i][$is]['access']) && $datas[$i][$is]['access']!=$level) {
						break;
					}
					if($datas[$i][$is]['type']=="cat") {
						$newMenu[$name] = $datas[$i][$is];
					} elseif(isset($newMenu[$name]) && $newMenu[$name]['link']!=$datas[$i][$is]['link']) {
						$newMenu[$name]['items'][$datas[$i][$is]['link']] = $datas[$i][$is];
					}
				}
			}
		}
		$editPage = "";
		$editor = modules::manifest_get("editor");
		if($editor!==false && Arr::get($editor, "class", false)) {
			$editPage = "{C_default_http_local}{D_ADMINCP_DIRECTORY}/?pages=".$editor['class'].(isset($editor['page']) ? "&".$editor['page'] : "");
		}
		$header .= "<link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css\"><link rel=\"stylesheet\" href=\"{C_default_http_local}skins/admin.{S_time}.css\">";
		$menu = "<div class=\"adminCoreCardinal\"><a href=\"{C_default_http_local}\" class=\"logo\"></a><a href=\"{C_default_http_local}{D_ADMINCP_DIRECTORY}/\" class=\"linkToAdmin\">{L_'adminpanel'}</a>".(!empty($editPage) ? "<div class=\"items\"><a href=\"".$editPage."\"><i class=\"fa-edit\"></i><span>{L_'Редактировать'}</span></a></div>":"").menuAdminHeader($newMenu)."<div class=\"user\"><span>{U_username}</span><div class=\"dropped\"><a href=\"{C_default_http_local}{D_ADMINCP_DIRECTORY}/?pages=Login&out\"><i class=\"fa-user-times\"></i>{L_'logout'}</a></div></div></div>";
		cardinalEvent::setData($menu);
		cardinalEvent::addListener("templates::display", "addAdminPanelToPage");
	}
	unset($array);
return $header;
}

function addAdminPanelToPage($page, $data) {
	if(defined("ADMINCP_POSITION_BOTTOM")) {
		$data = preg_replace("#<body(.*?)>#", "<body$1 data-body=\"bottom\">", $data);
		$data = str_replace("adminCoreCardinal", "adminCoreCardinal bottom", $data);
	}
	if(preg_match('#<body(.*?)>#i', $data)) {
		$data = preg_replace_callback('#<body(.*?)>#i', "callBackAdminPanelToPage", $data);
	} elseif(preg_match('/<body(.*?)>/', $data)) {
		$data = preg_replace('/<body(.*?)>/', '<body$1class="adminbarCardinal">', $data);
	} else {
		$data = str_replace('<body>', '<body class="adminbarCardinal">', $data);
	}
	$data = preg_replace("#<body(.*?)>#i", "<body$1 data-body=\"top\">", $data);
	$data = str_replace("</body>", templates::view($page)."</body>", $data);
	return $data;
}

function callBackAdminPanelToPage($arr) {
	$ret = $arr[0];
	if(isset($arr[1])) {
		$or = $arr[1];
		if(preg_match('#class=[\'"].+?[\'"]#', $arr[1], $match)) {
			$arr[1] = preg_replace('#class=([\'"])(.+?)([\'"])#', "class=$1$2 adminbarCardinal$3", $arr[1]);
		} else {
			$arr[1] .= " class=\"adminbarCardinal\"";
		}
		$ret = str_replace($or, $arr[1], $arr[0]);
	}
	return $ret;
}

// regCssJs("{THEME}/jquery.js", "js", true)
function regCssJs($js, $type, $mark = false, $name = "") {
global $manifest;
	if(is_array($js)) {
		foreach($js as $v) {
			regCssJs($v, $type, $mark, $name);
		}
	} else if(is_string($js)) {
		if(!isset($manifest['jscss'][$type])) {
			$manifest['jscss'][$type] = array();
		}
		if(!isset($manifest['jscss'][$type]['link'])) {
			$manifest['jscss'][$type]['link'] = array();
		}
		if(!isset($manifest['jscss'][$type]['full'])) {
			$manifest['jscss'][$type]['full'] = array();
		}
		$jsCheck = parse_url($js);
		if(!empty($name)) {
			if(isset($jsCheck['path'])) {
				$manifest['jscss'][$type]['link'][$name] = $js.($mark ? AmperOr($js).time() : "");
			} else {
				$manifest['jscss'][$type]['full'][$name] = $js.($mark ? AmperOr($js).time() : "");
			}
		} else {
			if(isset($jsCheck['path'])) {
				$manifest['jscss'][$type]['link'][] = $js.($mark ? AmperOr($js).time() : "");
			} else {
				$manifest['jscss'][$type]['full'][] = $js.($mark ? AmperOr($js).time() : "");
			}
		}
	}
}

function unRegCssJs($js, $type, $mark = false, $name = "") {
global $manifest;
	if(is_array($js)) {
		foreach($js as $v) {
			unRegCssJs($v, $type, $mark, $name);
		}
	} else if(is_string($js)) {
		$jsCheck = parse_url($js);
		if(!empty($name)) {
			if(isset($jsCheck['path']) && isset($manifest['jscss'][$type]['link'][$name])) {
				unset($manifest['jscss'][$type]['link'][$name]);
			} else if(isset($manifest['jscss'][$type]['full'][$name])) {
				unset($manifest['jscss'][$type]['full'][$name]);
			}
		} else {
			if(isset($jsCheck['path']) && isset($manifest['jscss'][$type]['link']) && is_array($manifest['jscss'][$type]['link']) && sizeof($manifest['jscss'][$type]['link'])>0) {
				for($i=0;$i<sizeof($manifest['jscss'][$type]['link']);$i++) {
					if(strpos($manifest['jscss'][$type]['link'][$i], $js)!==false) {
						unset($manifest['jscss'][$type]['link'][$i]);
					}
				}
			} else if(isset($manifest['jscss'][$type]['full']) && is_array($manifest['jscss'][$type]['full']) && sizeof($manifest['jscss'][$type]['full'])>0) {
				for($i=0;$i<sizeof($manifest['jscss'][$type]['full']);$i++) {
					if(strpos($manifest['jscss'][$type]['full'][$i], $js)!==false) {
						unset($manifest['jscss'][$type]['full'][$i]);
					}
				}
			}
		}
	}
}

function adminPanelVsort(&$array) {
	$arrs = array();
	foreach($array as $key => $val) {
		sort($val);
		$arrs[$key] = $val;
	}
	$array = $arrs;
}

function menuAdminHeader($arr, $isCat = false) {
	$menu = "";
	foreach($arr as $v) {
		$cat = false;
		if(isset($v['items'])) {
			$cat = true;
		}
		$menu .= (!$isCat ? "<div class=\"items\">" : "")."<a href=\"".$v['link']."\"".($cat ? " class=\"subItem\"": "").">".(isset($v['icon']) && !empty($v['icon']) ? "<i class=\"".$v['icon']."\"></i>" : "")."<span>".$v['title']."</span></a>";
		$menu .= ($cat ? "<div class=\"dropped\">" : "");
		if($cat) {
			$menu .= menuAdminHeader($v['items'], true);
		}
		$menu .= ($cat ? "</div>" : "").(!$isCat ? "</div>\n" : "");
	}
	return $menu;
}

function ajax_check() {
	if(HTTP::getServer('HTTP_X_REQUESTED_WITH') && HTTP::getServer('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest') {
		return "ajax";
	} else {
		return "html";
	}
}

?>