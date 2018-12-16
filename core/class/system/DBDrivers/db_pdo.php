<?php
/*
 *
 * @version 4.2
 * @copyright 2014-2016 KilleR for Cardinal Engine
 *
 * Version Engine: 4.2
 * Version File: 1
 *
 * 1.1
 * add support driver for pdo
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

class db_pdo extends DriverParam implements drivers {

	private $mc;
	private $query;
	public $connecten = false;
	public static $subname = "PDO";
	public $type_driver = "pdo";
	public $type_error = 1;
	public function connected() {
		return $this->connecten;
	}
	public function check_connect($host, $user, $pass) {
		try {
			new PDO("mysql:host=".$host, $user, $pass);
			return true;
		} catch (PDOException $e) {
			return false;
		}
	}
	public function exists_db($host, $user, $pass, $db) {
		if($this->check_connect()) {
			try {
				new PDO("mysql:host=".$host.";dbname=".$db, $user, $pass);
				return true;
			} catch(Exception $ex) {
				return false;
			}
		} else {
			return false;
		}
	}
	public function connect($host, $user, $pass, $db, $charset, $port) {
		if (!class_exists('PDO')) {
			if(function_exists("errorHeader")) { errorHeader(); }
			if(class_exists("HTTP") && method_exists("HTTP", "echos")) {
				HTTP::echos('Server database PDO not support PHP');
			} else {
				echo ('Server database PDO not support PHP');
			}
			die();
		}
		try {
			$this->mc = @new PDO("mysql:host=".$host.";port=".$port.";dbname=".$db.";charset=".$charset, $user, $pass);
			$this->mc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
			$this->mc->query("SET NAMES '".$charset."'");
			$this->mc->query("SET CHARACTER SET '".$charset."'");
			$parseInt = intval($this->mc->errorCode());
			if(!empty($parseInt)) {
				if(function_exists("errorHeader")) { errorHeader(); }
				if(class_exists("HTTP") && method_exists("HTTP", "echos")) {
					HTTP::echos();
				}
				switch($this->mc->errorCode()) {
					case 1044:
					case 1045:
						echo ("Connect to database not exists, incorrect login-password");
						break;
					case 1049:
						echo ("Select database not exists");
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
						echo ("[".$this->mc->errorCode()."]: ".var_export($this->mc->errorInfo(), true));
						break;
				}
				die();
			}
			$this->connecten = true;
			$this->mc->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
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
		$this->mc->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
		$this->mc->beginTransaction();
		$this->query = $return = $this->mc->query($query);
		if(!$return) {
			$this->mc->rollBack();
			$this->error($query);
			return false;
		} else {
			$this->mc->commit();
			return $return;
		}
	}
	public function affected_rows() {
		return $this->query->rowCount();
	}
	public function insert_id() {
		return $this->mc->lastInsertId();
	}
	public function escape($str, $saved = true) {
		$str = $this->mc->quote($str);
		if(!$saved && substr($str, 0, 1)=="'" && substr($str, -1, 1)=="'") {
			$str = substr($str, 1, -1);
		}
		return $str;
	}
	public function num_fields() {
		return $this->query->columnCount();
	}
	public function fetch_row($query) {
		return $query->fetch(PDO::FETCH_NUM);
	}
	public function fetch_array($query) {
		return $query->fetch(PDO::FETCH_BOTH);
	}
	public function fetch_assoc($query) {
		return $query->fetch(PDO::FETCH_ASSOC);
	}
	public function fetch_object($query, $class_name, $params = array()) {
		if(is_array($params) && sizeof($params)>0) {
			return $query->fetchObject($class_name, $params);
		} else {
			return $query->fetchObject($class_name);
		}
	}
	public function num_rows($query) {
		return $query->rowCount();
	}
	public function free($query) {
		return $query->closeCursor();
	}
	public function error($query) {
		$err = $this->mc->errorInfo();
		db::error(array("query" => $query, "mysql_error" => $err[2], "mysql_error_num" => $err[1]));
	}
	public function close() {
		if(!$this->mc) {
			return false;
		}
		$this->mc = false;
	}

}

?>