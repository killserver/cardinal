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
		case "sitemap":
			include_once(ROOT_PATH."core/pages/sitemap.".ROOT_EX);
		break;
		case "simular":
			include_once(ROOT_PATH."core/pages/simular.".ROOT_EX);
		break;
		case "view":
			include_once(ROOT_PATH."core/pages/view.".ROOT_EX);
		break;
		case "smiles":
		case "js":
			include_once(ROOT_PATH."core/pages/js.".ROOT_EX);
		break;
		case "css":
			include_once(ROOT_PATH."core/pages/css.".ROOT_EX);
		break;
		case "add":
		case "takeadd":
			include_once(ROOT_PATH."core/pages/add.".ROOT_EX);
		break;
		case "cat":
			include_once(ROOT_PATH."core/pages/cats.".ROOT_EX);
		break;
		case "feedback":
			include_once(ROOT_PATH."core/pages/feedback.".ROOT_EX);
		break;
		case "edit":
		case "takeedit":
			include_once(ROOT_PATH."core/pages/edit.".ROOT_EX);
		break;
		case "delete":
			include_once(ROOT_PATH."core/pages/delete.".ROOT_EX);
		break;
		case "hacker":
			include_once(ROOT_PATH."core/pages/hacker.".ROOT_EX);
		break;
		case "ajax":
			include_once(ROOT_PATH."core/pages/ajax.".ROOT_EX);
		break;
		case "reg":
			include_once(ROOT_PATH."core/pages/reg.".ROOT_EX);
		break;
		case "login":
			include_once(ROOT_PATH."core/pages/login.".ROOT_EX);
		break;
		case "user":
			include_once(ROOT_PATH."core/pages/user.".ROOT_EX);
		break;
		case "config_player":
			include_once(ROOT_PATH."core/pages/config_player.".ROOT_EX);
		break;
		case "playlist":
			include_once(ROOT_PATH."core/pages/playlist.".ROOT_EX);
		break;
		case "player":
			include_once(ROOT_PATH."core/pages/player.".ROOT_EX);
		break;
		case "player_style":
			include_once(ROOT_PATH."core/pages/player_style.".ROOT_EX);
		break;
		case "watch":
			include_once(ROOT_PATH."core/pages/video.".ROOT_EX);
		break;
		case "html5":
			include_once(ROOT_PATH."core/pages/html5.".ROOT_EX);
		break;
		case "image_send":
			include_once(ROOT_PATH."core/pages/image_send.".ROOT_EX);
		break;
		case "search":
			include_once(ROOT_PATH."core/pages/search.".ROOT_EX);
		break;
		case "rss":
			include_once(ROOT_PATH."core/pages/rss.".ROOT_EX);
		break;
		case "parser_video":
			include_once(ROOT_PATH."core/pages/parser_video.".ROOT_EX);
		break;
		case "main":
		default:
			include_once(ROOT_PATH."core/pages/main.".ROOT_EX);
		break;
	}
}

?>