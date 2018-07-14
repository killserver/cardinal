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
	$te = templates::lcud(templates::completed_assign_vars("meta", "core"));
return $te;
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
	$title = (isset($meta['title']) ? $meta['title'] : (isset($seoBlock['ogp']['title']) ? $seoBlock['ogp']['title'] : (isset($seoBlock['og']['title']) ? $seoBlock['og']['title'] : (isset($seoBlock['main']['title']) ? $seoBlock['main']['title'] : lang::get_lang("sitename")))));
	$description = (isset($meta['description']) ? $meta['description'] : (isset($seoBlock['ogp']['description']) ? $seoBlock['ogp']['description'] : (isset($seoBlock['og']['description']) ? $seoBlock['og']['description'] : (isset($seoBlock['main']['description']) ? $seoBlock['main']['description'] : lang::get_lang("s_description")))));
	$imageCheck = (isset($seoBlock['ogp']['image']) && (file_exists(ROOT_PATH.$seoBlock['ogp']['image']) || file_exists($seoBlock['ogp']['image']) || file_exists(config::Select("default_http_host").$seoBlock['ogp']['image']))) || (isset($seoBlock['main']['image_src']) && (file_exists(ROOT_PATH.$seoBlock['main']['image_src']) || file_exists($seoBlock['main']['image_src']) || file_exists(config::Select("default_http_host").$seoBlock['main']['image_src']))) || file_exists(ROOT_PATH."logo.jpg") || file_exists(ROOT_PATH."logo.jpeg") || file_exists(ROOT_PATH."logo.png") || file_exists(ROOT_PATH."uploads".DS."logo-for-site.jpg") || file_exists(ROOT_PATH."uploads".DS."logo-for-site.jpeg") || file_exists(ROOT_PATH."uploads".DS."logo-for-site.png");
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
		} else if(file_exists(ROOT_PATH."uploads".DS."logo-for-site.jpg")) {
			$imageLink = "{C_default_http_host}uploads/logo-for-site.jpg";
		} else if(file_exists(ROOT_PATH."uploads".DS."logo-for-site.jpeg")) {
			$imageLink = "{C_default_http_host}uploads/logo-for-site.jpeg";
		} else if(file_exists(ROOT_PATH."uploads".DS."logo-for-site.png")) {
			$imageLink = "{C_default_http_host}uploads/logo-for-site.png";
		} else {
			$imageCheck = false;
		}
	}
	$sitename = lang::get_lang("sitename");
	$ogpr = array(
		"og:site_name" => htmlspecialchars($sitename),
		"og:url" => "{C_default_http_host}".$link,
		"og:title" => htmlspecialchars($title),
		"og:description" => htmlspecialchars($description),
		"og:type" => $type,
	);
	if($imageCheck && !empty($imageLink)) {
		$ogpr = array_merge($ogpr, array(
			"og:image" => $imageLink."?".time(),
		));
	}
	$og = array(
		"title" => htmlspecialchars($title),
		"description" => htmlspecialchars($description),
	);
	if($imageCheck && !empty($imageLink)) {
		$og = array_merge($og, array(
			"image" => $imageLink."?".time(),
		));
	}
	$meta = array(
		"og" => $og,
		"ogpr" => $ogpr,
		"title" => htmlspecialchars($title),
		"robots" => $robots,
		"description" => htmlspecialchars($description),
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
	$rt = new Headers();
	return $rt->builder($array, $clear, $no_js);
}

// regCssJs("{THEME}/jquery.js", "js", true)
function regCssJs($js, $type, $mark = false, $name = "") {
	call_user_func_array("modules::regCssJs", func_get_args());
}

function unRegCssJs($js, $type = "", $mark = false, $name = "") {
	call_user_func_array("modules::unRegCssJs", func_get_args());
}

function ajax_check() {
	if(HTTP::getServer('HTTP_X_REQUESTED_WITH') && HTTP::getServer('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest') {
		return "ajax";
	} else {
		return "html";
	}
}

?>