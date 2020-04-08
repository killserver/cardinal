<?php

class SortList extends Core {

	function __construct() {
		$table = Arr::get($_GET, "table");
		$first = (nsubstr($table, 0, 1));
		if(sizeof($_POST)>0) {
			for($i=0;$i<sizeof($_POST['serialized']);$i++) {
				db::doquery("UPDATE {{".$table."}} SET `sort` = ".$i." WHERE `".$first."Id` = ".db::escape($_POST['serialized'][$i]['id']));
			}
			return;
		}
		$add = "";
		$key = "";
		if($table=="products") {
			$get = "name";
			$key = "Продукция";
			$title = "Категории скидок";
		}
		$arr = array();
		db::doquery("SELECT *, `".$first."Id`, `".$get."Ru`, `sort`".$add." FROM {{".$table."}} ORDER BY `sort` ASC", true);
		while($row = db::fetch_assoc()) {
			$row['id'] = $row[$first."Id"];
			$row['title'] = $row[$get."Ru"];
			$row['key'] = (isset($row['category']) ? $row['category'] : $key);
			if(!isset($arr[$row['key']])) {
				$arr[$row['key']] = array();
			}
			$arr[$row['key']][] = $row;
		}
		templates::assign_var("json", json_encode($arr));
		templates::assign_var("table", $title);
		$this->Prints("SortList");
	}

}