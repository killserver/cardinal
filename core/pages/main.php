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

    function __construct() {
		db::doquery("SELECT `id`, `alt_name`, `title`, `image`, `descr`, `time`, `added` FROM `posts` WHERE `active` = \"yes\"".(config::Select("new_date") ? " AND `time` <= UNIX_TIMESTAMP()" : ""), true);
		while($row = db::fetch_assoc()) {
			templates::assign_vars($row, "index", "index".$row['id']);
		}
		$tmp = templates::complited_assing_vars("index");
		templates::complited($tmp, array("title" => lang::get_lang('sitename'), "meta" => array(
					"ogpr" => array(
						"og:image" => "{C_default_http_host}logo.jpg?1",
						"og:site_name" => "{L_sitename}",
						"og:url" => "{C_default_http_host}",
						"og:title" => "{L_sitename}",
						"og:description" => "{L_s_description}",
						"og:type" => "website",
					),
					"link" => array(
						"image_src" => "{C_default_http_host}logo.jpg?1",
					),
					"keywords" => "{L_s_keywords}",
					"description" => "{L_s_description}",
				)));
		templates::display();
	}

}

?>