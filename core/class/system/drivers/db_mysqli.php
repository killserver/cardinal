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
 * add support driver for mysqli
 * 1.2
 * fix bug working with object
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

class db_mysqli extends DriverParam implements drivers {

	private $mc;
	public $connecten = false;
	public static $subname = "MySQLi";
	public $type_driver = "mysqli";
	public $type_error = 1;
	public function connected() {
		return $this->connecten;
	}
	public function check_connect($host, $user, $pass) {
		try {
			$mysqli = new mysqli($host, $user, $pass);
			$ret = empty($mysqli->connect_error);
			if($ret) {
				$mysqli->close();
			}
			return $ret;
		} catch(Exception $ex) {
			return false;
		}
	}
	public function exists_db($host, $user, $pass, $db) {
		if($this->check_connect()) {
			try {
				$mysqli = new mysqli($host, $user, $pass, $db);
				$ret = empty($mysqli->connect_error);
				if($ret) {
					$mysqli->close();
				}
				return $ret;
			} catch(Exception $ex) {
				return false;
			}
		} else {
			return false;
		}
	}
	public function connect($host, $user, $pass, $db, $charset, $port) {
		if (!class_exists('mysqli')) {
			HTTP::echos();
			echo ('Server database MySQL not support PHP');
			die();
		}
		if(!@$this->mc = mysqli_init()) {
			HTTP::echos();
			echo "[error]";
			die();
		}
		$this->mc->options(MYSQLI_INIT_COMMAND, "SET NAMES '".$charset."'");
		$this->mc->options(MYSQLI_INIT_COMMAND, "SET CHARACTER SET '".$charset."'");
		try {
			if(!$this->mc->real_connect($host, $user, $pass, $db, $port, false, MYSQLI_CLIENT_COMPRESS)) {
				HTTP::echos();
				switch($this->mc->connect_errno) {
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
						echo ("[".$this->mc->connect_errno."]: ".$this->mc->connect_errno);
						break;
				}
				die();
			}
			$this->connecten = true;
			$this->mc->autocommit(false);
		} catch(Exception $e) {
			Error::handlePhpError($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
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
		if(PHP_VERSION_ID>=55000) {
			$this->mc->begin_transaction();
		} else {
			$this->mc->autocommit(FALSE);
		}
		if(!($return = $this->mc->query($query))) {
			$this->error($query);
		}
		$this->mc->commit();
		return $return;
	}
	public function affected_rows() {
		return $this->mc->affected_rows;
	}
	public function insert_id() {
		return $this->mc->insert_id;
	}
	public function escape($str) {
		return $this->mc->escape_string($str);
	}
	public function num_fields() {
		return $this->mc->field_count;
	}
	public function fetch_row($query) {
		return $query->fetch_row();
	}
	public function fetch_array($query) {
		return $query->fetch_array(MYSQLI_BOTH);
	}
	public function fetch_assoc($query) {
		return $query->fetch_assoc();
	}
	public function fetch_object($query, $class_name, $params) {
		if(is_array($params) && sizeof($params)>0) {
			return $query->fetch_object($class_name, $params);
		} else {
			return $query->fetch_object($class_name);
		}
	}
	public function num_rows($query) {
		return $query->num_rows;
	}
	public function free($query) {
		return mysqli_free_result($query);
	}
	public function error($query) {
		db::error(array("query" => $query, "mysql_error" => $this->mc->error, "mysql_error_num" => $this->mc->errno));
	}
	public function close() {
		if(!$this->mc) {
			return;
		}
		$this->mc->close();
	}

}

?>