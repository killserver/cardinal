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
 * add support driver for mysql
 * 1.2
 * fix bug working with object
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

if(!class_exists("DriverParam", true)) {
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."DriverParam.php");
}
if(!class_exists("drivers", true)) {
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."drivers.php");
}

class db_mysql extends DriverParam implements drivers {
	
	private $mc;
	public $connecten = false;
	public static $subname = "MySQL";
	public $type_driver = "mysql";
	public $type_error = 1;
	public function connected() {
		return $this->connecten;
	}
	public function check_connect($host, $user, $pass) {
		try {
			$mysqli = mysql_connect($host, $user, $pass);
			$ret = $mysqli!==false;
			if($ret) {
				mysql_close($mysqli);
			}
			return $ret;
		} catch(Exception $ex) {
			if(function_exists("errorHeader")) { errorHeader(); }
			return false;
		}
	}
	public function exists_db($host, $user, $pass, $db) {
		if($this->check_connect()) {
			try {
				$mysqli = mysql_connect($host, $user, $pass);
				$ret = mysql_select_db($db, $mysqli);
				if($ret) {
					mysql_close($mysqli);
				}
				return $ret;
			} catch(Exception $ex) {
				if(function_exists("errorHeader")) { errorHeader(); }
				return false;
			}
		} else {
			return false;
		}
	}
	public function connect($host, $user, $pass, $db, $charset, $port) {
		if(!function_exists('mysql_connect')) {
			if(function_exists("errorHeader")) { errorHeader(); }
			if(class_exists("HTTP") && method_exists("HTTP", "echos")) {
				HTTP::echos('Server database MySQL not support PHP');
			} else {
				echo ('Server database MySQL not support PHP');
			}
			die();
		}
		try {
			if(!@$this->mc = mysql_connect($host, $user, $pass)) {
				if(function_exists("errorHeader")) { errorHeader(); }
				if(class_exists("HTTP") && method_exists("HTTP", "echos")) {
					HTTP::echos();
				}
				switch(mysql_errno($this->mc)) {
					case 1044:
					case 1045:
						echo ("Connect to database not exists, incorrect login-password");
						break;
					case 2003:
						echo ("Connect to database not exists, error in port database");
						break;
					case 2005:
						echo ("Connect to database is not exists, addres database or server database not exists");
						break;
					case 2006:
						echo ("Connect to database in not exists, server database is not exists");
						break;
					default:
						echo "[".mysql_errno($this->mc)."]: ".mysql_error($this->mc);
						break;
				}
				die();
			}
			mysql_select_db($db, $this->mc);
			$this->query("SET NAMES '".$charset."'");
			$this->query("SET CHARACTER SET '".$charset."'");
			$this->connecten = true;
		} catch(Exception $e) {
			if(function_exists("errorHeader")) { errorHeader(); }
			if(class_exists("cardinalError")) {
				cardinalError::handlePhpError($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
			} else {
				throw new Exception($e);
			}
			exit();
		}
	}
	public function set_type($int) {
		$this->type_error = intval($int);
	}
	public function get_type() {
		return $this->type_error;
	}
	public function query($query) {
		mysql_query("START TRANSACTION;", $this->mc);
		if(!($return = mysql_query($query, $this->mc))) {
			$this->error($query);
		}
		mysql_query("COMMIT;", $this->mc);
		return $return;
	}
	public function affected_rows() {
		return mysql_affected_rows($this->mc);
	}
	public function insert_id() {
		return mysql_insert_id($this->mc);
	}
	public function escape($str, $saved = true) {
		$save = preg_match("/(SELECT(.+?)FROM|UNIX_TIMESTAMP|WHERE(.+?)IN)/", $str);
		return (!$saved || $save ? "": "'").mysql_real_escape_string($str).(!$saved || $save ? "": "'");
	}
	public function num_fields() {
		return mysql_num_fields($this->mc);
	}
	public function fetch_row($query) {
		return mysql_fetch_row($query);
	}
	public function fetch_array($query) {
		return mysql_fetch_array($query);
	}
	public function fetch_assoc($query) {
		return mysql_fetch_assoc($query);
	}
	public function fetch_object($query, $class_name, $params = array()) {
		if(is_array($params) && sizeof($params)>0) {
			return mysql_fetch_object($query, $class_name, $params);
		} else {
			return mysql_fetch_object($query, $class_name);
		}
	}
	public function num_rows($query) {
		return mysql_num_rows($query);
	}
	public function free($query) {
		return mysql_free_result($query);
	}
	public function error($query) {
		db::error(array("query" => $query, "mysql_error" => mysql_error($this->mc), "mysql_error_num" => mysql_errno($this->mc)));
	}
	public function close() {
		if(!$this->mc) {
			return false;
		}
		mysql_close($this->mc);
		$this->mc = false;
	}
	
}