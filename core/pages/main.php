<?php
/*
 *
 * @version 1.25.7-a1
 * @copyright 2014-2015 KilleR for Cardinal Engine
 *
 * Version Engine: 1.25.7-a1
 * Version File: 1
 *
 * 1.1
 * add seo optimization
 * 1.2
 * add view post on site
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
exit();
}

class page {
	
	function view() {
	}

    function __construct() {
		execEvent("before_print_index");
		$page = Route::param('pages');
		if($page>1) {
			$link = "{R_[main][pages=".$page."]}";
		} else {
			$link = "";
		}
		if(templates::check_exists("index")) {
			$tmp = templates::completed_assign_vars("index");
		} else {
			$tmp = "";
		}
		addSeo("link", $link);
		$title = array();
		$title['title'] = lang::get_lang('sitename');
		$title = array_merge($title, releaseSeo(array(), true));
		templates::completed($tmp, $title);
		templates::display();
	}

}

?>