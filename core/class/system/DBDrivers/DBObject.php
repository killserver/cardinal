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

class DBObject implements ArrayAccess {
	
	private $loadedTable = "";
	private $groupBy = "";
	private $where = array();
	private $limit = 1;
	private $offset = 0;
	private $orderBy = array();
	private $Attributes = array();
	private $selectAdd = array();
	private $setAttrFor = array();
	private $allowEmptyAttr = true;
	private $pseudoFields = array();
	private $multiple = false;
	private static $usedCache = false;
	private $listAdd = array();
	
	final public function getInstance($notClearTable = false) {
		$th = clone $this;
		$rt = get_object_vars($th);
		foreach($rt as $k => $v) {
			$th->{$k} = "";
		}
		if($notClearTable===false) {
			$th->loadedTable = "";
		} else {
			$th->loadedTable = $this->loadedTable;
		}
		$th->groupBy = "";
		$th->where = array();
		$th->limit = 1;
		$th->offset = 0;
		$th->orderBy = array();
		$th->Attributes = array();
		$th->selectAdd = array();
		$th->setAttrFor = array();
		$th->allowEmptyAttr = true;
		$th->pseudoFields = array();
		$th->multiple = false;
		$th->listAdd = array();
		return $th;
	}
	
	final public function useCache($cache = true) {
		self::$usedCache = $cache;
		return true;
	}
	
	final private function clearCache($table) {
		if(!defined("PATH_CACHE_SYSTEM") || empty(PATH_CACHE_SYSTEM)) {
			return false;
		}
		if(is_dir(PATH_CACHE_SYSTEM)) {
			if($dh = dir(PATH_CACHE_SYSTEM)) {
				while(($file = $dh->read()) !== false) {
					if(strpos($file, $table."_") !== false) {
						unlink(PATH_CACHE_SYSTEM.$file);
					}
				}
			}
		}
		return true;
	}
	
	final private function UnSetAll(&$ret) {
		if(isset($ret['loadedTable'])) {
			unset($ret['loadedTable']);
		}
		if(isset($ret['groupBy'])) {
			unset($ret['groupBy']);
		}
		if(isset($ret['pathForUpload'])) {
			unset($ret['pathForUpload']);
		}
		if(isset($ret['multiple'])) {
			unset($ret['multiple']);
		}
		if(isset($ret['where'])) {
			unset($ret['where']);
		}
		if(isset($ret['limit'])) {
			unset($ret['limit']);
		}
		if(isset($ret['offset'])) {
			unset($ret['offset']);
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
		if(isset($ret['pseudoFields'])) {
			unset($ret['pseudoFields']);
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
		if(isset($ret['listAdd'])) {
			unset($ret['listAdd']);
		}
	}
	
	final private function addPrefixTable($query, $addSave = "`", $force = false) {
		if($addSave===false) {
			$save = "";
		} else if($addSave===true) {
			$save = "`";
		} else {
			$save = $addSave;
		}
		if($force === true && defined("PREFIX_DB") && !empty(PREFIX_DB) && strpos($query, PREFIX_DB)===false) {
			$arr = db::getTables();
			$arr = array_keys($arr);
			for($i=0;$i<sizeof($arr);$i++) {
				$t = str_replace(PREFIX_DB, "", $arr[$i]);
				$query = str_replace(array($arr[$i], $t), $save.PREFIX_DB.$t.$save, $query);
			}
		} else if(strpos($query, '{{') !== false && defined("PREFIX_DB") && !empty(PREFIX_DB) && strpos($query, PREFIX_DB)===false) {
			if(preg_match("/CREATE|DROP/", $query)) {
				$query = str_replace(array('{{', '}}'), array($save.PREFIX_DB, $save), $query);
			} else {
				$arr = db::getTables();
				$arr = array_keys($arr);
				for($i=0;$i<sizeof($arr);$i++) {
					$t = str_replace(PREFIX_DB, "", $arr[$i]);
					$query = str_replace(array('{{'.$arr[$i].'}}', '{{'.$t.'}}'), $save.PREFIX_DB.$t.$save, $query);
				}
			}
		} else if(strpos($query, '{{') !== false) {
			$query = str_replace(array("{{", "}}"), $save, $query);
		} else if($addSave===false) {
			$query = $save.$query.$save;
		}
		return $query;
	}
	
	final public function addPrefix($query, $force = false) {
		return $this->addPrefixTable($query, true, $force);
	}

	final public function getArray() {
		$ret = get_object_vars($this);
		$this->UnSetAll($ret);
		$arr = array_keys($ret);
		for($i=0;$i<sizeof($arr);$i++) {
			$ret[$arr[$i]] = $this->ToType($ret[$arr[$i]]);
		}
		foreach($this->pseudoFields as $k => $v) {
			$ret[$k] = $v;
		}
		return $ret;
	}
	
	final public function getClean($th = "") {
		if($th==="") {
			$th = $this;
		}
		if(!is_array($th)) {
			if(isset($th->loadedTable)) {
				unset($th->loadedTable);
			}
			if(isset($th->groupBy)) {
				unset($th->groupBy);
			}
			if(isset($th->pathForUpload)) {
				unset($th->pathForUpload);
			}
			if(isset($th->multiple)) {
				unset($th->multiple);
			}
			if(isset($th->where)) {
				unset($th->where);
			}
			if(isset($th->limit)) {
				unset($th->limit);
			}
			if(isset($th->offset)) {
				unset($th->offset);
			}
			if(isset($th->orderBy)) {
				unset($th->orderBy);
			}
			if(isset($th->Attributes)) {
				unset($th->Attributes);
			}
			if(isset($th->addWhereModel)) {
				unset($th->addWhereModel);
			}
			if(isset($th->pseudoFields)) {
				unset($th->pseudoFields);
			}
			if(isset($th->selectAdd)) {
				unset($th->selectAdd);
			}
			if(isset($th->setAttrFor)) {
				unset($th->setAttrFor);
			}
			if(isset($th->allowEmptyAttr)) {
				unset($th->allowEmptyAttr);
			}
			if(isset($th->listAdd)) {
				unset($th->listAdd);
			}
		} else {
			for($i=0;$i<sizeof($th);$i++) {
				$th[$i] = $th[$i]->getClean();
			}
		}
		return $th;
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
				if(!isset($_SERVER['HTTP_CF_VISITOR'])) {
					header("HTTP/1.0 520 Unknown Error");
				} else {
					header("HTTP/1.0 404 Not found");
				}
				throw new Exception("Table for get comments is not set or empty");
				die();
			}
			db::doquery("SHOW FULL COLUMNS FROM ".$this->addPrefixTable($table), true);
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
				$default = (isset($this->setAttrFor[$row['Field']]) && isset($this->setAttrFor[$row['Field']]['Default']) ? $this->setAttrFor[$row['Field']]['Default'] : $row['Default']);
				$typeData = (isset($this->setAttrFor[$row['Field']]) && isset($this->setAttrFor[$row['Field']]['typeData']) ? $this->setAttrFor[$row['Field']]['typeData'] : $typeData);
				$this->Attributes[$row['Field']] = array("comment" => $comment, "type" => $type, "default" => $default, "typeData" => $typeData);
			}
		}
		return $this->Attributes;
	}
	
	final public function setAttribute($field, $attr, $value) {
		if(empty($field) || empty($attr) || empty($value)) {
			if(!isset($_SERVER['HTTP_CF_VISITOR'])) {
				header("HTTP/1.0 520 Unknown Error");
			} else {
				header("HTTP/1.0 404 Not found");
			}
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
			if(!isset($_SERVER['HTTP_CF_VISITOR'])) {
				header("HTTP/1.0 520 Unknown Error");
			} else {
				header("HTTP/1.0 404 Not found");
			}
			throw new Exception("Table for get attribute is not set or empty");
			die();
		}
		$this->getAttributes($table);
		if(isset($this->setAttrFor[$field]) && isset($this->setAttrFor[$field][$attr])) {
			return $this->setAttrFor[$field][$attr];
		} elseif(isset($this->Attributes[$field]) && isset($this->Attributes[$field][$attr])) {
			return $this->Attributes[$field][$attr];
		} elseif(!$this->allowEmptyAttr) {
			if(!isset($_SERVER['HTTP_CF_VISITOR'])) {
				header("HTTP/1.0 520 Unknown Error");
			} else {
				header("HTTP/1.0 404 Not found");
			}
			throw new Exception("Attribute \"".$attr."\" for field \"".$field."\" is not found in ".$table);
			die();
		} else {
			return "";
		}
	}
	
	final public function switchAllowEmptyAttr($switch = "") {
		if($switch==="" && ($switch!==false || $switch!==true)) {
			if(!isset($_SERVER['HTTP_CF_VISITOR'])) {
				header("HTTP/1.0 520 Unknown Error");
			} else {
				header("HTTP/1.0 404 Not found");
			}
			throw new Exception("Switch for allowed empty attribute is not boolen");
			die();
		}
		if($switch!=="") {
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
		if(isset($this->pseudoFields[$name]) || (isset($this->Attributes[$name]) && isset($this->Attributes[$name]["comment"]))) {
			if($empty === false) {
				return (defined("ADMINCP_DIRECTORY") ? "{L_\"" : "").(!empty($this->Attributes[$name]["comment"]) ? $this->Attributes[$name]["comment"] : $name).(defined("ADMINCP_DIRECTORY") ? "\"}" : "");
			} else {
				return (defined("ADMINCP_DIRECTORY") ? "{L_\"" : "").(!empty($this->Attributes[$name]["comment"]) ? $this->Attributes[$name]["comment"] : "").(defined("ADMINCP_DIRECTORY") ? "\"}" : "");
			}
		} else {
			if(!isset($_SERVER['HTTP_CF_VISITOR'])) {
				header("HTTP/1.0 520 Unknown Error");
			} else {
				header("HTTP/1.0 404 Not found");
			}
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
	
	final public function SetLimit($limit, $move = false) {
		if((!is_numeric($limit) || $limit == 0) && strpos($limit, ",")===false) {
			if(!isset($_SERVER['HTTP_CF_VISITOR'])) {
				header("HTTP/1.0 520 Unknown Error");
			} else {
				header("HTTP/1.0 404 Not found");
			}
			throw new Exception("Error numeric from limit");
			die();
		}
		if($move!==false) {
			$limit = $move.", ".$limit;
		}
		$this->limit = $limit;
	}
	
	final public function SetOffset($offset) {
		if((!is_numeric($offset) || $offset < -1)) {
			if(!isset($_SERVER['HTTP_CF_VISITOR'])) {
				header("HTTP/1.0 520 Unknown Error");
			} else {
				header("HTTP/1.0 404 Not found");
			}
			throw new Exception("Error numeric from offset");
			die();
		}
		$this->offset = $offset;
	}
	
	final public function OrderByTo($name, $type = "DESC") {
		$this->orderBy[][$name] = $type;
	}
	
	final public function OrderBy($name, $type = "DESC") {
		return self::OrderByTo($name, $type);
	}
	
	final public function Where($name, $to = false, $val = false, $type = "AND") {
		return $this->WhereTo($name, $to, $val, $type);
	}
	
	final public function WhereTo($name, $to = false, $val = false, $type = "AND") {
		if($to===false) {
			$to = $name;
			$name = "";
		}
		if(empty($name)) {
			$name = get_object_vars($this);
			$name = key($name);
		}
		if($val===false) {
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
	
	final private function ReleaseGroupBy($groupBy = "") {
		$groupbys = "";
		if($groupBy!=="") {
			$groupbys = $groupBy;
		} elseif($this->groupBy!=="") {
			$groupbys = $this->groupBy;
		}

		return (!empty($groupbys) ? " GROUP BY `".$groupbys."` " : "");
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
		return (!empty($limits) && $this->multiple===false ? " LIMIT ".$limits." " : "");
	}
	
	final private function ReleaseOffset($offset = "") {
		$offsets = "";
		if(!empty($offset)) {
			$offsets = $offset;
		} elseif(!empty($this->offset)) {
			$offsets = $this->offset;
		}
		if($offsets<1) {
			$offsets = "";
		}
		return (!empty($offsets) ? " OFFSET ".$offsets." " : "");
	}
	
	final public function getFirst() {
		$ret = get_object_vars($this);
		$this->UnSetAll($ret);
		$ret = array_keys($ret);
		$first = current($ret);
		return $first;
	}
	
	final public function SetTable($table) {
		$this->loadedTable = $this->addPrefix($table, true);
	}
	
	final public function loadTable($name = "") {
		if(empty($this->loadedTable) && empty($name)) {
			if(!isset($_SERVER['HTTP_CF_VISITOR'])) {
				header("HTTP/1.0 520 Unknown Error");
			} else {
				header("HTTP/1.0 404 Not found");
			}
			throw new Exception("Table for loading is not set or empty");
			die();
		}
		if(empty($name)) {
			$name = $this->loadedTable;
		}
		$name = $this->addPrefixTable($name, "");
		if(substr($name, 0, 1)==="`") {
			$name = substr($name, 1, -1);
		}
		$row = db::doquery("SELECT EXISTS(SELECT 1 FROM `information_schema`.`tables` WHERE `table_schema` = '".db::$dbName."' AND `table_name` = '".$name."') AS `exists`");
		if(!isset($row['exists']) || $row['exists'] != 1) {
			if(!isset($_SERVER['HTTP_CF_VISITOR'])) {
				header("HTTP/1.0 520 Unknown Error");
			} else {
				header("HTTP/1.0 404 Not found");
			}
			throw new Exception("Table ".$name." is not exists");
			die();
		}
		if(empty($name)) {
			$name = $this->loadedTable;
		} else {
			$this->loadedTable = $name;
		}
		$row = db::select_query("SHOW COLUMNS FROM `".db::$dbName."`.".$this->addPrefixTable($name));
		foreach($row as $k => $v) {
			$this->{$v['Field']} = "";
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
			if(!isset($_SERVER['HTTP_CF_VISITOR'])) {
				header("HTTP/1.0 520 Unknown Error");
			} else {
				header("HTTP/1.0 404 Not found");
			}
			throw new Exception("First parameter not array or string");
			die();
		}
	}
	
	final public function Select($table = "", $where = "", $orderBy = "", $limit = "", $offset = "", $groupby = "") {
		if(empty($this->loadedTable) && empty($table)) {
			throw new Exception("Table for select is not set or empty");
			die();
		}
		$keys = get_object_vars($this);
		$this->UnSetAll($keys);
		$keys = array_keys($keys);
		if(sizeof($keys)==0) {
			if(!isset($_SERVER['HTTP_CF_VISITOR'])) {
				header("HTTP/1.0 520 Unknown Error");
			} else {
				header("HTTP/1.0 404 Not found");
			}
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
		$offset = $this->ReleaseOffset($offset);
		$groupby = $this->ReleaseGroupBy($groupby);
		$table = $this->addPrefixTable($table);
		$sql = "SELECT ".implode(", ", array_map(array(&$this, "getFieldForSelect"), $keys))." FROM ".$table.$where.$groupby.$orderBy.$limit.$offset;
		if(defined("PATH_CACHE_SYSTEM") && !empty(PATH_CACHE_SYSTEM)) {
			$fileCache = PATH_CACHE_SYSTEM.$table."_".md5($sql).".cache";
		}
		$cached = false;
		if(defined("PATH_CACHE_SYSTEM") && !empty(PATH_CACHE_SYSTEM) && file_exists(PATH_CACHE_SYSTEM) && is_writeable(PATH_CACHE_SYSTEM) && is_bool(self::$usedCache) && self::$usedCache===true) {
			$cached = true;
		}
		if($cached && isset($fileCache) && file_exists($fileCache)) {
			$file = file_get_contents($fileCache);
			$result = unserialize($file);
			return $result;
		}
		$rel = db::doquery($sql, true);
		if(!$this->multiple && db::num_rows($rel) <= 1) {
			$ret = db::fetch_object($rel, get_class($this));
			if(is_object($ret)) {
				foreach($this->pseudoFields as $k => $v) {
					$ret->{$k} = $v;
				}
			}
			db::free($rel);
			if($cached && isset($fileCache)) {
				$cacheData = serialize($ret);
				file_put_contents($fileCache, $cacheData);
			}
			return $ret;
		} else {
			$arr = array();
			while($row = db::fetch_object($rel, get_class($this))) {
				if(is_object($row)) {
					foreach($this->pseudoFields as $k => $v) {
						$row->{$k} = $v;
					}
				}
				$arr[] = $row;
			}
			db::free($rel);
			if($cached && isset($fileCache)) {
				$cacheData = serialize($arr);
				file_put_contents($fileCache, $cacheData);
			}
			return $arr;
		}
	}

	final public function multiple($val = "") {
		$this->multiple = ($val === "" ? (!$this->multiple ? true : false) : $val);
	}

	final public function getSelectQuery($table = "", $where = "", $orderBy = "", $limit = "", $offset = "", $groupby = "") {
		if(empty($this->loadedTable) && empty($table)) {
			if(!isset($_SERVER['HTTP_CF_VISITOR'])) {
				header("HTTP/1.0 520 Unknown Error");
			} else {
				header("HTTP/1.0 404 Not found");
			}
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
		$groupby = $this->ReleaseGroupBy($groupby);
		$offset = $this->ReleaseOffset($offset);
		return "SELECT ".implode(", ", array_map(array(&$this, "getFieldForSelect"), $keys))." FROM ".$this->addPrefixTable($table).$where.$groupby.$orderBy.$limit.$offset;
	}

	final public function getMax($table = "", $where = "", $orderBy = "", $limit = "", $offset = "", $groupby = "") {
		if(empty($this->loadedTable) && empty($table)) {
			if(!isset($_SERVER['HTTP_CF_VISITOR'])) {
				header("HTTP/1.0 520 Unknown Error");
			} else {
				header("HTTP/1.0 404 Not found");
			}
			throw new Exception("Table for select is not set or empty");
			die();
		}
		$keys = get_object_vars($this);
		$this->UnSetAll($keys);
		$keys = array_keys($keys);
		if(sizeof($keys)==0) {
			if(!isset($_SERVER['HTTP_CF_VISITOR'])) {
				header("HTTP/1.0 520 Unknown Error");
			} else {
				header("HTTP/1.0 404 Not found");
			}
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
		$groupby = $this->ReleaseGroupBy($groupby);
		$offset = $this->ReleaseOffset($offset);
		$rel = db::doquery("SELECT COUNT(".current($keys).") AS `max` FROM ".$this->addPrefixTable($table).$groupby.$where.$orderBy.$limit.$offset);
		return $rel['max'];
	}

	final public function Time() {
		if(db::connected()) {
			$r = db::doquery("SELECT UNIX_TIMESTAMP() AS `time`");
			return $r['time'];
		} else {
			return time();
		}
	}

	final public function groupBy($field) {
		$this->groupBy = $field;
		return true;
	}

	final public function Insert($table = "") {
		if(empty($this->loadedTable) && empty($table)) {
			if(!isset($_SERVER['HTTP_CF_VISITOR'])) {
				header("HTTP/1.0 520 Unknown Error");
			} else {
				header("HTTP/1.0 404 Not found");
			}
			throw new Exception("Table for insert is not set or empty");
			die();
		}
		$arr = get_object_vars($this);
		$this->UnSetAll($arr);
		if(sizeof($arr)==0) {
			if(!isset($_SERVER['HTTP_CF_VISITOR'])) {
				header("HTTP/1.0 520 Unknown Error");
			} else {
				header("HTTP/1.0 404 Not found");
			}
			throw new Exception("Fields is not set");
			die();
		}
		$forUpdate = array();
		foreach($arr as $k => $v) {
			if($v!=="") {
				$forUpdate[$k] = $v;
			}
		}
		if(empty($table)) {
			$table = $this->loadedTable;
		} else {
			$this->loadedTable = $table;
		}
		$key = array_keys($forUpdate);
		$val = array_values($forUpdate);
		$table = $this->addPrefixTable($table);
		$this->clearCache($table);
		if(sizeof($this->listAdd)>0) {
			$keys = array_keys($this->listAdd);
			$vals = array_values($this->listAdd);
			$key = array_merge($key, $keys);
			$val = array_merge($val, $vals);
		}
		return db::doquery("INSERT INTO ".$table." (".implode(", ", array_map(array(&$this, "buildKeyIn"), $key)).") VALUES(".implode(", ", array_map(array(&$this, "buildValueIn"), $val)).")");
	}
	
	final private function buildKeyIn($d) {
		return "`".$d."`";
	}
	
	final private function buildValueIn($d) {
		return ("".str_replace("\\\\u", "\\u", db::escape($d))."");
	}

	final public function Update($table = "", $where = "", $orderBy = "", $limit = "", $groupby = "") {
		if(empty($this->loadedTable) && empty($table)) {
			if(!isset($_SERVER['HTTP_CF_VISITOR'])) {
				header("HTTP/1.0 520 Unknown Error");
			} else {
				header("HTTP/1.0 404 Not found");
			}
			throw new Exception("Table for update is not set or empty");
			die();
		}
		if((!isset($this->where) || sizeof($this->where)==0) && empty($where)) {
			if(!isset($_SERVER['HTTP_CF_VISITOR'])) {
				header("HTTP/1.0 520 Unknown Error");
			} else {
				header("HTTP/1.0 404 Not found");
			}
			throw new Exception("Сonditions for update is not set or empty");
			die();
		}
		$arr = get_object_vars($this);
		$this->UnSetAll($arr);
		if(sizeof($arr)==0) {
			if(!isset($_SERVER['HTTP_CF_VISITOR'])) {
				header("HTTP/1.0 520 Unknown Error");
			} else {
				header("HTTP/1.0 404 Not found");
			}
			throw new Exception("Fields is not set");
			die();
		}
		$forUpdate = array();
		foreach($arr as $k => $v) {
			if($v!==""&&$v!==null) {
				if(!is_string($v) && !is_numeric($v)) {
					if(!isset($_SERVER['HTTP_CF_VISITOR'])) {
						header("HTTP/1.0 520 Unknown Error");
					} else {
						header("HTTP/1.0 404 Not found");
					}
					throw new Exception("Fields ".$k." is not string and not number");
					die();
				}
				$forUpdate[$k] = $v;
			}
		}
		if(empty($table)) {
			$table = $this->loadedTable;
		} else {
			$this->loadedTable = $table;
		}
		$where = $this->ReleaseWhere($where);
		$orderBy = $this->ReleaseOrder($orderBy);
		$groupby = $this->ReleaseGroupBy($groupby);
		$limit = $this->ReleaseLimit($limit);
		$key = array_keys($forUpdate);
		$val = array_values($forUpdate);
		$table = $this->addPrefixTable($table);
		$this->clearCache($table);
		if(sizeof($this->listAdd)>0) {
			$keys = array_keys($this->listAdd);
			$vals = array_values($this->listAdd);
			$key = array_merge($key, $keys);
			$val = array_merge($val, $vals);
		}
		return db::doquery("UPDATE ".$table." SET ".implode(", ", array_map(array(&$this, "buildUpdateKV"), $key, $val)).$where.$groupby.$orderBy.$limit);
	}
	
	final private function buildUpdateKV($k, $v) {
		return "`".$k."` = ".("".str_replace("\\\\u", "\\u", db::escape($v))."");
	}

	final public function Deletes($table = "", $where = "", $orderBy = "", $limit = "", $groupby = "") {
		if(empty($this->loadedTable) && empty($table)) {
			if(!isset($_SERVER['HTTP_CF_VISITOR'])) {
				header("HTTP/1.0 520 Unknown Error");
			} else {
				header("HTTP/1.0 404 Not found");
			}
			throw new Exception("Table for delete is not set or empty");
			die();
		}
		if((!isset($this->where) || sizeof($this->where)==0) && empty($where)) {
			if(!isset($_SERVER['HTTP_CF_VISITOR'])) {
				header("HTTP/1.0 520 Unknown Error");
			} else {
				header("HTTP/1.0 404 Not found");
			}
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
		$groupby = $this->ReleaseGroupBy($groupby);
		if((!isset($this->where) || sizeof($this->where)==0) && empty($where)) {
			if(!isset($_SERVER['HTTP_CF_VISITOR'])) {
				header("HTTP/1.0 520 Unknown Error");
			} else {
				header("HTTP/1.0 404 Not found");
			}
			throw new Exception("Сonditions for delete is not set or empty");
			die();
		}
		$table = $this->addPrefixTable($table);
		$this->clearCache($table);
		return db::doquery("DELETE FROM ".$table." ".$where.$groupby.$orderBy.$limit);
	}
	
	final public function addField($name, $val = "") {
		$this->listAdd[$name] = $val;
		$this->{$name} = $val;
		return true;
	}
	
	final public function removeField($name) {
		unset($this->{$name});
		return true;
	}
	
	final public function addPseudoField($name, $val = "") {
		$this->pseudoFields[$name] = $val;
		return true;
	}
	
	final public function removePseudoField($name) {
		unset($this->pseudoFields[$name]);
		return true;
	}
	
	public function offsetSet($offset, $value) {
		if(is_null($offset)) {
			$this->nulled[] = $value;
		} else {
			$this->{$offset} = $value;
		}
    }
	
	public function offsetExists($offset) {
		return isset($this->{$offset});
	}
	
	public function offsetUnset($offset) {
		unset($this->{$offset});
	}
	
	public function offsetGet($offset) {
		return isset($this->{$offset}) ? $this->{$offset} : null;
	}

}

?>