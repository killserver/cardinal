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
global $user;
	$css = $js = array();
	$sRet = "";
	$js_list = modules::manifest_get(array("create_js", "min"));
	if(!config::Select("js_min")) {
		if(!$clear) {
			if(isset($user) && isset($user['id']) && $user['id']==1) {
				$js[] = 'http://ie.microsoft.com/testdrive/HTML5/CompatInspector/inspector.js';
			}
		}
		$dirs = read_dir(ROOT_PATH."core".DS."modules".DS."js".DS, ".".ROOT_EX);
		sort($dirs);
		for($i=0;$i<sizeof($dirs);$i++) {
			if(file_exists(ROOT_PATH."core".DS."modules".DS."js".DS.$dirs[$i])) {
				include_once(ROOT_PATH."core".DS."modules".DS."js".DS.$dirs[$i]);
			}
		}
		if(is_array($js)) {
			for($i=0;$i<sizeof($js);$i++) {
				$sRet .= "<script type=\"text/javascript\" src=\"".$js[$i].AmperOr($js[$i]).time()."\"></script>\n";
			}
		}
		unset($dirs, $js);
		$dirs = read_dir(ROOT_PATH."core".DS."modules".DS."css".DS, ".".ROOT_EX);
		sort($dirs);
		for($i=0;$i<sizeof($dirs);$i++) {
			if(file_exists(ROOT_PATH."core".DS."modules".DS."css".DS.$dirs[$i])) {
				include_once(ROOT_PATH."core".DS."modules".DS."css".DS.$dirs[$i]);
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
		$sRet = "<script type=\"text/javascript\" src=\"{C_default_http_host}core/class/min/index.php?g=general&amp;charset=".config::Select("charset").(sizeof($js)>0 ? "&amp;f=".implode(",", $js) : "")."&amp;".time()."\"></script>\n";
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
		$sRet .= "<script type=\"text/javascript\" async src=\"{C_default_http_host}core/class/min/index.php?g=general&amp;charset=".config::Select("charset").(sizeof($js_list)>0 ? "&amp;f=".implode(",", $js_list) : "")."&amp;".time()."\"></script>\n";
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
	unset($all, $js, $user);
return $sRet;
}

function createForm($inputs, $to = "", $head = "") {
	$form = "";
	if(!empty($head)) {
		$form .= "<h4>".$head."</h4>";
	}
	$form .= "<form method=\"post\"".(!empty($to) ? " action=\"".$to."\"" : "")." enctype=\"multipart/form-data\">";
	for($i=0;$i<sizeof($inputs);$i++) {
		$form .= "<div><label for=\"input".$i."\">".$inputs[$i]['name']."</label>".(isset($inputs[$i]['html']) && $inputs[$i]['html']=="textarea" ? "<textarea id=\"input".$i."\" name=\"inputData[".$i."]\"></textarea>" : "<input id=\"input".$i."\" type=\"".(isset($inputs[$i]['type']) ? $inputs[$i]['type'] : "text")."\" name=\"inputData[".$i."]\"".(isset($inputs[$i]['placeholder']) ? " placeholder=\"".$inputs[$i]['placeholder']."\"" : "").">")."</div>";
	}
	$form .= "<div><input type=\"submit\"></div>";
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
	if(isset($array['meta']) && array_key_exists("robots", $array['meta'])) {
		$header .= "<meta name=\"robots\" content=\"all\" />\n";
	}
	if(file_exists(ROOT_PATH."favicon.ico")) {
		$header .= "<link href=\"{C_default_http_host}favicon.ico\" rel=\"shortcut icon\" type=\"image/x-icon\" />\n";
		$header .= "<link rel=\"icon shortcut\" type=\"image/vnd.microsoft.icon\" href=\"{C_default_http_host}favicon.ico\" sizes=\"16x16\" />\n";
		$header .= "<link rel=\"icon\" type=\"image/x-icon\" href=\"{C_default_http_host}favicon.ico\" sizes=\"16x16\" />\n";
	}
if(!$clear) {
/*
<!--script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.js"></script><script type='text/javascript' src='http://simplemodal.googlecode.com/files/jquery.simplemodal.1.4.4.min.js'></script><script type="text/javascript" src="http://malsup.github.io/jquery.form.js"></script><script type="text/javascript" src="http://online-killer.com/skins/Kinore/js/jqueryui.js"></script><script type="text/javascript" src="http://online-killer.com/skins/Kinore/js/libs.js"></script><script type="text/javascript" src="http://online-killer.com/skins/Kinore/js/jquery.jmpopups-0.5.1.js"></script><script type="text/javascript" src="http://online-killer.com/skins/Kinore/js/spoiler.js"></script><script type="text/javascript" src="http://online-killer.com/skins/Kinore/js/tabs.js"></script><script type="text/javascript" src="http://online-killer.com/skins/Kinore/js/tabcontent.js"></script><script type="text/javascript" src="http://online-killer.com/skins/Kinore/js/md-socwidget.js"></script><script type="text/javascript">setTimeout(function(){ $('.box').fadeOut('fast') },10000);  //30000 = 30 секунд</script><script type="text/javascript">	var username = "";	var default_link = "http://online-killer.com/";	jQuery(function() {		jQuery('#tabs').tabs('#tabsText > li');	});</script><script type="text/javascript" src="http://online-killer.com/js/poll.core.js"></script><script type="text/javascript">jQuery(document).ready(function(){	loadpoll();});</script><script type="text/javascript" src="http://online-killer.com/js/ajax_core.js"></script><script type="text/javascript" src="http://online-killer.com/flash-js-tagcloud-swfobject.js"></script><meta name="application-name" content="" /><meta name="msapplication-TileColor" content="#e0161d" /><meta name="msapplication-notification" content="frequency=30;polling-uri=http://notifications.buildmypinnedsite.com/?feed=http://online-killer.com/rss.xml&amp;id=1;polling-uri2=http://notifications.buildmypinnedsite.com/?feed=http://online-killer.com/rss.xml&amp;id=2;polling-uri3=http://notifications.buildmypinnedsite.com/?feed=http://online-killer.com/rss.xml&amp;id=3;polling-uri4=http://notifications.buildmypinnedsite.com/?feed=http://online-killer.com/rss.xml&amp;id=4;polling-uri5=http://notifications.buildmypinnedsite.com/?feed=http://online-killer.com/rss.xml&amp;id=5; cycle=1" /-->
*/
	$skin = templates::get_skins();
	$header .= '<meta name="viewport" content="'.config::Select("viewport").'" />'."\n";
	$header .= '<meta http-equiv="imagetoolbar" content="no" />'."\n";
	$header .= '<!-- saved from url=(0014)about:internet -->'."\n";
	$header .= '<meta name="apple-mobile-web-app-capable" content="yes">'."\n";
	$header .= '<meta http-equiv="cleartype" content="on">'."\n";
	$header .= "<script type=\"text/javascript\">\n".
		"	var username = \"{U_username}\";\n".
		"	var default_link = \"{C_default_http_host}\";\n".
		"	var tskins = \"".$skin."\";\n".
		"	var SystemTime = \"".time()."\";\n".
		(file_exists(ROOT_PATH."skins".DS.$skin.DS."skin.css" && !(!Route::Name("css_skin"))) ? "	var cssRebuildLink = \"{R_[css_skin]}\";\n" : "").
		"</script>\n";
	if(file_exists(ROOT_PATH."skins".DS.$skin.DS."skin.css" && !(!Route::Name("css_skin"))) {
		$header .= '<div id="skinRebuilded"><script type="text/javascript" src="{C_default_http_host}js/skins.js" id="removedSkinRebuilded"></script></div>';
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
	if($rss && !empty($link_rss)) {
		$header .= "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"{L_sitename}\" href=\"{C_default_http_host}".$link_rss."\" />\n";
	}
/*if(isset($array['title']) && isset($array['meta']['watch']) && $user['id'] == 1) {
	$header .= "<link rel=\"alternate\" type=\"application/json+oembed\" href=\"{C_default_http_host}oembed?url={C_default_http_host}?watch%26v=".$array['meta']['watch']."&format=json\" title=\"".$array['title']."\" />\n";
	$header .= "<link rel=\"alternate\" type=\"text/xml+oembed\" href=\"{C_default_http_host}oembed?url={C_default_http_host}?watch%26v=".$array['meta']['watch']."&type=xml\" title=\"".$array['title']."\" />\n";
}*/
	$header .= modules::use_modules("watch", $array);

	if($rss && !empty($link_rss)) {
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
			if(is_array($val)) continue;
			$header .= "<meta name=\"".$name."\" content=\"".$val."\" />\n";
		}
	}
	if($is_use) {
		$header .= "</span>";
	}
	unset($array);
return $header;
}

function ajax_check() {
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
		return "ajax";
	} else {
		return "html";
	}
}

?>