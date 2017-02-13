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
	private $where = array();
	private $limit = 1;
	private $orderBy = array();
	private $Attributes = array();
	private $selectAdd = array();
	private $setAttrFor = array();
	private $allowEmptyAttr = false;
	
	final private function UnSetAll(&$ret) {
		if(isset($ret['loadedTable'])) {
			unset($ret['loadedTable']);
		}
		if(isset($ret['where'])) {
			unset($ret['where']);
		}
		if(isset($ret['limit'])) {
			unset($ret['limit']);
		}
		if(isset($ret['orderBy'])) {
			unset($ret['orderBy']);
		}
		if(isset($ret['Attributes'])) {
			unset($ret['Attributes']);
		}
		if(isset($ret['addWhereModel'])) {
			unset($ret['addWhereModel']);
		}
		if(isset($ret['selectAdd'])) {
			unset($ret['selectAdd']);
		}
		if(isset($ret['setAttrFor'])) {
			unset($ret['setAttrFor']);
		}
		if(isset($ret['allowEmptyAttr'])) {
			unset($ret['allowEmptyAttr']);
		}
	}

    final public function getArray() {
		$ret = get_object_vars($this);
		$this->UnSetAll($ret);
		$arr = array_keys($ret);
		for($i=0;$i<sizeof($arr);$i++) {
			$ret[$arr[$i]] = $this->ToType($ret[$arr[$i]]);
		}
        return $ret;
    }
	
	final public function ToType(&$var) {
		if(is_array($var)) {
			settype($var, "array");
		} else if(is_bool($var)) {
			settype($var, "boolean");
		} else if(is_float($var+0)) {
			settype($var, "float");
		} else if(is_int($var) || is_integer($var) || is_numeric($var)) {
			settype($var, "integer");
		} else if(is_object($var)) {
			settype($var, "object");
		}
		return $var;
	}
	
	final private function TypeDataRebuild(&$arr) {
		return str_replace("'", "", $arr);
	}
	
	final public function getAttributes($table) {
		if(sizeof($this->Attributes)==0) {
			if(empty($table)) {
				throw new Exception("Table for get comments is not set or empty");
				die();
			}
			db::doquery("SHOW FULL COLUMNS FROM ".$table, true);
			while($row = db::fetch_assoc()) {
				$last = strpos($row['Type'], "(")!==false ? strpos($row['Type'], "(") : strlen($row['Type']);
				$typeData = array();
				if(strpos($row['Type'], "enum")!==false&&strpos($row['Type'], "(")!==false) {
					$cut = $row['Type'];
					$cut1 = strpos($cut, "(")+1;
					$cut1 = substr($cut, $cut1, -1);
					$typeData = array_map(array(&$this, "TypeDataRebuild"), explode(",", $cut1));
				}
				$comment = (isset($this->setAttrFor[$row['Field']]) && isset($this->setAttrFor[$row['Field']]['Comment']) ? $this->setAttrFor[$row['Field']]['Comment'] : $row['Comment']);
				$type = (isset($this->setAttrFor[$row['Field']]) && isset($this->setAttrFor[$row['Field']]['Type']) ? $this->setAttrFor[$row['Field']]['Type'] : substr($row['Type'], 0, $last));
				$default = (isset($this->setAttrFor[$row['Field']]) && isset($this->setAttrFor[$row['Field']]['Default']) ? $this->setAttrFor[$row['Field']]['Default'] : ($row['Null']=="NO" ? "" : $row['Default']));
				$typeData = (isset($this->setAttrFor[$row['Field']]) && isset($this->setAttrFor[$row['Field']]['typeData']) ? $this->setAttrFor[$row['Field']]['typeData'] : $typeData);
				$this->Attributes[$row['Field']] = array("comment" => $comment, "type" => $type, "default" => $default, "typeData" => $typeData);
			}
		}
		return $this->Attributes;
	}
	
	final public function setAttribute($field, $attr, $value) {
		if(empty($field) || empty($attr) || empty($value)) {
			throw new Exception("Data for attribute not set or empty");
			die();
		}
		if(!isset($this->setAttrFor[$field])) {
			$this->setAttrFor[$field] = array();
		}
		$this->setAttrFor[$field][$attr] = $value;
		return true;
	}
	
	final public function getAttribute($field, $attr, $table = "", $empty = false) {
		if(empty($table)) {
			$table = $this->loadedTable;
		}
		if(empty($table)) {
			throw new Exception("Table for get attribute is not set or empty");
			die();
		}
		$this->getAttributes($table);
		if(isset($this->Attributes[$field]) && isset($this->Attributes[$field][$attr])) {
			return $this->Attributes[$field][$attr];
		} elseif(isset($this->setAttrFor[$field]) && isset($this->setAttrFor[$field][$attr])) {
			return $this->setAttrFor[$field][$attr];
		} elseif(!$this->allowEmptyAttr) {
			throw new Exception("Attribute \"".$attr."\" for field \"".$field."\" is not found in ".$table);
			die();
		} else {
			return "";
		}
	}
	
	final public function switchAllowEmptyAttr($switch = "") {
		if($switch==="" && ($switch!==false || $switch!==true)) {
			throw new Exception("Switch for allowed empty attribute is not boolen");
			die();
		}
		if(!empty($switch)) {
			$this->allowEmptyAttr = $switch;
		}
		return $this->allowEmptyAttr;
	}
	
	final public function getComment($table, $name = "", $empty = false) {
		if(empty($name)) {
			$name = $table;
			$table = $this->loadedTable;
		}
		$this->getAttributes($table);
		if(isset($this->Attributes[$name]) && isset($this->Attributes[$name]["comment"])) {
			if($empty === false) {
				return "{L_\"".(!empty($this->Attributes[$name]["comment"]) ? $this->Attributes[$name]["comment"] : $name)."\"}";
			} else {
				return "{L_\"".(!empty($this->Attributes[$name]["comment"]) ? $this->Attributes[$name]["comment"] : "")."\"}";
			}
		} else {
			throw new Exception("Field \"".$name."\" is not found in set table");
			die();
		}
	}
	
	final public function getComments() {
		$table = $this->loadedTable;
		$ret = array();
		$obj = $this->getArray();
		foreach($obj as $field => $val) {
			$ret[$field] = $this->getComment($table, $field);
		}
		return $ret;
	}
	
	final public function SetLimit($limit) {
		if((!is_numeric($limit) || $limit == 0) && strpos($limit, ",")===false) {
			throw new Exception("Error numeric from limit");
			die();
		}
		$this->limit = $limit;
	}
	
	final public function OrderByTo($name, $type = "DESC") {
		$this->orderBy[][$name] = $type;
	}
	
	final public function WhereTo($name, $to = "", $val = "", $type = "AND") {
		if(empty($to)) {
			$to = $name;
			$name = "";
		}
		if(empty($name)) {
			$name = get_object_vars($this);
			$name = key($name);
		}
		if(empty($val)) {
			$val = $to;
			if(is_numeric($to)) {
				$to = "=";
			} else {
				$to = "LIKE";
			}
		}
		$this->where[][$type] = "`".$name."` ".$to." ".db::escape($val)."";
	}
	
	final private function ReleaseWhere($where = "") {
		$wheres = "";
		if(!empty($where)) {
			$wheres = $where;
		} elseif(sizeof($this->where)>0) {
			$listAll = $last = "";
			$where = $this->where;
			for($i=0;$i<sizeof($where);$i++) {
				list($arrK, $arrV) = each($where[$i]);
				$last = $arrK;
				$listAll .= $arrV." ".$last." ";
			}
			$wheres = substr($listAll, 0, 0-strlen(" ".$last." "));
		}
		return (!empty($wheres) ? " WHERE ".$wheres.(isset($this->addWhereModel) ? " AND ".$this->addWhereModel." " : " ") : "");
	}
	
	final private function ReleaseOrder($orderBy = "") {
		$orders = "";
		if(!empty($orderBy)) {
			$orders = $orderBy;
		} elseif(sizeof($this->orderBy)>0) {
			for($i=0;$i<sizeof($this->orderBy);$i++) {
				list($arrK, $arrV) = each($this->orderBy[$i]);
				$orders .= $arrK." ".$arrV.", ";
			}
			$orders = substr($orders, 0, 0-strlen(", "));
		}
		return (!empty($orders) ? " ORDER BY ".$orders." " : "");
	}
	
	final private function ReleaseLimit($limit = "") {
		$limits = "";
		if(!empty($limit)) {
			$limits = $limit;
		} elseif(!empty($this->limit)) {
			$limits = $this->limit;
		}
		if($limits<1) {
			$limits = "";
		}
		return (!empty($limits) ? " LIMIT ".$limits." " : "");
	}
	
	final public function getFirst() {
		$ret = get_object_vars($this);
		$this->UnSetAll($ret);
		$ret = array_keys($ret);
		$first = current($ret);
		return $first;
	}
	
	final public function SetTable($table) {
		$this->loadedTable = $table;
	}
	
	final public function loadTable($name = "") {
		if(empty($this->loadedTable) && empty($name)) {
			throw new Exception("Table for loading is not set or empty");
			die();
		}
		if(empty($name)) {
			$name = $this->loadedTable;
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
	
	final private function getFieldForSelect($data) {
		$save = "`";
		if(strpos($data, "(")!==false || strpos($data, "AS")!==false) {
			$save = "";
		}
		return $save.$data.$save;
	}
	
	final public function AddToSelect($field) {
		if(is_array($field) && sizeof($field)>0) {
			$arr = array_values($field);
			for($i=0;$i<sizeof($arr);$i++) {
				$this->selectAdd[$arr[$i]] = $arr[$i];
			}
		} else if(is_string($field) && !empty($field)) {
			$this->selectAdd[$field] = $field;
		} else {
			throw new Exception("First parameter not array or string");
			die();
		}
	}
	
	final public function Select($table = "", $where = "", $orderBy = "", $limit = "") {
		if(empty($this->loadedTable) && empty($table)) {
			throw new Exception("Table for select is not set or empty");
			die();
		}
		$keys = get_object_vars($this);
		$this->UnSetAll($keys);
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
		$keys = array_merge($keys, $this->selectAdd);
		$where = $this->ReleaseWhere($where);
		$orderBy = $this->ReleaseOrder($orderBy);
		$limit = $this->ReleaseLimit($limit);
		$rel = db::doquery("SELECT ".implode(", ", array_map(array(&$this, "getFieldForSelect"), $keys))." FROM `".$table."`".$where.$orderBy.$limit, true);
		if(db::num_rows($rel) <= 1) {
			$ret = db::fetch_object($rel, get_class($this));
			db::free($rel);
			return $ret;
		} else {
			$arr = array();
			while($row = db::fetch_object($rel, get_class($this))) {
				$arr[] = $row;
			}
			db::free($rel);
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
		$this->UnSetAll($arr);
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
        return db::doquery("INSERT INTO `".$table."` (".implode(", ", array_map(function($d) { return "`".$d."`";}, $key)).") VALUES(".implode(", ", array_map(function($d) {return (strpos($d, "(")!==false&&strpos($d, ")")!==false ? $d : "".db::escape($d)."");}, $val)).")");
    }

    final public function Update($table = "", $where = "", $orderBy = "", $limit = "") {
		if(empty($this->loadedTable) && empty($table)) {
			throw new Exception("Table for update is not set or empty");
			die();
		}
		if((!isset($this->where) || sizeof($this->where)==0) && empty($where)) {
			throw new Exception("Сonditions for update is not set or empty");
			die();
		}
        $arr = get_object_vars($this);
		$this->UnSetAll($arr);
		if(sizeof($arr)==0) {
			throw new Exception("Fields is not set");
			die();
		}
		foreach($arr as $k => $v) {
			if(!is_string($v)) {
				throw new Exception("Fields ".$k." is not string");
				die();
			}
		}
		if(empty($table)) {
			$table = $this->loadedTable;
		} else {
			$this->loadedTable = $table;
		}
		$where = $this->ReleaseWhere($where);
		$orderBy = $this->ReleaseOrder($orderBy);
		$limit = $this->ReleaseLimit($limit);
        $key = array_keys($arr);
        $val = array_values($arr);
        return db::doquery("UPDATE `".$table."` SET ".implode(", ", array_map(function($k, $v) { return "`".$k."` = ".(strpos($v, "(")!==false&&strpos($v, ")")!==false ? $v : "".db::escape($v)."");}, $key, $val)).$where.$orderBy.$limit);
    }

    final public function Deletes($table = "", $where = "", $orderBy = "", $limit = "") {
		if(empty($this->loadedTable) && empty($table)) {
			throw new Exception("Table for delete is not set or empty");
			die();
		}
		if((!isset($this->where) || sizeof($this->where)==0) && empty($where)) {
			throw new Exception("Сonditions for delete is not set or empty");
			die();
		}
		if(empty($table)) {
			$table = $this->loadedTable;
		} else {
			$this->loadedTable = $table;
		}
		$where = $this->ReleaseWhere($where);
		$orderBy = $this->ReleaseOrder($orderBy);
		$limit = $this->ReleaseLimit($limit);
		if((!isset($this->where) || sizeof($this->where)==0) && empty($where)) {
			throw new Exception("Сonditions for delete is not set or empty");
			die();
		}
        return db::doquery("DELETE FROM `".$table."` ".$where.$orderBy.$limit);
    }
	
	final public function addField($name, $val = "") {
		$this->{$name} = $val;
		return true;
	}
	
	final public function removeField($name) {
		unset($this->{$name});
		return true;
	}

}

?>