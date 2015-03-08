<?php
/*
*
* Version Engine: 1.25.3
* Version File: 12
*
* 12.1
* add support initialize config before include page
* 12.2
* fix errors
*
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

function view_pages($page) {
global $manifest;
	if(array_key_exists($page, $manifest['before_ini_class'])) {
		$pages = $manifest['before_ini_class'][$page];
		$pages['object']->$pages['func']();
		unset($pages);
	}
	if(array_key_exists($page, $manifest['pages']) && file_exists(ROOT_PATH."core/pages/".$manifest['pages'][$page])) {
		include_once(ROOT_PATH."core/pages/".$manifest['pages'][$page]);
		if(array_key_exists($page, $manifest['after_ini_class'])) {
			$page = $manifest['after_ini_class'][$page];
			$page['object']->$page['func']();
			unset($page);
		}
		return;
	}
	if(array_key_exists($page, $manifest['class_pages'])) {
		$pages = $manifest['class_pages'][$page];
		$pages['object']->$pages['func']();
		unset($pages);
		if(array_key_exists($page, $manifest['after_ini_class'])) {
			$page = $manifest['after_ini_class'][$page];
			$page['object']->$page['func']();
			unset($page);
		}
		return;
	}
	switch($page) {
		case "news":
			include_once(ROOT_PATH."core/pages/view.".ROOT_EX);
		break;
		case "main":
		default:
			include_once(ROOT_PATH."core/pages/main.".ROOT_EX);
		break;
	}
}

?>