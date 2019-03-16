<?php
if(!defined("IS_CORE")) {
	echo "ERROR";
	die();
}

Route::Set("manifest", "manifest.cache")->defaults(array(
          'page' => 'manifest',
));

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