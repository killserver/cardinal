<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

class QueryBuilder extends db {
	
	private $table = "";
	private $select = array();
	private $whereAdd = array();
	private $whereOR = array();
	private $leftJoin = array();
	private $limit = "";
	private $queryBuilder = false;
	
	final private function esq($name) {
		if(strpos($name, "`") === false && strpos($name, ".") === false) {
			return "`".$name."`";
		} else {
			return $name;
		}
	}
	
	final public function __construct($table) {
		$this->table = $table;
	}
	
	final public static function SaveEscape($val) {
		return str_replace(array('\x00', '\n', '\r', '\\', "'", '"', '\x1a'), array('\\x00', '\\n', '\\r', '\\\\', "\'", '\"', '\\x1a'), $val);
	}
	
	final public function selectAll() {
		$this->select[] = "*";
	}
	
	final public function select($select) {
		if(is_array($select)) {
			$this->select[] = $this->esq($select[0])." AS ".$this->esq($select[1]);
		} else if(is_string($select)) {
			$this->select[] = $this->esq($select);
		}
	}
	
	final public function whereAnd($where, $or = "", $val = "") {
		if(empty($val)) {
			if(is_int($or) || is_integer($or) || is_float($or) || is_double($or) || is_long($or) || is_numeric($or) || is_real($or) || is_scalar($or)) {
				$val = $or;
				$or = "=";
			} else if(is_string($or)) {
				$val = "'".$this->SaveEscape($or)."'";
				$or = "LIKE";
			}
		}
		if(!empty($or) && !empty($val)) {
			$this->whereAdd[] = $this->esq($where)." ".$or." ".$val;
		} else {
			$this->whereAdd[] = ($where);
		}
	}
	
	final public function whereOR($where, $or = "", $val = "") {
		if(empty($val)) {
			if(is_int($or) || is_integer($or) || is_float($or) || is_double($or) || is_long($or) || is_numeric($or) || is_real($or) || is_scalar($or)) {
				$val = $or;
				$or = "=";
			} else if(is_string($or)) {
				$val = "'".$this->SaveEscape($or)."'";
				$or = "LIKE";
			}
		}
		if(!empty($or) && !empty($val)) {
			$this->whereOR[] = $this->esq($where)." ".$or." ".$val;
		} else {
			$this->whereOR[] = ($where);
		}
	}
	
	final public function leftJoin($table, $onFirst, $onSecond) {
		if(is_array($table)) {
			$this->leftJoin[] = $this->esq($table[0])." AS ".$this->esq($table[1]);
		} else if(is_string($table)) {
			$this->leftJoin[] = $this->esq($table)." ON ".$this->esq($onFirst)."=".$this->esq($onSecond);
		}
	}
	
	final public function limit($limit, $offset = -1) {
		if($offset <= -1) {
			$this->limit = "LIMIT ".$limit;
		} else {
			$this->limit = "LIMIT ".$offset.", ".$limit;
		}
	}
	
	final public function execs() {
		$this->queryBuilder = $this->doquery("SELECT ".implode(", ", $this->select)." ".(sizeof($this->leftJoin)>0 ? implode(" LEFT JOIN ", $this->leftJoin) : "")." FROM ".$this->table.(sizeof($this->whereAdd)>0 || sizeof($this->whereOR)>0 ? (" WHERE ".(sizeof($this->whereAdd)>0 ? implode(" AND ", $this->whereAdd) : "")." ".(sizeof($this->whereOR)>0 ? implode(" AND ", $this->whereOR) : "")) : ""), true);
	}
	
	final public function result() {
		return $this->queryBuilder;
	}
	
	final public function resultArrayAll() {
		$list = array();
		while($row = $this->fetch_assoc($this->queryBuilder)) {
			$list[] = $row;
		}
		$this->free($this->queryBuilder);
		return $list;
	}
	
	final public function resultArray() {
		$row = $this->fetch_assoc($this->queryBuilder);
		$this->free($this->queryBuilder);
		return $row;
	}
	
	final public function resultObjectAll($object) {
		$list = array();
		while($row = $this->fetch_object($this->queryBuilder, $object)) {
			$list[] = $row;
		}
		$this->free($this->queryBuilder);
		return $list;
	}
	
	final public function resultObject($object) {
		$row = $this->fetch_object($this->queryBuilder, $object);
		$this->free($this->queryBuilder);
		return $row;
	}
	
	final public function resultNumAll() {
		$list = array();
		while($row = $this->fetch_row($this->queryBuilder)) {
			$list[] = $row;
		}
		$this->free($this->queryBuilder);
		return $list;
	}
	
	final public function resultNum() {
		$row = $this->fetch_row($this->queryBuilder);
		$this->free($this->queryBuilder);
		return $row;
	}
	
}