<?php
/*
 *
 * @version 1.25.6-rc5
 * @copyright 2014-2015 KilleR for Cardinal Engine
 *
 * Version Engine: 1.25.6-rc5
 * Version File: 21
 *
 * 21.1
 * add var for js detected system time in unix format
 * 22.1
 * add css and rebuild js data to template
 * 22.2
 * rebuild local data
 * 22.3
 * fix and clear include modules js and css files
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

function meta($array=array()) {
	templates::assign_vars(array(
		"url" => "{C_default_http_host}",
		"title" => "{L_site_name}",
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
	$te = templates::lcud(templates::complited_assing_vars("meta", null));
return $te;
}

function create_js($clear = false) {
global $user;
	$css = $js = array();
	$sRet = "";
	if(!config::Select("js_min")) {
		if(!$clear) {
			if(isset($user) && isset($user['id']) && $user['id']==1) {
				$js[] = 'http://ie.microsoft.com/testdrive/HTML5/CompatInspector/inspector.js';
			}
			/*$js[] = 'http://code.jquery.com/jquery-2.1.0.min.js';
			$js[] = 'http://code.jquery.com/jquery-migrate-1.2.1.min.js';
			$js[] = 'http://malsup.github.io/jquery.form.js';
			$js[] = 'http://code.jquery.com/ui/1.10.4/jquery-ui.min.js';
			$js[] = '{THEME}/js/jquery.jmpopups-0.5.1.js';
			$js[] = '/js/init.js';
			$js[] = '{C_default_http_host}js/ajax_core.js';*/
		}
		$dirs = read_dir(ROOT_PATH."core/modules/js/", ".php");
		sort($dirs);
		for($i=0;$i<sizeof($dirs);$i++) {
			if(file_exists(ROOT_PATH."core/modules/js/".$dirs[$i])) {
				include_once(ROOT_PATH."core/modules/js/".$dirs[$i]);
			}
		}
		if(is_array($js)) {
			for($i=0;$i<sizeof($js);$i++) {
				$sRet .= "<script type=\"text/javascript\" src=\"".$js[$i]."\"></script>\n";
			}
		}
		unset($dirs, $js);
		$dirs = read_dir(ROOT_PATH."core/modules/css/", ".php");
		sort($dirs);
		for($i=0;$i<sizeof($dirs);$i++) {
			if(file_exists(ROOT_PATH."core/modules/css/".$dirs[$i])) {
				include_once(ROOT_PATH."core/modules/css/".$dirs[$i]);
			}
		}
		if(is_array($css)) {
			for($i=0;$i<sizeof($css);$i++) {
				$sRet .= "<link href=\"".$css[$i]."\" rel=\"stylesheet\" />\n";
			}
		}
		unset($dirs, $css);
	} else {
		$js = modules::manifest_get(array("create_js", "mini"));
		if($js) {
			$js = implode(",", $js);
		}
		$sRet = "<script type=\"text/javascript\" src=\"{C_default_http_host}js/require.js?".time()."\"></script>\n<script type=\"text/javascript\" src=\"{C_default_http_host}js/config.js?".time()."\"></script>";
	}
	$all = modules::manifest_get(array("create_js", "full"));
	if(is_array($all)) {
		$all = array_values($all);
		if($all) {
			for($i=0;$i<sizeof($all);$i++) {
				$sRet .= "<script type=\"text/javascript\" src=\"".$all[$i]."\"></script>\n";
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
	$all = modules::manifest_get(array("create_css", "full"));
	if(is_array($all)) {
		$all = array_values($all);
		if($all) {
			for($i=0;$i<sizeof($all);$i++) {
				$sRet .= "<link href=\"".$all[$i]."\" rel=\"stylesheet\" type=\"text/css\" />\n";
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

function headers($array = array(), $clear = false) {
global $user;
	$header = "";
	if(isset($array['title'])) {
		$header .= "\t<title>".$array['title']."</title>\n";
	} else {
		$header .= "\t<title>{L_sitename}</title>\n";
	}
	$header .= "<meta name=\"generator\" content=\"Cardinal ".VERSION."\" />\n";
	//$header .= "<meta name=\"robots\" content=\"noindex, nofollow\"/>\n";
	$header .= "<link href=\"{C_default_http_host}favicon.ico\" rel=\"shortcut icon\" type=\"image/x-icon\" />\n";
	$header .= "<link rel=\"icon shortcut\" type=\"image/vnd.microsoft.icon\" href=\"{C_default_http_host}favicon.ico\" sizes=\"16x16\" />\n";
	$header .= "<link rel=\"icon\" type=\"image/x-icon\" href=\"{C_default_http_host}favicon.ico\" sizes=\"16x16\" />\n";
if(!$clear) {
	if(!defined("MOBILE")) {
		if(isset($array['user_row'])) {
			$header .= "<link href='/?css&user=".$array['user_row']."' rel='stylesheet' type='text/css'/>\n";
		} else {
			$header .= "<link href='/?css' rel='stylesheet' type='text/css'/>\n";
		}
	}
/*
<!--script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.js"></script><script type='text/javascript' src='http://simplemodal.googlecode.com/files/jquery.simplemodal.1.4.4.min.js'></script><script type="text/javascript" src="http://malsup.github.io/jquery.form.js"></script><script type="text/javascript" src="http://online-killer.com/skins/Kinore/js/jqueryui.js"></script><script type="text/javascript" src="http://online-killer.com/skins/Kinore/js/libs.js"></script><script type="text/javascript" src="http://online-killer.com/skins/Kinore/js/jquery.jmpopups-0.5.1.js"></script><script type="text/javascript" src="http://online-killer.com/skins/Kinore/js/spoiler.js"></script><script type="text/javascript" src="http://online-killer.com/skins/Kinore/js/tabs.js"></script><script type="text/javascript" src="http://online-killer.com/skins/Kinore/js/tabcontent.js"></script><script type="text/javascript" src="http://online-killer.com/skins/Kinore/js/md-socwidget.js"></script><script type="text/javascript">setTimeout(function(){ $('.box').fadeOut('fast') },10000);  //30000 = 30 секунд</script><script type="text/javascript">	var username = "";	var default_link = "http://online-killer.com/";	jQuery(function() {		jQuery('#tabs').tabs('#tabsText > li');	});</script><script type="text/javascript" src="http://online-killer.com/js/poll.core.js"></script><script type="text/javascript">jQuery(document).ready(function(){	loadpoll();});</script><script type="text/javascript" src="http://online-killer.com/js/ajax_core.js"></script><script type="text/javascript" src="http://online-killer.com/flash-js-tagcloud-swfobject.js"></script><meta name="application-name" content="" /><meta name="msapplication-TileColor" content="#e0161d" /><meta name="msapplication-notification" content="frequency=30;polling-uri=http://notifications.buildmypinnedsite.com/?feed=http://online-killer.com/rss.xml&amp;id=1;polling-uri2=http://notifications.buildmypinnedsite.com/?feed=http://online-killer.com/rss.xml&amp;id=2;polling-uri3=http://notifications.buildmypinnedsite.com/?feed=http://online-killer.com/rss.xml&amp;id=3;polling-uri4=http://notifications.buildmypinnedsite.com/?feed=http://online-killer.com/rss.xml&amp;id=4;polling-uri5=http://notifications.buildmypinnedsite.com/?feed=http://online-killer.com/rss.xml&amp;id=5; cycle=1" /-->
*/
	$header .= "<link href='/js/nprogress.css' rel='stylesheet' type='text/css'/>\n";
	$header .= "<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:200,400,700,400italic' rel='stylesheet' type='text/css'/>\n";

	$header .= '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=1" />'."\n";
	$header .= "<script type=\"text/javascript\">\n".
		"	var username = \"{U_username}\";\n".
		"	var default_link = \"{C_default_http_host}\";\n".
		"	var tskins = \"".templates::get_skins()."\";\n".
		"	var SystemTime = \"".time()."\";\n".
		"</script>\n";
}
	if(isset($array['meta']['canonical'])) {
		$header .= "<link rel=\"canonical\" href=\"".$array['meta']['canonical']."\" />\n";
		unset($array['meta']['canonical']);
	}
	$header .= create_js($clear);
	$header .= "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"{L_sitename}\" href=\"{C_default_http_host}rss.xml\" />\n";
/*if(isset($array['title']) && isset($array['meta']['watch']) && $user['id'] == 1) {
	$header .= "<link rel=\"alternate\" type=\"application/json+oembed\" href=\"{C_default_http_host}oembed?url={C_default_http_host}?watch%26v=".$array['meta']['watch']."&format=json\" title=\"".$array['title']."\" />\n";
	$header .= "<link rel=\"alternate\" type=\"text/xml+oembed\" href=\"{C_default_http_host}oembed?url={C_default_http_host}?watch%26v=".$array['meta']['watch']."&type=xml\" title=\"".$array['title']."\" />\n";
}*/
	$header .= modules::use_modules("watch", $array);

	$header .= "<meta name=\"application-name\" content=\"{L_sitename}\" />\n".
		"<meta name=\"msapplication-TileColor\" content=\"#e0161d\"/>\n".
		"<meta name=\"msapplication-notification\" content=\"frequency=30;polling-uri=http://notifications.buildmypinnedsite.com/?feed={C_default_http_host}rss.xml&amp;id=1;polling-uri2=http://notifications.buildmypinnedsite.com/?feed={C_default_http_host}rss.xml&amp;id=2;polling-uri3=http://notifications.buildmypinnedsite.com/?feed={C_default_http_host}rss.xml&amp;id=3;polling-uri4=http://notifications.buildmypinnedsite.com/?feed={C_default_http_host}rss.xml&amp;id=4;polling-uri5=http://notifications.buildmypinnedsite.com/?feed={C_default_http_host}rss.xml&amp;id=5; cycle=1\"/>\n\n";

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
//	$header = str_replace("\n", "", $header);
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