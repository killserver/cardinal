<?php
if(!defined("IS_CORE")) {
	echo "ERROR";
	die();
}

Route::Set("manifest", "manifest.cache")->defaults(array(
          'page' => 'manifest',
));

Route::Set("changeLangInMainRoute", "changeLang(-<lang>).php")->defaults(array(
	"callback" => array("changeLangInMainRoute"),
	"lang" => lang::get_lg(),
));

addEvent("go_to_home_with_lang", "fixGoToHomeWithLang");
function fixGoToHomeWithLang() {
	global $mainLangSite;
	$selectLang = Route::param("lang");
	if(empty($selectLang) || $selectLang==$mainLangSite) {
		return config::Select("default_http_local");
	} else if(!empty($selectLang)) {
		return config::Select("default_http_local").Route::param("lang")."/";
	}
}

function changeLangInMainRoute($lang, $langDB) {//
	global $mainLangSite;
	$server = config::Select("default_http_host");
	$uri = HTTP::getServer("HTTP_REFERER");
	$uri = substr($uri, strlen($server));
	$uri = substr($uri, 3);
	if(!isset($_GET['lang'])) {
		$_GET['lang'] = Route::param("langs");
	}
	if($mainLangSite!=$_GET['lang']) {
		$_GET['lang'] = $_GET['lang']."/".$uri;
	} else if(!empty($uri) && $mainLangSite==$_GET['lang']) {
		$_GET['lang'] = $_GET['lang']."/".$uri;
	} else {
		$_GET['lang'] = "";
	}
	location(config::Select("default_http_local").$_GET['lang']);
}

function routeDefault($uri, $page) {
	if($uri && preg_match("#^(?:page/(?P<page>[^/.,;?\n]++).html)?$#uD", $uri, $all)) {
		$ret = array(
			'page'  => 'main',
			'pages' => $all['page'],
		);
		return $ret;
	}
	if($uri) {
		Route::Delete("default");
		$par = Route::Load($page);
		if(Route::Search("notFound") && (!is_array($par) || !isset($par['params']) || !is_array($par['params']) || !Route::checkEmpty($par['params']))) {
			return Route::Get('notFound')->GetDefaults();
		} else {
			return $par['params'];
		}
	} else {
		return array(
			'page' => $page,
		);
	}
}
Route::Set("default", "routeDefault");