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
	$prev = (isset($meta['prevLink']) ? $meta['prevLink'] : (isset($meta['prev']) ? $meta['prev'] : (isset($seoBlock['og']['prev']) ? $seoBlock['og']['prev'] : (isset($seoBlock['ogp']['prev']) ? $seoBlock['ogp']['prev'] : (isset($seoBlock['main']['prev']) ? $seoBlock['main']['prev'] : (isset($seoBlock['main']['prev']) ? $seoBlock['main']['prev'] : ""))))));
	$next = (isset($meta['nextLink']) ? $meta['nextLink'] : (isset($meta['next']) ? $meta['next'] : (isset($seoBlock['og']['next']) ? $seoBlock['og']['next'] : (isset($seoBlock['ogp']['next']) ? $seoBlock['ogp']['next'] : (isset($seoBlock['main']['next']) ? $seoBlock['main']['next'] : (isset($seoBlock['main']['next']) ? $seoBlock['main']['next'] : ""))))));
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
		"og:site_name" => htmlspecialchars_decode($sitename),
		"og:url" => "{C_default_http_host}".$link,
		"og:title" => htmlspecialchars_decode($title),
		"og:description" => htmlspecialchars_decode($description),
		"og:type" => $type,
	);
	if($imageCheck && !empty($imageLink)) {
		$ogpr = array_merge($ogpr, array(
			"og:image" => $imageLink."?".time(),
		));
	}
	$og = array(
		"title" => htmlspecialchars_decode($title),
		"description" => htmlspecialchars_decode($description),
	);
	if($imageCheck && !empty($imageLink)) {
		$og = array_merge($og, array(
			"image" => $imageLink."?".time(),
		));
	}
	$meta = array(
		"og" => $og,
		"ogpr" => $ogpr,
		"title" => htmlspecialchars_decode($title),
		"robots" => $robots,
		"description" => htmlspecialchars_decode($description),
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
		$meta["link"]["image_src"] = $imageLink."?".time();
	}
	if(!empty($next)) {
		$meta["link"]["next"] = $next;
	}
	if(!empty($prev)) {
		$meta["link"]["prev"] = $prev;
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

function AmperOr() {
	return call_user_func_array("modules::AmperOr", func_get_args());
}

function headers($array = array(), $clear = false, $no_js = false, $no_css = false) {
	$rt = new Headers();
	return $rt->builder($array, $clear, $no_js, $no_css);
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

function ajax($arr) {
	HTTP::ajax($arr);
}

function changeSortAdminMenu($menuName, $sort = 0) {
	addEventRef("admin_menu_sort", function(&$menu) use ($menuName, $sort) {
		if(isset($menu[$menuName])) {
			$menu[$menuName]['cat']['sort'] = $sort;
		}
	});
}

function changeSortAdminMenuItem($menuName, $linkPart, $sort = 0) {
	addEventRef("admin_menu_sort", function(&$menu) use ($menuName, $linkPart, $sort) {
		if(isset($menu[$menuName]['item'])) {
			for($i=0;$i<sizeof($menu[$menuName]['item']);$i++) {
				if(strpos($menu[$menuName]['item'][$i]['link'], $linkPart)!==false) {
					$menu[$menuName]['item'][$i]['sort'] = $sort;
					break;
				}
			}
		}
	});
}

?>