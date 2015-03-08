<?php
/*
*
* Version Engine: 1.25.3
* Version File: 21
*
* 21.1
* add var for js detected system time in unix format
*
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die;
}

function meta($array=array()) {
global $templates, $user;
	$templates->assign_vars(array(
		"url" => "{C_default_http_host}",
		"title" => "{L_site_name}",
	), "meta", "1");
	if(sizeof($array)>0) {
		for($i=0;$i<sizeof($array);$i++) {
			$templates->assign_vars(array(
				"url" => $array[$i]['url'],
				"title" => $array[$i]['title'],
			), "meta", ($i+3));
		}
	}
	$te = $templates->lcud($templates->complited_assing_vars("meta", null));
	unset($templates);
return $te;
}

function create_js($clear = false) {
global $user, $config;
	if(!$config["js_min"]) {
		if(!$clear) {
			$js = array();
			if(isset($user) && $user['id']==1) {
				$js[] = 'http://ie.microsoft.com/testdrive/HTML5/CompatInspector/inspector.js';
			}
			$js[] = 'http://code.jquery.com/jquery-2.1.0.min.js';
			$js[] = 'http://code.jquery.com/jquery-migrate-1.2.1.min.js';
			$js[] = 'http://malsup.github.io/jquery.form.js';
			$js[] = 'http://code.jquery.com/ui/1.10.4/jquery-ui.min.js';
			$js[] = '{THEME}/js/jquery.jmpopups-0.5.1.js';
			$js[] = '/js/init.js';
			$js[] = '{C_default_http_host}js/ajax_core.js';
		}
		$js[] = '/flash-js-tagcloud-swfobject.js';
		$sRet = "";
		for($i=0;$i<sizeof($js);$i++) {
			$sRet .= "<script type=\"text/javascript\" src=\"".$js[$i]."\"></script>\n";
		}
	} else {
		$js = modules::manifest_get(array("create_js", "mini"));
		if($js) {
			$js = implode(",", $js);
		}
		if(isset($user['id']) && $user['id']==1) {
			//$sRet = "<script type=\"text/javascript\" src=\"{C_default_http_host}min/index.php?charset=".$config['charset']."&amp;f=/js/inspector.js".($js ? ",".$js : "")."&g=general&13\"></script>\n";
		} else {
			//$sRet = "<script type=\"text/javascript\" src=\"{C_default_http_host}min/index.php?charset=".$config['charset'].($js ? "&amp;f=".$js : "")."&amp;g=general&13\"></script>\n";
		}
		$sRet = "<script type=\"text/javascript\" src=\"{C_default_http_host}js/require.js?".time()."\"></script>\n<script type=\"text/javascript\" src=\"{C_default_http_host}js/config.js?".time()."\"></script>";
	}
	$all = modules::manifest_get(array("create_js", "js"));
	if($all) {
		for($i=0;$i<sizeof($all);$i++) {
			$sRet .= "<script type=\"text/javascript\">".$all[$i]."</script>\n";
		}
	}
	$all = modules::manifest_get(array("create_js", "full"));
	if($all) {
		for($i=0;$i<sizeof($all);$i++) {
			$sRet .= "<script type=\"text/javascript\" src=\"".$all[$i]."\"></script>\n";
		}
	}
	$lang = $config['lang'];
	$sRet .= "<script type=\"text/javascript\" src=\"{C_default_http_host}js/lang/".$lang.".js\"></script>\n";
	unset($config, $lang, $all, $js, $user);
return $sRet;
}

function headers($array = array(), $clear = false) {
global $user, $templates;
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
<!--script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.js"></script><script type='text/javascript' src='http://simplemodal.googlecode.com/files/jquery.simplemodal.1.4.4.min.js'></script><script type="text/javascript" src="http://malsup.github.io/jquery.form.js"></script><script type="text/javascript" src="http://online-killer.com/skins/Kinore/js/jqueryui.js"></script><script type="text/javascript" src="http://online-killer.com/skins/Kinore/js/libs.js"></script><script type="text/javascript" src="http://online-killer.com/skins/Kinore/js/jquery.jmpopups-0.5.1.js"></script><script type="text/javascript" src="http://online-killer.com/skins/Kinore/js/spoiler.js"></script><script type="text/javascript" src="http://online-killer.com/skins/Kinore/js/tabs.js"></script><script type="text/javascript" src="http://online-killer.com/skins/Kinore/js/tabcontent.js"></script><script type="text/javascript" src="http://online-killer.com/skins/Kinore/js/md-socwidget.js"></script><script type="text/javascript">setTimeout(function(){ $('.box').fadeOut('fast') },10000);  //30000 = 30 СЃРµРєСѓРЅРґ</script><script type="text/javascript">	var username = "";	var default_link = "http://online-killer.com/";	jQuery(function() {		jQuery('#tabs').tabs('#tabsText > li');	});</script><script type="text/javascript" src="http://online-killer.com/js/poll.core.js"></script><script type="text/javascript">jQuery(document).ready(function(){	loadpoll();});</script><script type="text/javascript" src="http://online-killer.com/js/ajax_core.js"></script><script type="text/javascript" src="http://online-killer.com/flash-js-tagcloud-swfobject.js"></script><meta name="application-name" content="" /><meta name="msapplication-TileColor" content="#e0161d" /><meta name="msapplication-notification" content="frequency=30;polling-uri=http://notifications.buildmypinnedsite.com/?feed=http://online-killer.com/rss.xml&amp;id=1;polling-uri2=http://notifications.buildmypinnedsite.com/?feed=http://online-killer.com/rss.xml&amp;id=2;polling-uri3=http://notifications.buildmypinnedsite.com/?feed=http://online-killer.com/rss.xml&amp;id=3;polling-uri4=http://notifications.buildmypinnedsite.com/?feed=http://online-killer.com/rss.xml&amp;id=4;polling-uri5=http://notifications.buildmypinnedsite.com/?feed=http://online-killer.com/rss.xml&amp;id=5; cycle=1" /-->
*/
	$header .= "<link href='/js/nprogress.css' rel='stylesheet' type='text/css'/>\n";
	$header .= "<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:200,400,700,400italic' rel='stylesheet' type='text/css'/>\n";

	$header .= '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=1" />'."\n";
	$header .= "<script type=\"text/javascript\">\n".
		"	var username = \"".(isset($user['username']) ? $user['username'] : "")."\";\n".
		"	var default_link = \"{C_default_http_host}\";\n".
		"	var tskins = \"".$templates->get_skins()."\";\n".
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
	if(isset($array['meta'])) {
		foreach($array['meta'] as $name => $val) {
			if(is_array($val)) continue;
			$header .= "<meta name=\"".$name."\" content=\"".$val."\" />\n";
		}
	}
	if($is_use) {
		$header .= "</span>";
	}
	unset($array, $templates);
/*
<script type="text/javascript">
    var reformalOptions = {
        project_id: 91518,
        project_host: "idea.online-killer.com",
        tab_orientation: "left",
        tab_indent: "50%",
        tab_bg_color: "#d9ae03",
        tab_border_color: "#4646e3",
        tab_image_url: "http://tab.reformal.ru/T9GC0LfRi9Cy0Ysg0Lgg0L%252FRgNC10LTQu9C%252B0LbQtdC90LjRjw==/4646e3/401bacf84347cd4a664b570651db5d86/left/0/tab.png",
        tab_border_width: 1
    };
    
    (function() {
        var script = document.createElement('script');
        script.type = 'text/javascript'; script.async = true;
        script.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'media.reformal.ru/widgets/v3/reformal.js';
        document.getElementsByTagName('head')[0].appendChild(script);
    })();
</script><noscript><a href="http://reformal.ru"><img src="http://media.reformal.ru/reformal.png" /></a><a href="http://idea.online-killer.com">Oтзывы и предложения для Онлайн видео</a></noscript>
*/
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