<?php

class page {
	
	function __construct() {
		$type = rawurldecode(Route::param("type"));
		if(!$type) {
			templates::error("{L_error_page}", "{L_error_level}");
		}
		$sel1 = "count(`id`) as ct";
		$sel2 = "`id`, `alt_name`, `title`, `image`, `descr`, `time`, `added`";
		switch($type) {
			case "cat":
				if(!Route::param("alt_name")) {
					templates::error("{L_error_page}", "{L_error_level}");
				}
				$string = saves(Route::param("alt_name"), true);
				$title = "Просмотр категории \"".$string."\"";
				$db = "SELECT {%sel%} FROM `posts` WHERE `active` = \"yes\"".(config::Select("new_date") ? " AND `time` <= UNIX_TIMESTAMP()" : "")." AND (`cat_id` regexp '[[:<:]]'+(SELECT `cat_id` FROM `category` WHERE `alt_name` LIKE \"".$string."\" LIMIT 1)+'[[:>:]]') ORDER BY `id` DESC";
			break;
			case "search":
				if(!isset($_POST['q']) && !isset($_GET['q'])) {
					templates::error("{L_error_page}", "{L_error_level}");
				}
				if(isset($_POST['q'])) {
					$string = saves($_POST['q'], true);
				} else if(isset($_GET['q'])) {
					$string = saves($_GET['q'], true);
				}
				$title = "Поиск \"".$string."\"";
				$db = "SELECT {%sel%} FROM `posts` WHERE `active` = \"yes\"".(config::Select("new_date") ? " AND `time` <= UNIX_TIMESTAMP()" : "")." AND MATCH(`title`, `descr`) AGAINST('+".$string."' IN BOOLEAN MODE) ORDER BY `id` DESC";
			break;
		}
		$pages = Route::param('page');
		$count = db::doquery(str_replace("{%sel%}", $sel1, $db));
		db::free();
		templates::assign_var("count", $count['ct']);
		if(isset($pages) && is_numeric($pages) && $pages>0) {
			$page = intval($pages);
		} else {
			$page = 1;
		}
		$start = is_numeric($pages) ? intval($pages)-1 : 0;
		$limit = 9;
		$pg = new pager($start, $count['ct'], $limit, "cat", "alt_name=".$string.";page", 10, true);
		$pages = $pg->get();
		$limits = $pg->limit();
		unset($pg);
		foreach($pages as $id=>$page) {
			templates::assign_vars($page, "pages", $id);
		}
		db::doquery(str_replace("{%sel%}", $sel2, $db)." ".$limits, true);
		while($row = db::fetch_assoc()) {
			$row['short_descr'] = trim(cut(trim(bbcodes::clear_bbcode($row['descr'])), 100));
			templates::assign_vars($row, "index", "index".$row['id']);
		}
		$tmp = templates::complited_assing_vars("index");
		templates::complited($tmp, array("title" => $title, "meta" => array(
					"ogpr" => array(
						"og:image" => "{C_default_http_host}logo.jpg?1",
						"og:site_name" => "{L_sitename}",
						"og:url" => "{C_default_http_host}",
						"og:title" => $title,
						"og:type" => "website",
					),
					"link" => array(
						"image_src" => "{C_default_http_host}logo.jpg?1",
					),
				)));
		templates::display();
	}
	
}

?>