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
		$tmp = templates::complited_assing_vars("index");
		$ogpr = array(
			"og:site_name" => "{L_sitename}",
			"og:url" => "{C_default_http_host}",
			"og:title" => "{L_sitename}",
			"og:description" => "{L_s_description}",
			"og:type" => "website",
		);
		if(file_exists(ROOT_PATH."logo.jpg")) {
			$ogpr = array_merge($ogpr, array(
				"og:image" => "{C_default_http_host}logo.jpg?".time(),
			));
		}
		$meta = array(
			"ogpr" => $ogpr,
			"description" => "{L_s_description}",
		);
		if(file_exists(ROOT_PATH."logo.jpg")) {
			$meta = array_merge($meta, array(
				"link" => array(
					"image_src" => "{C_default_http_host}logo.jpg?".time(),
				),
			));
		}
		templates::complited($tmp, array("title" => lang::get_lang('sitename'), "meta" => $meta));
		templates::display();
	}

    function __construct() {
		if(defined("WITHOUT_DB")) {
			$this->view();
			return false;
		}
		Route::RegParam("inPage", "index");
		$pages = Route::param('pages');
		$count = db::doquery("SELECT COUNT(`id`) AS `ct` FROM `posts` WHERE `active` = \"yes\"".(config::Select("new_date") ? " AND `time` <= UNIX_TIMESTAMP() AND `type` = \"post\"" : ""));
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
		unset($pg);
		foreach($pages as $id=>$page) {
			templates::assign_vars($page, "pages", $id);
		}
		db::doquery("SELECT `id`, `alt_name`, `title`, `image`, `descr`, `time`, `added` FROM `posts` WHERE `active` = \"yes\"".(config::Select("new_date") ? " AND `time` <= UNIX_TIMESTAMP()" : "")." AND `type` = \"post\" ORDER BY `id` DESC ".$limits, true);
		while($row = db::fetch_assoc()) {
			$row['short_descr'] = trim(cut(trim(bbcodes::clear_bbcode($row['descr'])), 100));
			templates::assign_vars($row, "index", "index".$row['id']);
		}
		$this->view();
	}

}

?>