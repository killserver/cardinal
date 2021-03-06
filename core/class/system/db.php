<?php
/*
 *
 * @version 4.1
 * @copyright 2014-2016 KilleR for Cardinal Engine
 *
 * Version Engine: 4.1
 * Version File: 18
 *
 * 16.1
 * fix errors
 * 16.2
 * add checker connection to db
 * 17.1
 * add support "drivers" - submodules for database
 * 17.2
 * fix method db for drivers
 * 18.0
 * completed working with core database and fix working with object
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

final class db {

	private static $qid;
	private static $driver;
	private static $param = array("sql" => "", "param" => array());
	public static $time = 0;
	public static $num = 0;
	public static $querys = array();
	private static $driver_name = null;

	public static function connected() {
		if(is_bool(self::$driver) || empty(self::$driver)) {
			return false;
		}
		return self::$driver->connected();
	}

	public static function check_connect($host, $user, $pass) {
		if(is_bool(self::$driver) || empty(self::$driver)) {
			return false;
		}
		return self::$driver->check_connect($host, $user, $pass);
	}

	public static function escape($str) {
		return self::$driver->escape($str);
	}

	public static function exists_db($host, $user, $pass, $db) {
		if(is_bool(self::$driver) || empty(self::$driver)) {
			return false;
		}
		return self::$driver->exists_db($host, $user, $pass, $db);
	}

	public static function changeDriver($driver) {
		self::$driver_name = $driver;
	}

	public static function OpenDriver() {
		$driv = self::$driver_name;
		if(!class_exists($driv)) {
			$driv = self::DriverList();
			$driv = $driv[array_rand($driv)];
		}
		self::$driver = new $driv();
		return true;
	}

	public static function DriverList() {
		$dir = ROOT_PATH."core".DS."class".DS."system".DS."drivers".DS;
		$dirs = array();
		if(is_dir($dir)) {
			if($dh = dir($dir)) {
				while(($file = $dh->read()) !== false) {
					if(is_file($dir.$file) && strpos($file, ".".ROOT_EX)!==false) {
						$dirs[] = $file;
					}
				}
			$dh->close();
			}
		}
		sort($dirs);
		$drivers = array();
		for($i=0;$i<sizeof($dirs);$i++) {
			if($dirs[$i]=="index.".ROOT_EX||$dirs[$i]=="index.html"||$dirs[$i]=="DriverParam.".ROOT_EX||$dirs[$i]=="drivers.".ROOT_EX||$dirs[$i]=="DBObject.".ROOT_EX) {
				continue;
			}
			$dr_subname = str_replace(".".ROOT_EX, "", $dirs[$i]);
			$drivers[] = $dr_subname;
		}
		return $drivers;
	}

	public static function connect($host, $user, $pass, $db, $charset, $port) {
//mysql_query ("set character_set_client='utf8'"); 
//mysql_query ("set character_set_results='utf8'"); 
//mysql_query ("set collation_connection='utf8_general_ci'");
		$open = self::OpenDriver();
		if($open) {
			self::$driver->connect($host, $user, $pass, $db, $charset, $port);
		}
	}

	function __construct() {
		if(!defined("INSTALLER")) {
			config::StandAlone();
			self::$driver_name = config::Select('db','driver');
			self::connect(config::Select('db','host'), config::Select('db','user'), config::Select('db','pass'), config::Select('db','db'), config::Select('db', 'charset'), config::Select('db', 'port'));
		}
	}

	private static function time() {
		return microtime();
	}

	function set_type($int = 2) {
		self::$driver->set_type($int);
	}

	public static function prepare($sql) {
		self::$param['sql'] = $sql;
	}

	public static function param() {
		$params = func_get_args();
		if(is_array($params[0])) {
			$param = $params[0];
		} else {
			$param = array_merge(self::$param['param'], array($params[0] => $params[1]));
		}
		self::$param['param'] = $param;
	}

	public static function execute() {
		if(is_bool(self::$driver) || empty(self::$driver)) {
			return false;
		}
		$sql = self::$param['sql'];
		foreach(self::$param['param'] as $n => $v) {
			$sql = str_replace(array("::".$n, ":".$n, "$".$n), $v, $sql);
		}
		unset(self::$param);
		return self::query($sql);
	}

	public static function doquery($query, $only = "", $check = false) {
	global $user;
		$table = preg_replace("/(.*)(FROM|TABLE|UPDATE|INSERT INTO) (.+?) (.*)/", "$3", $query);
		$badword = false;
		if((stripos($query, 'RUNCATE TABL') != FALSE) && ($table != 'shoutbox')) {
			$badword = true;
		} elseif(stripos($query, 'ROP TABL') != FALSE) {
			$badword = true;
		} elseif(stripos($query, 'ENAME TABL') != FALSE) {
			$badword = true;
		} elseif(stripos($query, 'REATE DATABAS') != FALSE) {
			$badword = true;
		} elseif(stripos($query, 'REATE TABL') != FALSE) {
			$badword = true;
		} elseif(stripos($query, 'ET PASSWOR') != FALSE) {
			$badword = true;
		} elseif(stripos($query, 'EOAD DAT') != FALSE) {
			$badword = true;
		} elseif(stripos($query, 'AUTHLEVEL') != FALSE && stripos($query, 'SELECT') !== 0) {
			$badword = true;
		}
		if($badword) {
			$message = '������, � �� ���� ��, ��� �� ��������� �������, �� �������, ������� �� ������ ������� ���� ������, �� ��������� ����� ������������� � ��� ���� ��������������.<br /><br />��� IP, � ������ ������ ��������� ������������� �������. �����!.';
			$report  = "Hacking attempt (".date("d.m.Y H:i:s")." - [".time()."]):\n";
			$report .= ">Database Inforamation\n";
			$report .= "\tID - ".(isset($user['id']) ? $user['id'] : 0)."\n";
			$report .= "\tUser - ".(isset($user['username']) ? $user['username'] : "")."\n";
			$report .= "\tAuth level - ".(isset($user['authlevel']) ? $user['authlevel'] : 0)."\n";
			$report .= "\tUser IP - ".(isset($user['regip']) ? $user['regip'] : "")."\n";
			$report .= "\tUser IP at Reg - ".(isset($user['lastip']) ? $user['lastip'] : "")."\n";
			$report .= "\tUser Agent - ".(isset($user['user_agent']) ? $user['user_agent'] : "")."\n";
			$report .= "\tRegister Time - ".(isset($user['register_time']) ? $user['register_time'] : "")."\n";
			$report .= "\n";
			$report .= ">Query Information\n";
			$report .= "\tTable - ".$table."\n";
			$report .= "\tQuery - ".$query."\n";
			$report .= "\n";
			$report .= ">\$_SERVER Information\n";
			$report .= "\tIP - ".(isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "")."\n";
			$report .= "\tHost Name - ".(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "")."\n";
			$report .= "\tUser Agent - ".(isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "")."\n";
			$report .= "\tRequest Method - ".(isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : "")."\n";
			$report .= "\tCame From - ".(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "")."\n";
			$report .= "\tPage is - ".(isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : "")."\n";
			$report .= "\tUses Port - ".(isset($_SERVER['REMOTE_PORT']) ? $_SERVER['REMOTE_PORT'] : "")."\n";
			$report .= "\tServer Protocol - ".(isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : "")."\n";
			$report .= "\n--------------------------------------------------------------------------------------------------\n";
			$fp = fopen(ROOT_PATH.'core'.DS.'cache'.DS.'badqrys.txt', 'a');
			fwrite($fp, $report);
			fclose($fp);
			die($message);
		}
		self::$qid = self::query($query);
		if(!$check) {
			if(strpos($query, "SELECT") !== false || strpos($query, "SHOW TABLE") !== false) {
				if(!empty($only)) {
					return self::$qid;
				} else {
					return self::fetch_array();
				}
			} else {
				return self::$qid;
			}
		} else {
			return $this;
		}
	}

	public static function last_id($table) {
		$table = self::doquery("SHOW TABLE STATUS LIKE '".$table."'");
		return $table['Auto_increment'];
	}
	
	private static function RePair() {
		$db_name = config::Select('db','db');
		$sel = self::query("SHOW FULL TABLES");
		while($row = self::fetch_assoc($sel)) {
			db::query("REPAIR TABLE `".$row['Tables_in_'.$db_name]."`");
		}
	}

	public static function select_query($query) {
		if(is_bool(self::$driver) || empty(self::$driver)) {
			return false;
		}
		if(strpos($query, "SELECT") !== false || strpos($query, "SHOW TABLE") !== false) {
			$qid = self::query($query);
			$array = array();
			while($row=self::fetch_assoc($qid)) {
				$array[] = $row;
			}
			return $array;
		} else {
			return false;
		}
	}

	public static function query($query) {
		if(is_bool(self::$driver) || empty(self::$driver)) {
			return false;
		}
		$stime = self::time();
		self::$qid = $return = self::$driver->query($query);
		$etime = self::time()-$stime;
		self::$time += $etime;
		self::$num += 1;
		self::$querys[] = array("time" => $etime, "query" => htmlspecialchars($query));
	return $return;
	}

	public static function affected_rows() {
		if(is_bool(self::$driver) || empty(self::$driver)) {
			return false;
		}
		return self::$driver->affected_rows();
	}

	public static function insert_id() {
		if(is_bool(self::$driver) || empty(self::$driver)) {
			return false;
		}
		return self::$driver->insert_id();
	}

	public static function num_fields() {
		if(is_bool(self::$driver) || empty(self::$driver)) {
			return false;
		}
		return self::$driver->field_count();
	}

	public static function fetch_row($query = "") {
		if(is_bool(self::$driver) || empty(self::$driver)) {
			return false;
		}
		if(empty($query)) {
			$query = self::$qid;
		}
		return self::$driver->fetch_row($query);
	}

	public static function fetch_array($query = "") {
		if(is_bool(self::$driver) || empty(self::$driver)) {
			return false;
		}
		if(empty($query)) {
			$query = self::$qid;
		}
		return self::$driver->fetch_array($query);
	}

	public static function fetch_assoc($query = "") {
		if(is_bool(self::$driver) || empty(self::$driver)) {
			return false;
		}
		if(empty($query)) {
			$query = self::$qid;
		}
		return self::$driver->fetch_assoc($query);
	}

	public static function fetch_object($query = "", $class_name = "", $params = array()) {
		if(is_bool(self::$driver) || empty(self::$driver)) {
			return false;
		}
		if(empty($query)) {
			$query = self::$qid;
		}
		return self::$driver->fetch_object($query, $class_name, $params);
	}

	public static function num_rows($query = "") {
		if(is_bool(self::$driver) || empty(self::$driver)) {
			return false;
		}
		if(empty($query)) {
			$query = self::$qid;
		}
		return self::$driver->num_rows($query);
	}

	public static function free($query = "") {
		if(is_bool(self::$driver) || empty(self::$driver)) {
			return false;
		}
		if(empty($query)) {
			$query = self::$qid;
		}
		if(is_bool($query)) {
			return false;
		}
		return self::$driver->free($query);
	}

	public static function close() {
		if(is_bool(self::$driver) || empty(self::$driver)) {
			return false;
		}
		return self::$driver->close();
	}

	public static function error($arr) {
		$mysql_error = $arr['mysql_error'];
		$mysql_error_num = $arr['mysql_error_num'];
		$query = $arr['query'];
		
		if(in_array($mysql_error_num, array(145, 144, 135, 136, 126, 127))) {
			self::RePair();
		}

		if($query) {
			// Safify query
			$query = preg_replace("/([0-9a-f]){32}/", "********************************", $query); // Hides all hashes
		}

		$query = htmlspecialchars($query, ENT_QUOTES, 'ISO-8859-1');
		$mysql_error = htmlspecialchars($mysql_error, ENT_QUOTES, 'ISO-8859-1');

		$trace = debug_backtrace();

		$level = 0;
		if (isset($trace[1]['function']) && $trace[1]['function'] == "query" ) $level = 1;
		if (isset($trace[2]['function']) && $trace[2]['function'] == "doquery" ) $level = 2;

		$trace[$level]['file'] = str_replace(ROOT_PATH, "", $trace[$level]['file']);

		if(self::$driver->get_type() === 1) {
			modules::init_templates()->dir_skins("skins/");
			modules::init_templates()->assign_vars(array(
				"query" => $query,
				"error" => $mysql_error,
				"error_num" => $mysql_error_num,
				"file" => $trace[$level]['file'],
				"line" => $trace[$level]['line'],
			));
			echo modules::init_templates()->complited_assing_vars("mysql_error", null);
		} else {
			echo "<center><br />".$trace[$level]['file'].":".$trace[$level]['line']."<hr />Query:<br /><textarea cols=\"40\" rows=\"5\">".$query."</textarea><hr />[".$mysql_error_num."] ".$mysql_error."<br />";
		}
		modules::init_templates()->__destruct();
		exit();
	}

	function __destruct() {
		self::close();
	}

}