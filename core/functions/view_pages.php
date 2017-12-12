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
	if($page=="debug") {
		$page = "main";
	}
	if(array_key_exists($page, $manifest['before_ini_class'])) {
		$pages = $manifest['before_ini_class'][$page];
		$args = array();
		if(isset($pages['set'])) {
			$args = (isset($pages['args']) ? $pages['args'] : array());
			$pages = $pages['set'];
		}
		if(is_array($pages) && !isset($pages['object'])) {
			foreach($pages as $p) {
				$args = array();
				if(isset($p['set'])) {
					$args = (isset($p['args']) ? $p['args'] : array());
					$p = $p['set'];
				}
				if(isset($p[0]) && is_object($p[0]) && method_exists($p[0], $p[1])) {
					$fn = $p[1];
					$p[0]->$fn($args);
				}
				if(!isset($p['object']) || !is_object($p['object']) || !method_exists($p['object'], $p['func'])) {
					continue;
				}
				$fn = $p['func'];
				$p['object']->$fn($args);
			}
		} else if(isset($pages['object']) && is_object($pages['object']) && method_exists($pages['object'], $pages['func'])) {
			$fn = $pages['func'];
			$pages['object']->$fn($args);
		} else if(isset($pages[0]) && is_object($pages[0]) && method_exists($pages[0], $pages[1])) {
			$fn = $pages[1];
			$pages[0]->$fn($args);
		}
		unset($pages);
	}
	if(array_key_exists($page, $manifest['pages']) && file_exists(PATH_PAGES.$manifest['pages'][$page])) {
		include_once(PATH_PAGES.$manifest['pages'][$page]);
		if(array_key_exists($page, $manifest['after_ini_class'])) {
			$page = $manifest['after_ini_class'][$page];
			$args = array();
			if(isset($pages['set'])) {
				$args = (isset($pages['args']) ? $pages['args'] : array());
				$pages = $pages['set'];
			}
			if(is_array($page) && !isset($page['object'])) {
				foreach($page as $p) {
					$args = array();
					if(isset($p['set'])) {
						$args = (isset($p['args']) ? $p['args'] : array());
						$p = $p['set'];
					}
					if(isset($p[0]) && is_object($p[0]) && method_exists($p[0], $p[1])) {
						$fn = $p[1];
						$p[0]->$fn($args);
					}
					if(!isset($p['object']) || !is_object($p['object']) || !method_exists($p['object'], $p['func'])) {
						continue;
					}
					$fn = $p['func'];
					$p['object']->$fn($args);
				}
			} else if(isset($page['object']) && is_object($page['object']) && method_exists($page['object'], $page['func'])) {
				$fn = $page['func'];
				$page['object']->$fn($args);
			} else if(isset($page[0]) && is_object($page[0]) && method_exists($page[0], $page[1])) {
				$fn = $page[1];
				$page[0]->$fn($args);
			}
			unset($page);
		}
		return;
	}
	if(array_key_exists($page, $manifest['class_pages'])) {
		$pages = $manifest['class_pages'][$page];
		$args = array();
		if(isset($pages['set'])) {
			$args = (isset($pages['args']) ? $pages['args'] : array());
			$pages = $pages['set'];
		}
		if(isset($pages['object']) && is_object($pages['object']) && method_exists($pages['object'], $pages['func'])) {
			$fn = $page['func'];
			$pages['object']->$fn($args);
		}
		if(isset($pages[0]) && is_object($pages[0]) && method_exists($pages[0], $pages[1])) {
			$fn = $pages[1];
			$pages[0]->$fn($args);
		}
		unset($pages);
		if(array_key_exists($page, $manifest['after_ini_class'])) {
			$page = $manifest['after_ini_class'][$page];
			$args = array();
			if(isset($page['set'])) {
				$args = (isset($page['args']) ? $page['args'] : array());
				$page = $page['set'];
			}
			if(is_array($page) && !isset($page['object'])) {
				foreach($page as $p) {
					$args = array();
					if(isset($p['set'])) {
						$args = (isset($p['args']) ? $p['args'] : array());
						$p = $p['set'];
					}
					if(!isset($p['object']) || !is_object($p['object']) || !method_exists($p['object'], $p['func'])) {
						continue;
					}
					$fn = $p['func'];
					$p['object']->$fn($args);
				}
			} else if(isset($page['object']) && is_object($page['object']) && method_exists($page['object'], $page['func'])) {
				$fn = $page['func'];
				$page['object']->$fn($args);
			} else if(isset($page[0]) && is_object($page[0]) && method_exists($page[0], $page[1])) {
				$fn = $page[1];
				$page[0]->$fn($args);
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
		case "pong":
			include_once(PATH_PAGES."pong.".ROOT_EX);
		break;
		case "main":
			include_once(PATH_PAGES."main.".ROOT_EX);
		break;
		default:
			if(array_key_exists("default", $manifest['class_pages'])) {
				if(array_key_exists("default", $manifest['before_ini_class'])) {
					$pages = $manifest['before_ini_class']["default"];
					$args = array();
					if(isset($pages['set'])) {
						$args = (isset($pages['args']) ? $pages['args'] : array());
						$pages = $pages['set'];
					}
					if(is_array($pages) && !isset($pages['object'])) {
						foreach($pages as $p) {
							$args = array();
							if(isset($p['set'])) {
								$args = (isset($p['args']) ? $p['args'] : array());
								$p = $p['set'];
							}
							if(isset($p[0]) && is_object($p[0]) && method_exists($p[0], $p[1])) {
								$fn = $p[1];
								$p[0]->$fn($args);
							}
							if(!isset($p['object']) || !is_object($p['object']) || !method_exists($p['object'], $p['func'])) {
								continue;
							}
							$fn = $p['func'];
							$p['object']->$fn($args);
						}
					} else if(isset($pages['object']) && is_object($pages['object']) && method_exists($pages['object'], $pages['func'])) {
						$fn = $pages['func'];
						$pages['object']->$fn($args);
					} else if(isset($pages[0]) && is_object($pages[0]) && method_exists($pages[0], $pages[1])) {
						$fn = $pages[1];
						$pages[0]->$fn($args);
					}
					unset($pages);
				}
				$pages = $manifest['class_pages']["default"];
				$args = array();
				if(isset($pages['set'])) {
					$args = (isset($pages['args']) ? $pages['args'] : array());
					$pages = $pages['set'];
				}
				if(isset($pages['object']) && is_object($pages['object']) && method_exists($pages['object'], $pages['func'])) {
					$fn = $pages['func'];
					$pages['object']->$fn($args);
				}
				if(isset($pages[0]) && is_object($pages[0]) && method_exists($pages[0], $pages[1])) {
					$fn = $pages[1];
					$pages[0]->$fn($args);
				}
				unset($pages);
				if(array_key_exists("default", $manifest['after_ini_class'])) {
					$page = $manifest['after_ini_class']["default"];
					$args = array();
					if(isset($page['set'])) {
						$args = (isset($page['args']) ? $page['args'] : array());
						$page = $page['set'];
					}
					if(is_array($page) && !isset($page['object'])) {
						foreach($page as $p) {
							$args = array();
							if(isset($p['set'])) {
								$args = (isset($p['args']) ? $p['args'] : array());
								$p = $p['set'];
							}
							if(isset($p[0]) && is_object($p[0]) && method_exists($p[0], $p[1])) {
								$fn = $p[1];
								$p[0]->$fn($args);
							}
							if(!isset($p['object']) || !is_object($p['object']) || !method_exists($p['object'], $p['func'])) {
								continue;
							}
							$fn = $p['func'];
							$p['object']->$fn($args);
						}
					} else if(isset($page['object']) && is_object($page['object']) && method_exists($page['object'], $page['func'])) {
						$fn = $page['func'];
						$page['object']->$fn($args);
					} else if(isset($page[0]) && is_object($page[0]) && method_exists($page[0], $page[1])) {
						$fn = $page[1];
						$page[0]->$fn($args);
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
		$args = array();
		if(isset($page['set'])) {
			$args = (isset($page['args']) ? $page['args'] : array());
			$page = $page['set'];
		}
		if(is_array($page) && !isset($page['object'])) {
			foreach($page as $p) {
				$args = array();
				if(isset($p['set'])) {
					$args = (isset($p['args']) ? $p['args'] : array());
					$p = $p['set'];
				}
				if(isset($p[0]) && is_object($p[0]) && method_exists($p[0], $p[1])) {
					$fn = $p[1];
					$p[0]->$fn($args);
				}
				if(!isset($p['object']) || !is_object($p['object']) || !method_exists($p['object'], $p['func'])) {
					continue;
				}
				$fn = $page['func'];
				$p['object']->$fn($args);
			}
		} else if(isset($page['object']) && is_object($page['object']) && method_exists($page['object'], $page['func'])) {
			$fn = $page['func'];
			$page['object']->$fn($args);
		} else if(isset($page[0]) && is_object($page[0]) && method_exists($page[0], $page[1])) {
			$fn = $page[1];
			$page[0]->$fn($args);
		}
		unset($page);
	}
}

?>