<?php

if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

function view_pages($page) {
global $manifest;
	if(array_key_exists($page, $manifest['pages']) && file_exists(ROOT_PATH."core/pages/".$manifest['pages'][$page])) {
		include_once(ROOT_PATH."core/pages/".$manifest['pages'][$page]);
		return;
	}
	if(array_key_exists($page, $manifest['class_pages'])) {
		$page = $manifest['class_pages'][$page];
		$page['object']->$page['func']();
		unset($page);
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