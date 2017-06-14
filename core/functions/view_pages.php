<?php
/*
 *
 * @version 5.4
 * @copyright 2014-2016 KilleR for Cardinal Engine
 *
 * Version Engine: 5.4
 * Version File: 12
 *
 * 12.1
 * add support initialize config before include page
 * 12.2
 * fix errors
 * 12.3
 * add pages red and login pages in core
 * 12.4
 * add page error
 * 12.5
 * add page add post
 * 12.6
 * add page skin reBuilder
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
		if(is_array($pages) && !isset($pages['object'])) {
			foreach($pages as $p) {
				if(!isset($p['object'])) {
					continue;
				}
				$p['object']->$p['func']();
			}
		} else if(isset($pages['object'])) {
			$pages['object']->$pages['func']();
		}
		unset($pages);
	}
	if(array_key_exists($page, $manifest['pages']) && file_exists(PATH_PAGES.$manifest['pages'][$page])) {
		include_once(PATH_PAGES.$manifest['pages'][$page]);
		if(array_key_exists($page, $manifest['after_ini_class'])) {
			$page = $manifest['after_ini_class'][$page];
			if(is_array($page) && !isset($page['object'])) {
				foreach($page as $p) {
					if(!isset($p['object'])) {
						continue;
					}
					$p['object']->$p['func']();
				}
			} else if(isset($page['object'])) {
				$page['object']->$page['func']();
			}
			unset($page);
		}
		return;
	}
	if(array_key_exists($page, $manifest['class_pages'])) {
		$pages = $manifest['class_pages'][$page];
		if(isset($pages['object'])) {
			$pages['object']->$pages['func']();
		}
		unset($pages);
		if(array_key_exists($page, $manifest['after_ini_class'])) {
			$page = $manifest['after_ini_class'][$page];
			if(is_array($page) && !isset($page['object'])) {
				foreach($page as $p) {
					if(!isset($p['object'])) {
						continue;
					}
					$p['object']->$p['func']();
				}
			} else if(isset($page['object'])) {
				$page['object']->$page['func']();
			}
			unset($page);
		}
		return;
	}
	switch($page) {
		case "error":
			include_once(PATH_PAGES."error.".ROOT_EX);
		break;
		case "upload":
			include_once(PATH_PAGES."upload.".ROOT_EX);
		break;
		case "reg":
			include_once(PATH_PAGES."reg.".ROOT_EX);
		break;
		case "manifest":
			include_once(PATH_PAGES."manifest.".ROOT_EX);
		break;
		case "add":
		case "edit":
		case "post":
			include_once(PATH_PAGES."post.".ROOT_EX);
		break;
		case "login":
			include_once(PATH_PAGES."login.".ROOT_EX);
		break;
		case "news":
			include_once(PATH_PAGES."view.".ROOT_EX);
		break;
		case "skin":
			include_once(PATH_PAGES."skin.".ROOT_EX);
		break;
		case "search":
			include_once(PATH_PAGES."search.".ROOT_EX);
		break;
		case "main":
			include_once(PATH_PAGES."main.".ROOT_EX);
		break;
		default:
			if(array_key_exists("default", $manifest['class_pages'])) {
				if(array_key_exists("default", $manifest['before_ini_class'])) {
					$pages = $manifest['before_ini_class']["default"];
					if(is_array($pages) && !isset($pages['object'])) {
						foreach($pages as $p) {
							$p['object']->$p['func']();
						}
					} else {
						$pages['object']->$pages['func']();
					}
					unset($pages);
				}
				$pages = $manifest['class_pages']["default"];
				if(isset($pages['object'])) {
					$pages['object']->$pages['func']();
				}
				unset($pages);
				if(array_key_exists("default", $manifest['after_ini_class'])) {
					$page = $manifest['after_ini_class']["default"];
					if(is_array($page) && !isset($page['object'])) {
						foreach($page as $p) {
							if(!isset($p['object'])) {
								continue;
							}
							$p['object']->$p['func']();
						}
					} else if(isset($page['object'])) {
						$page['object']->$page['func']();
					}
					unset($page);
				}
				return false;
			}
			include_once(PATH_PAGES."main.".ROOT_EX);
		break;
	}
	if(array_key_exists($page, $manifest['after_ini_class'])) {
		$page = $manifest['after_ini_class'][$page];
		if(is_array($page) && !isset($page['object'])) {
			foreach($page as $p) {
				if(!isset($p['object'])) {
					continue;
				}
				$p['object']->$p['func']();
			}
		} else if(isset($page['object'])) {
			$page['object']->$page['func']();
		}
		unset($page);
	}
}

?>