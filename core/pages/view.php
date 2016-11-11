<?php

class page {
	
	function related($id, $name, $descr) {
		$search = others_video($name);
		$limit = config::Select("related");
		$view = str_replace("\"", "\\\"", Saves::SaveOld($name." ".$descr));
		db::doquery("SELECT `id`, `alt_name`, `title`, `image`, `descr`, (MATCH(`title`, `descr`) AGAINST(\"".$view."\")) AS `status` FROM `posts` WHERE `id` != ".$id." AND MATCH(`title`, `descr`) AGAINST(\"".$view."\") ORDER BY `status` DESC LIMIT ".$limit, true);
		while($row = db::fetch_assoc()) {
			$short_descr = bbcodes::clear_bbcode($row['descr']);
			$short_descr = trim($short_descr);
			$short_descr = cut($short_descr, 100);
			$short_descr = trim($short_descr);
			$row['descr'] = cut($short_descr, 0, 200, "...");
			$row['img']=$row['image'];
			templates::assign_vars($row, "relateds", $row['id']);
		}
	}
	
	function __construct() {
		$link = Route::param("view");
		if(!$link) {
			templates::error("", "");
			return;
		}
		$link = trim(Saves::SaveOld($link, true));
		if(empty($link)) {
			templates::error("", "");
		}
		$repl = ToTranslit($link);
		if(!cache::Exists($repl)) {
			$model = new ModelDB("posts", "\"%".$link."%\"", "alt_name", "select", "like", array("id", "title", "image", "descr", "time", "added", "cat_id"));
			if($model->loaded()) {
				cache::Set($repl, ($model));
			} else {
				templates::error("", "");
			}
		} else {
			$model = cache::Get($repl);
		}
		if(!cache::Exists("category")) {
			$category = array();
			db::doquery("SELECT * FROM `category`", true);
			while($row = db::fetch_assoc()) {
				$category[$row['cat_id']] = $row;
			}
			cache::Set("category", $category);
		} else {
			$category = cache::Get("category");
		}
		$sub_action = Route::param("sub_action");
		if(!(!$sub_action)) {
			$this->related($model->id, $model->title, $model->descr);
			$tpl = templates::complited_assing_vars("relatednews");
			if(ajax_check() != "ajax") {
				templates::complited($tpl);
				templates::display();
			} else {
				HTTP::echos(templates::view($tpl));
			}
			return;
		}
		$tags = "";
		if($model->loaded()) {
			if(!cache::Exists("tags_".$repl)) {
				$tags = new ModelDB("tags", $model->id, array("post_id", "id"));
				if($tags->loaded()) {
					cache::Set("tags_".$repl, ($tags));
				}
			} else {
				$tags = cache::Get("tags_".$repl);
			}
		}
		$descr = $model->descr;
		$short_descr = trim(cut(trim(bbcodes::clear_bbcode($descr)), 100));
		$descr = bbcodes::colorit($descr);
		$comment = new comment($model->id);
		$commCout = $comment->getCount();
		$comments = $comment->get(false);
		$addcomments = $comment->addcomments();
		templates::assign_vars(array("title" => $model->title, "alt_name" => $link, "added" => $model->added, "alt_name" => $link, "full-story" => $descr, "action" => "fullnews", "comments" => $comments, "addcomments" => $addcomments, "cat_name" => (isset($category[$model->cat_id]) ? $category[$model->cat_id]['name'] : ""), "cat_altname" => (isset($category[$model->cat_id]) ? $category[$model->cat_id]['alt_name'] : ""), "commCout" => $commCout));
		$tpl = templates::complited_assing_vars("fullstory");
		templates::complited($tpl, array("title" => $model->title, "meta" => array(
					"ogpr" => array(
						"og:image" => "{C_default_http_host}".$model->image,
						"og:site_name" => "{L_sitename}",
						"og:url" => "{C_default_http_host}".$model->alt_name,
						"og:title" => $model->title,
						"og:description" => $short_descr,
						"og:type" => "website",
					),
					"link" => array(
						"image_src" => "{C_default_http_host}".$model->image,
					),
					"keywords" => implode(",", $tags->__getAll("tag")),
					"description" => $short_descr,
				)));
		unset($tags, $model, $link);
		templates::display();
	}
	
}

?>