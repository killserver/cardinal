<?php

class RecycleBin extends Core {

	function __construct() {
		if(!file_exists(PATH_CACHE_USERDATA."trashBin.lock")) {
			new Errors();
			return false;
		}
		if(isset($_GET['recover']) && is_numeric($_GET['recover']) && $_GET['recover']>0) {
			$id = intval($_GET['recover']);
			db::doquery("SELECT * FROM {{trashBin}} WHERE `tId` = ".$id, true);
			if(db::num_rows()==0) {
				new Errors();
				return false;
			}
			$row = db::fetch_assoc();
			$table = $row['tTable'];
			$structNow = db::getTable($table);
			$data = json_decode($row['tData'], true);
			$arr = array();
			for($i=0;$i<sizeof($structNow);$i++) {
				$arr[$structNow[$i]] = (isset($data[$structNow[$i]]) ? $data[$structNow[$i]] : "");
			}
			$query = "INSERT INTO `".$table."` SET ".implode(", ", array_map(array($this, "build"), array_keys($arr), array_values($arr)));
			db::doquery($query);
			db::doquery("DELETE FROM {{trashBin}} WHERE `tId` = ".$id);
			cardinal::RegAction("Восстановление данных из корзины. Таблица \"".$table."\". ИД: \"".$id."\"");
			location("./?pages=RecycleBin");
			return false;
		}
		db::doquery("SELECT * FROM {{trashBin}} ORDER BY `tId` DESC", true);
		while($row = db::fetch_assoc()) {
			templates::assign_vars($row, "trash", $row['tId']);
		}
		$this->Prints("RecycleBin");
	}

	function build($k, $v) {
		return "`".$k."` = ".db::escape($v);
	}

}