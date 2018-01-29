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

    function __construct() {
		if(defined("WITHOUT_DB") || !db::connected()) {
			$this->view();
			return false;
		}
		Route::RegParam("inPage", "index");
		$pages = Route::param('pages');
		$count = db::doquery("SELECT COUNT(`id`) AS `ct` FROM {{posts}} WHERE `active` LIKE \"yes\"".(config::Select("new_date") ? " AND `time` <= UNIX_TIMESTAMP() AND `type` LIKE \"post\"" : ""));
		db::free();
		templates::assign_var("count", $count['ct']);
		if(isset($pages) && is_numeric($pages) && $pages > 0) {
			$page = intval($pages);
		} else {
			$page = 1;
		}
		$start = is_numeric($pages) ? intval($pages)-1 : 0;
		$limit = 9;
		$pg = new pager($start, $count['ct'], $limit, "main", "pages", 10, true);
		$pages = $pg->get();
		$limits = $pg->limit();
		$prevLink = $pg->prevLink();
		$nextLink = $pg->nextLink();
		unset($pg);
		templates::assign_vars(array("prevLink" => $prevLink, "nextLink" => $nextLink));
		foreach($pages as $id=>$page) {
			templates::assign_vars($page, "pages", $id);
		}
		db::doquery("SELECT `id`, `alt_name`, `title`, `image`, `descr`, `time`, `added` FROM {{posts}} WHERE `active` LIKE \"yes\"".(config::Select("new_date") ? " AND `time` <= UNIX_TIMESTAMP()" : "")." AND `type` LIKE \"post\" ORDER BY `id` DESC ".$limits, true);
		while($row = db::fetch_assoc()) {
			$row['short_descr'] = trim(cut(trim(bbcodes::clear_bbcode($row['descr'])), 100));
			templates::assign_vars($row, "index", "index".$row['id']);
		}
		$this->view();
	}

}

?>