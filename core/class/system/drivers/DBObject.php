<?php
/*
 *
 * @version 4.1
 * @copyright 2014-2016 KilleR for Cardinal Engine
 *
 * Version Engine: 4.1
 * Version File: 1
 *
 * 1.1
 * add support return array all property
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

class DBObject {
	
	private $loadedTable = "";

    final public function getArray() {
		$ret = get_object_vars($this);
		if(isset($ret['loadedTable'])) {
			unset($ret['loadedTable']);
		}
        return $ret;
    }
	
	final public function getFirst() {
		$ret = get_object_vars($this);
		if(isset($ret['loadedTable'])) {
			unset($ret['loadedTable']);
		}
		$ret = array_keys($ret);
		$first = current($ret);
		return $first;
	}
	
	final public function SetTable($table) {
		$this->loadedTable = $table;
	}
	
	final public function loadTable($name = "") {
		if(empty($this->loadedTable) && empty($name)) {
			throw new Exception("Table for insert is not set or empty");
			die();
		}
		$row = db::doquery("SELECT EXISTS(SELECT 1 FROM `information_schema`.`tables` WHERE `table_schema` = '".db::$dbName."' AND `table_name` = '".$name."') AS `exists`");
		if(!isset($row['exists']) || $row['exists'] != 1) {
			throw new Exception("Table is not exists");
			die();
		}
		if(empty($name)) {
			$name = $this->loadedTable;
		} else {
			$this->loadedTable = $name;
		}
		db::doquery("SELECT * FROM `".db::$dbName."`.`".$name."` LIMIT 1", true);
		$row = db::fetch_assoc();
		foreach($row as $k => $v) {
			$this->{$k} = "";
		}
	}
	
	final public function Select($table = "", $where = "", $orderBy = "", $limit = "") {
		if(empty($this->loadedTable) && empty($table)) {
			throw new Exception("Table for insert is not set or empty");
			die();
		}
		$keys = get_object_vars($this);
		if(isset($keys["loadedTable"])) {
			unset($keys["loadedTable"]);
		}
		$keys = array_keys($keys);
		if(sizeof($keys)==0) {
			throw new Exception("Fields is not set");
			die();
		}
		if(empty($table)) {
			$table = $this->loadedTable;
		} else {
			$this->loadedTable = $table;
		}
		$rel = db::doquery("SELECT ".implode(", ", array_map(function($data) { return "`".$data."`"; }, $keys))." FROM `".$table."`".(!empty($where) ? " WHERE ".$where : "").(!empty($orderBy) ? " ORDER BY ".$orderBy : "").(!empty($limit) ? " LIMIT ".$limit : ""), true);
		if(db::num_rows($rel) <= 1) {
			return db::fetch_object($rel, get_class($this));
		} else {
			$arr = array();
			while($row = db::fetch_object($rel, get_class($this))) {
				$arr[] = $row;
			}
			return $arr;
		}
	}

    final public function Time() {
        $r = db::doquery("SELECT UNIX_TIMESTAMP() AS `time`");
        return $r['time'];
    }

    final public function Insert($table = "") {
		if(empty($this->loadedTable) && empty($table)) {
			throw new Exception("Table for insert is not set or empty");
			die();
		}
        $arr = get_object_vars($this);
		if(isset($arr["loadedTable"])) {
			unset($arr["loadedTable"]);
		}
		if(sizeof($arr)==0) {
			throw new Exception("Fields is not set");
			die();
		}
		if(empty($table)) {
			$table = $this->loadedTable;
		} else {
			$this->loadedTable = $table;
		}
        $key = array_keys($arr);
        $val = array_values($arr);
        return db::doquery("INSERT INTO `".$table."` (".implode(", ", array_map(function($d) { return "`".$d."`";}, $key)).") VALUES(".implode(", ", array_map(function($d) {return (strpos($d, "(")!==false&&strpos($d, ")")!==false ? $d : "'".db::escape($d)."'");}, $val)).")");
    }

    final public function Update($table = "", $where) {
		if(empty($this->loadedTable) && empty($table)) {
			throw new Exception("Table for update is not set or empty");
			die();
		}
        $arr = get_object_vars($this);
		if(isset($arr["loadedTable"])) {
			unset($arr["loadedTable"]);
		}
		if(sizeof($arr)==0) {
			throw new Exception("Fields is not set");
			die();
		}
		if(empty($table)) {
			$table = $this->loadedTable;
		} else {
			$this->loadedTable = $table;
		}
        $key = array_keys($arr);
        $val = array_values($arr);
        return db::doquery("UPDATE `".$table."` SET ".implode(", ", array_map(function($k, $v) { return "`".$k."` = ".(strpos($v, "(")!==false&&strpos($v, ")")!==false ? $v : "'".db::escape($v)."'");}, $key, $val))." WHERE ".$where);
    }

}

?>