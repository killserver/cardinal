<?php
/*
 *
 * @version 5.2
 * @copyright 2014-2016 KilleR for Cardinal Engine
 *
 * Version Engine: 5.2
 * Version File: 1
 *
 * 1.1
 * add page for add post on site
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

class page {
	
	function __construct() {
		$page = Route::param("action");
		if(sizeof($_POST)>0) {
			if(isset($_GET['add']) || $page=="add") {
				$model = new ModelDB("posts");
				$_POST = modules::change_db("add", $_POST);
				$title = Arr::get($_POST, 'title');
				$model->title = Saves::SaveOld($title, true);
				$altName = Arr::get($_POST, 'alt_name', false);
				if(!$altName) {
					$altName = ToTranslit($title);
				}
				$altName = Saves::SaveOld(Saves::SaveAltName($altName), true);
				$model->alt_name = $altName;
				$descr = Arr::get($_POST, 'descr');
				$descr = bbcodes::html2bbcode($descr);
				$descr = preg_replace('#\[img\](.*?)uploads/'.modules::get_user("alt_name").'/(.+?)\[/img\]#is', "[attach='".modules::get_user("alt_name")."']$2[/attach]", $descr);
				$model->descr = Saves::SaveOld($descr);
				$model->time = "UNIX_TIMESTAMP()";
				$model->cat_id = intval(Arr::get($_POST, 'cat', 0));
				$model->added = modules::get_user("alt_name");
				$model->type = "post";
				$model->active = "yes";
				$model->Insert();
				$id = $model->Get("id");
				$image = "";
				if(isset($_FILES['image']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
					$types = explode(".", $_FILES['image']['name']);
					$type = end($types);
					unset($types);
					$check = explode("/", $_FILES['image']['type']);
					if($check[0]=="image") {
						move_uploaded_file($_FILES['image']['tmp_name'], ROOT_PATH . DS . "uploads" . DS . modules::get_user("alt_name") . DS . $id .".". $type);
						$model->image = "uploads/" . modules::get_user("alt_name") . "/" . $id .".". $type . "?" . time();
						$model->Update();
					}
				}
				$tags = new ModelDB("tags", $id, "id", "insert");
				$tags_list = Saves::SaveOld(Arr::get($_POST, 'tags'), true);
				$tags_list = explode(",", $tags_list);
				$tags_list = array_unique($tags_list);
				for($i=0;$i<sizeof($tags_list);$i++) {
					if(empty($tags_list[$i])) {
						continue;
					}
					$tags->post_id = $id;
					$tags->tag = $tags_list[$i];
					$tags->Insert();
				}
				//} else if(isset($_GET['edit'])) {
				//	modules::change_db("edit", $db);
				//}
				location("{C_default_http_host}".Route::get("news")->uri(array('view' => $altName)), 5, false);
				templates::assign_vars(array("title" => "{L_complited_add}", "descr" => "{L_complited_view}", "action" => "done"));
				$tpl = templates::complited_assing_vars("ainfo");
				templates::complited($tpl);
				templates::display();
			} else if($page=="edit") {
				$model = new ModelDB("posts", "\"%".Route::param("sub_link")."%\" AND `type` = \"post\"", "alt_name", "select", "like");
				$_POST = modules::change_db("add", $_POST);
				$title = Saves::SaveOld(Arr::get($_POST, 'title'), true);
				if($model->title != $title) {
					$model->title = $title;
				}
				$cat_id = intval(Arr::get($_POST, 'cat', 0));
				if($model->cat_id != $cat_id) {
					$model->cat_id = $cat_id;
				}
				$descr = Arr::get($_POST, 'descr');
				$descr = bbcodes::html2bbcode($descr);
				$descr = preg_replace('#\[img\](.*?)uploads/'.modules::get_user("alt_name").'/(.+?)\[/img\]#is', "[attach='".modules::get_user("alt_name")."']$2[/attach]", $descr);
				$descr = Saves::SaveOld($descr);
				if($model->descr != $descr) {
					$model->descr = $descr;
				}
				$model->type = "post";
				$model->Update();
				$id = $model->id;
				$image = "";
				if(isset($_FILES['image']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
					$types = explode(".", $_FILES['image']['name']);
					$type = end($types);
					unset($types);
					$check = explode("/", $_FILES['image']['type']);
					if($check[0]=="image") {
						if(file_exists(ROOT_PATH . DS . $model->image)) {
							unlink(ROOT_PATH . DS . $model->image);
						}
						move_uploaded_file($_FILES['image']['tmp_name'], ROOT_PATH . DS . "uploads" . DS . modules::get_user("alt_name") . DS . $id .".". $type);
						$model->image = "uploads/" . modules::get_user("alt_name") . "/" . $id .".". $type . "?" . time();
						$model->Update();
					}
				}
				$tags = new ModelDB("tags", $id, "id", "insert");
				$tags->Delete();
				$tags = new ModelDB("tags", $id, "id", "insert");
				$tags_list = Saves::SaveOld(Arr::get($_POST, 'tags'), true);
				$tags_list = explode(",", $tags_list);
				$tags_list = array_unique($tags_list);
				for($i=0;$i<sizeof($tags_list);$i++) {
					if(empty($tags_list[$i])) {
						continue;
					}
					$tags->post_id = $id;
					$tags->tag = $tags_list[$i];
					$tags->Insert();
				}
				$link = trim(Saves::SaveOld($model->alt_name, true));
				cache::Delete(ToTranslit($link));
				location("{C_default_http_host}".Route::get("news")->uri(array('view' => $link)), 5, false);
				templates::assign_vars(array("title" => "{L_complited_add}", "descr" => "{L_complited_view}", "action" => "done"));
				$tpl = templates::complited_assing_vars("ainfo");
				templates::complited($tpl);
				templates::display();
			}
		} else {
			$dir = ROOT_PATH."core".DS."media".DS."smiles".DS;
			if(is_dir($dir)) {
				$files = array();
				if($dh = dir($dir)) {
					$i=1;
					while(($file = $dh->read()) !== false) {
						if(strpos($file, ".gif") !== false && $file != "." && $file != "..") {
							$sm = strtr($file, array(".gif" => ""));
							templates::assign_vars(array(
								"smile" => $sm,
							), "smiles", "smile_".$i);
							$i++;
						}
					}
				$dh->close();
				}
			}
			templates::assign_var("title", "");
			templates::assign_var("alt_name", "");
			templates::assign_var("descr", "");
			templates::assign_var("tags", "");
			if($page == "add") {
				modules::use_modules("add");
			} else if($page == "edit") {
				modules::use_modules("edit");
				$sub_link = Route::param("sub_link");
				db::doquery("SELECT * FROM `posts` WHERE `alt_name` LIKE \"".Saves::SaveOld($sub_link, true)."\" AND `type` = \"post\"", true);
				if(db::num_rows() == 0) {
					templates::error("Not Found", "{L_error}");
					return;
				}
				$row = db::fetch_assoc();
				$row['descr'] = bbcodes::colorit(str_replace(array("&amp;laquo;", "&amp;raquo;"), array("&laquo;", "&raquo;"), $row['descr']));
				templates::assign_vars($row);
				$tags = db::doquery("SELECT * FROM `tags` WHERE `post_id` = ".$row['id']);
				if(sizeof($tags)>0) {
					templates::assign_var("tags", implode(",", array_map(function($data) {return $data;}, $tags)));
				} else {
					templates::assign_var("tags", "");
				}
			}
			db::doquery("SELECT `cat_id`, `name` FROM `category` ORDER BY `sort` ASC", true);
			while($rows = db::fetch_assoc()) {
				if(isset($row) && is_array($row) && sizeof($row) > 0 && $row['cat_id'] == $rows['cat_id']) {
					$rows['active'] = " selected=\"selected\"";
				} else {
					$rows['active'] = "";
				}
				templates::assign_vars($rows, "cats", $rows['cat_id']);
			}
			$langs = lang::get_lang('translate');
			foreach($langs as $k=>$v) {
				if(empty($v)) {
					$v = "'";
				}
				templates::assign_vars(array('k' => $k, 'v' => $v), "translate", "translate".$k."=".$v);
			}
			$tmp = templates::complited_assing_vars("add");
			templates::complited($tmp);
			templates::display();
		}
	}
	
}

?>