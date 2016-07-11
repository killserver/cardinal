<?php
/*
 *
 * @version 3.0
 * @copyright 2014-2016 KilleR for Cardinal Engine
 * @author KilleR
 *
 * Version Engine: 3.0
 * Version File: 1.1
 *
 * 1.1
 * add support working with db as model database
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

final class ModelDB {
	
	private $data = array();
	private $table = array();
	private $replace = array("\"" => "\\\"");
	private $loaded = true;
	
	function __construct($table, $id = null, $unique = array(), $mode = "select", $search = "=", $cols = "*") {
		if(sizeof($unique)==0) {
			$unique = array("id");
		}
		if(!is_array($unique)) {
			$unique = array($unique);
		}
		if(is_array($cols)) {
			$cols = implode(",", array_map(function($data) { return "`".$data."`"; }, $cols));
		}
		$this->table = array("table" => $table, "id" => $id, "unique" => $unique, "search" => $search);
		$rows = array();
		if(!empty($id) && $mode == "select") {
			db::doquery("SELECT ".$cols." FROM `".$table."` WHERE `".$unique[0]."` ".$this->table['search']." ".$id, true);
			if(db::num_rows()==0) {
				$this->loaded = false;
				return;
			}
			if(db::num_rows()>1) {
				$rows = array();
				while($row = db::fetch_assoc()) {
					$rows[] = $row;
				}
				db::free();
				for($i=0;$i<sizeof($rows);$i++) {
					$this->data[] = new ModelDB($table, $rows[$i][$unique[1]], $unique[1], $mode, $this->table['search']);
				}
			} else {
				$db = db::fetch_assoc();
				db::free();
				db::doquery("SHOW COLUMNS FROM ".$table, true);
				if(db::num_rows()==0) {
					$this->loaded = false;
					return;
				}
				while($row = db::fetch_assoc()) {
					if(isset($db[$row['Field']])) {
						$this->data[$row['Field']] = $db[$row['Field']];
					} else {
						$this->data[$row['Field']] = ($row['Null']=="NO" ? "" : null);
					}
				}
				db::free();
			}
		} else {
			db::doquery("SHOW COLUMNS FROM ".$table, true);
			if(db::num_rows()==0) {
				$this->loaded = false;
				return;
			}
			while($row = db::fetch_assoc()) {
				$this->data[$row['Field']] = ($row['Null']=="NO" ? "" : null);
			}
			db::free();
		}
	}
	
	function loaded() {
		return $this->loaded;
	}
	
	function Get($name) {
		if(isset($this->table[$name])) {
			return $this->table[$name];
		} else {
			return false;
		}
	}
	
	function Replace($find) {
		if(!is_array($find)) {
			$find = array($find);
		}
		$this->replace = array_merge($this->replace, $find);
	}
	
	function __get($name) {
		if(isset($this->data[$name])) {
			return $this->data[$name];
		} else {
			return false;
		}
	}
	
	function __set($name, $val) {
		$this->data[$name] = $val;
	}
	
	function __getAll($name = "") {
		$arr = array();
		if(is_array($this->data) && isset($this->data[0]) && is_object($this->data[0])) {
			for($i=0;$i<sizeof($this->data);$i++) {
				$arr[$name.$i] = $this->data[$i]->$name;
			}
		} elseif(!empty($name)) {
			if(isset($this->data[$name])) {
				$arr[$name] = $this->data[$name];
			} else {
				return $arr;
			}
		} else {
			foreach($this->data as $key => $val) {
				$arr[$key] = $val;
			}
		}
		return $arr;
	}
	
	function Update() {
		if((!isset($this->table['table']) || empty($this->table['table'])) || (!isset($this->table['unique'][0]) || empty($this->table['unique'][0])) || (!isset($this->table['id']) || empty($this->table['id']))) {
			return false;
		}
		$key = array_keys($this->data);
		$val = array_values($this->data);
		$unique = $this->table['unique'][0];
		if(!isset($this->table['search']) || empty($this->table['search'])) {
			$this->table['search'] = "=";
		}
		$id = $this->table['id'];
		for($i=0;$i<sizeof($key);$i++) {
			if($unique != $key[$i]) {
				db::query("UPDATE `".$this->table['table']."` SET `".$key[$i]."` = ".($val[$i]=="UNIX_TIMESTAMP()" ? "UNIX_TIMESTAMP()" : "\"".str_replace(array_keys($this->replace), array_values($this->replace), $val[$i])."\"")." WHERE `".$unique."` ".$this->table['search']." ".$id.";");
			}
		}
		return true;
	}
	
	function Delete() {
		if((!isset($this->table['table']) || empty($this->table['table'])) || (!isset($this->table['unique'][0]) || empty($this->table['unique'][0])) || (!isset($this->table['id']) || empty($this->table['id']))) {
			return false;
		}
		$unique = $this->table['unique'][0];
		if(!isset($this->table['search']) || empty($this->table['search'])) {
			$this->table['search'] = "=";
		}
		$id = $this->table['id'];
		db::query("DELETE FROM `".$this->table['table']."`  WHERE `".$unique."` ".$this->table['search']." ".$id.";");
		return true;
	}
	
	function Insert() {
		if((!isset($this->table['table']) || empty($this->table['table'])) || (!isset($this->table['unique'][0]) || empty($this->table['unique'][0]))) {
			return false;
		}
		db::query("INSERT INTO `".$this->table['table']."` VALUES();");
		$this->table['id'] = $this->{$this->table['unique'][0]} = (intval(db::last_id($this->table['table']))-1);
		$key = array_keys($this->data);
		$val = array_values($this->data);
		$unique = $this->table['unique'][0];
		if(!isset($this->table['search']) || empty($this->table['search'])) {
			$this->table['search'] = "=";
		}
		$id = $this->table['id'];
		for($i=0;$i<sizeof($key);$i++) {
			if($unique != $key[$i]) {
				db::query("UPDATE `".$this->table['table']."` SET `".$key[$i]."` = ".($val[$i]=="UNIX_TIMESTAMP()" ? "UNIX_TIMESTAMP()" : "\"".str_replace(array_keys($this->replace), array_values($this->replace), $val[$i])."\"")." WHERE `".$unique."` ".$this->table['search']." ".$id.";");
			}
		}
		return true;
	}
	
}

?>