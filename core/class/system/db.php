<?php
/*
 *
 * @version 5.2
 * @copyright 2014-2016 KilleR for Cardinal Engine
 *
 * Version Engine: 5.2
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
 * 18.1
 * add support get version database
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

$phpEx = substr(strrchr(__FILE__, '.'), 1);
if(!defined("ROOT_EX") && strpos($phpEx, '/') === false) {
	define("ROOT_EX", $phpEx);
}

/**
 * Class db
 */
class db {

    /**
     * @var mixed Resource query
     */
    private static $qid;
    /**
     * @var mixed Resource driver(connection)
     */
    private static $driver;
    /**
     * @var bool Check used generator connection
     */
    private static $driverGen = false;
    /**
     * @var array List params
     */
    private static $param = array("sql" => "", "param" => array());
    /**
     * @var int Timer for query execute
     */
    public static $time = 0;
    /**
     * @var int Counter for query in execute
     */
    public static $num = 0;
    /**
     * @var array List query's
     */
    public static $querys = array();
    /**
     * @var string Name connection drive
     */
    private static $driver_name = "";
    /**
     * @var string Database name
     */
    public static $dbName = "";
    /**
     * @var array Config for db without core
     */
	private static $configInit = array();
	private static $loadedTable = array();

    /**
     * Check connection for database
     * @return bool Result connection
     */
    final public static function connected() {
		if(is_bool(self::$driver) || empty(self::$driver)) {
			return false;
		}
		return self::$driver->connected();
	}

    /**
     * Check stable connection
     * @param string $host Host for connect
     * @param string $user User for connect
     * @param string $pass Password for connect
     * @return bool Result checked stable connection
     */
    final public static function check_connect($host, $user, $pass) {
		if(is_bool(self::$driver) || empty(self::$driver)) {
			return false;
		}
		return self::$driver->check_connect($host, $user, $pass);
	}

    /**
     * Save string for query
     * @param string $str Needed string
     * @return string Result saving
     */
    final public static function escape($str, $save = true) {
		if(is_bool(self::$driver) || empty(self::$driver)) {
			return str_replace(array('\x00', '\n', '\r', '\\', "'", '"', '\x1a'), array('\\x00', '\\n', '\\r', '\\\\', "\'", '\"', '\\x1a'), $str);
		} else {
			return self::$driver->escape($str, $save);
		}
	}

    /**
     * Check database in connection
     * @param string $host Host for connection
     * @param string $user User for connection
     * @param string $pass Password for connection
     * @param string $db Database for connection
     * @return bool Result checked database in connection
     */
    final public static function exists_db($host, $user, $pass, $db) {
		if(is_bool(self::$driver) || empty(self::$driver)) {
			return false;
		}
		return self::$driver->exists_db($host, $user, $pass, $db);
	}

    /**
     * Force change driver for connection
     * @param string $driver
     */
    final public static function changeDriver($driver) {
		self::$driver_name = $driver;
	}

    /**
     * Start connection to database
     * @return bool Result connection
     */
    final public static function OpenDriver() {
		$driv = self::$driver_name;
		if(!is_string($driv) || !class_exists($driv)) {
			if(defined("PATH_CACHE_USERDATA") && file_exists(PATH_CACHE_USERDATA."db_lock.lock") && is_readable(PATH_CACHE_USERDATA."db_lock.lock")) {
				$driv = file_get_contents(PATH_CACHE_USERDATA."db_lock.lock");
			} elseif(!defined("PATH_CACHE_USERDATA") && file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."db_lock.lock") && is_readable(dirname(__FILE__).DIRECTORY_SEPARATOR."db_lock.lock")) {
				$driv = file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR."db_lock.lock");
			} else {
				self::$driverGen = true;
				$driv = self::DriverList();
				$driv = $driv[array_rand($driv)];
				self::$driver_name = $driv;
			}
			if(!defined("PATH_CACHE_USERDATA")) {
				include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."DBDrivers".DIRECTORY_SEPARATOR.$driv.".".ROOT_EX);
			}
		}
		self::$driver = new $driv();
		return true;
	}

    /**
     * Generate list exists drivers
     * @return array List exists drivers
     */
    final public static function DriverList() {
		$dir = (defined("PATH_DB_DRIVERS") ? PATH_DB_DRIVERS : dirname(__FILE__).DIRECTORY_SEPARATOR."DBDrivers".DIRECTORY_SEPARATOR);
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
			if($dirs[$i]=="index.".ROOT_EX||$dirs[$i]=="index.html"||$dirs[$i]=="DriverParam.".ROOT_EX||$dirs[$i]=="drivers.".ROOT_EX||$dirs[$i]=="DBObject.".ROOT_EX||$dirs[$i]=="QueryBuilder.".ROOT_EX) {
				continue;
			}
			$dr_subname = str_replace(".".ROOT_EX, "", $dirs[$i]);
			$drivers[] = $dr_subname;
		}
		return $drivers;
	}

    /**
     * Connect to database and create resource connection
     * @param string $host Host for connection
     * @param string $user User for connection
     * @param string $pass Password for connection
     * @param string $db Database for connection
     * @param string $charset Charset for connection
     * @param string $port Port for connection
     */
    final public static function connect($host, $user, $pass, $db, $charset, $port) {
//mysql_query ("set character_set_client='utf8'"); 
//mysql_query ("set character_set_results='utf8'"); 
//mysql_query ("set collation_connection='utf8_general_ci'");
		$open = self::OpenDriver();
		if($open) {
			self::$driver->connect($host, $user, $pass, $db, $charset, $port);
			if(self::connected()) {
				if(defined("ROOT_PATH") && !file_exists(PATH_CACHE_SYSTEM."db_charset.lock") && is_writable(PATH_CACHE_SYSTEM)) {
					self::query("ALTER DATABASE `".$db."` DEFAULT CHARSET=".$charset." COLLATE ".$charset."_general_ci;");
					file_put_contents(PATH_CACHE_SYSTEM."db_charset.lock", "");
				}
				if(self::$driverGen && !empty(self::$driver_name)) {
					if(defined("ROOT_PATH") && !file_exists(PATH_CACHE_SYSTEM."db_lock.lock") && is_writable(PATH_CACHE_SYSTEM)) {
						file_put_contents(PATH_CACHE_SYSTEM."db_lock.lock", self::$driver_name);
					}
					if(!defined("ROOT_PATH") && !file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."db_lock.lock") && is_writable(dirname(__FILE__).DIRECTORY_SEPARATOR)) {
						file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR."db_lock.lock", self::$driver_name);
					}
				}
			}
		}
	}

    /**
     * db constructor.
     */
    function __construct($withoutInit = false, $forceStart = false) {
		$host = (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "online-killer.pp.ua");
		if($forceStart
			||
			(
				!$withoutInit
				&&
				(!defined("INSTALLER")
					||
					(
						(
							(defined("PATH_MEDIA") && file_exists(PATH_MEDIA."db.".ROOT_EX))
							||
							(!defined("PATH_MEDIA") && file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."db.config.".ROOT_EX))
							||
							(defined("PATH_MEDIA") && file_exists(PATH_MEDIA."db.".str_replace("www.", "", $host).".".ROOT_EX))
							||
							(!defined("PATH_MEDIA") && file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."db.config.".str_replace("www.", "", $host).".".ROOT_EX))
						)
						&&
						defined("WITHOUT_DB")
					)
				)
			)
		) {
			if(!defined("PATH_MEDIA")) {
				self::config();
			}
			self::init();
		}
	}
	
	final public static function config($config = array()) {
		$host = (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "online-killer.pp.ua");
		if(sizeof($config)==0 &&
				(
					(defined("PATH_MEDIA") && !file_exists(PATH_MEDIA."db.".$host.".".ROOT_EX))
					&&
					(defined("PATH_MEDIA") && !file_exists(PATH_MEDIA."db.".ROOT_EX))
					&&
					(!defined("PATH_MEDIA") && !file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."db.config.".$host.".".ROOT_EX))
					&&
					(!defined("PATH_MEDIA") && !file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."db.config.".ROOT_EX))
				)
		) {
			if(function_exists("errorHeader")) {
				errorHeader();
			}
			throw new Exception("Config file for db or data in config is not correct");
			die();
		} else if(defined("PATH_MEDIA") && file_exists(PATH_MEDIA."db.".$host.".".ROOT_EX)) {
			include_once(PATH_MEDIA."db.".$host.".".ROOT_EX);
			if(isset($config['db'])) {
				$config = $config['db'];
			}
		} else if(!defined("PATH_MEDIA") && file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."db.config.".$host.".".ROOT_EX)) {
			include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."db.config.".$host.".".ROOT_EX);
			if(isset($config['db'])) {
				$config = $config['db'];
			}
		} else if(defined("PATH_MEDIA") && file_exists(PATH_MEDIA."db.".ROOT_EX)) {
			include_once(PATH_MEDIA."db.".ROOT_EX);
			if(isset($config['db'])) {
				$config = $config['db'];
			}
		} else if(!defined("PATH_MEDIA") && file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."db.config.".ROOT_EX)) {
			include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."db.config.".ROOT_EX);
			if(isset($config['db'])) {
				$config = $config['db'];
			}
		}
		self::$configInit = array_merge(self::$configInit, $config);
	}

    /**
     * Initializatior connection
     */
    final public static function init() {
		if(class_exists("config")) {
			config::StandAlone();
			self::$driver_name = config::Select('db','driver');
			self::$dbName = config::Select('db','db');
			$host = config::Select('db','host');
			$user = config::Select('db','user');
			$pass = config::Select('db','pass');
			$chst = config::Select('db', 'charset');
			$port = config::Select('db', 'port');
		} else {
			if(!isset(self::$configInit['driver'])) {
				if(function_exists("errorHeader")) { errorHeader(); }
				throw new Exception("Error! Driver is not set");
				die();
			} else {
				self::$driver_name = self::$configInit['driver'];
			}
			if(!isset(self::$configInit['db'])) {
				if(function_exists("errorHeader")) { errorHeader(); }
				throw new Exception("Error! DB is not set");
				die();
			} else {
				self::$dbName = self::$configInit['db'];
			}
			if(!isset(self::$configInit['host'])) {
				if(function_exists("errorHeader")) { errorHeader(); }
				throw new Exception("Error! Host is not set");
				die();
			} else {
				$host = self::$configInit['host'];
			}
			if(!isset(self::$configInit['user'])) {
				if(function_exists("errorHeader")) { errorHeader(); }
				throw new Exception("Error! User is not set");
				die();
			} else {
				$user = self::$configInit['user'];
			}
			if(!isset(self::$configInit['pass'])) {
				if(function_exists("errorHeader")) { errorHeader(); }
				throw new Exception("Error! Password is not set");
				die();
			} else {
				$pass = self::$configInit['pass'];
			}
			if(!isset(self::$configInit['charset'])) {
				if(function_exists("errorHeader")) { errorHeader(); }
				throw new Exception("Error! Charset is not set");
				die();
			} else {
				$chst = self::$configInit['charset'];
			}
			if(!isset(self::$configInit['port'])) {
				if(function_exists("errorHeader")) { errorHeader(); }
				throw new Exception("Error! Port is not set");
				die();
			} else {
				$port = self::$configInit['port'];
			}
		}
		if(is_string($host) && is_string($user) && is_string($pass) && is_string($chst) && is_string($port)) {
			self::connect($host, $user, $pass, self::$dbName, $chst, $port);
		}
	}

	final public static function reconnect() {
		self::init();
	}

	final public static function flushCacheTables() {
		self::$loadedTable = array();
		return true;
	}
	
	final public static function getTables($columns = true, $andType = false, $full = false) {
		$loaded = array();
		if(sizeof(self::$loadedTable)==0 && self::connected()) {
			$sel = db::doquery("SHOW FULL TABLES", true);
			while($row = db::fetch_assoc($sel)) {
				$loaded[$row['Tables_in_'.strtolower(self::$dbName)]] = array();
				$res = db::query("SHOW COLUMNS FROM `".$row['Tables_in_'.strtolower(self::$dbName)]."`");
				while($roz = db::fetch_assoc($res)) {
					$loaded[$row['Tables_in_'.strtolower(self::$dbName)]][$roz['Field']] = $roz['Type'];
				}
			}
			self::$loadedTable = $loaded;
		} else if(self::connected()) {
			$loaded = self::$loadedTable;
		}
		$ret = array();
		foreach($loaded as $name => $fields) {
			$ret[$name] = array();
			if($columns && !$andType) {
				$ret[$name] = array_keys($fields);
			} else if($columns && $andType && !$full) {
				$keys = array_keys($fields);
				$retz = array();
				for($i=0;$i<sizeof($keys);$i++) {
					$type = $fields[$keys[$i]];
					$pos = strpos($type, "(");
					if($pos!==false) {
						$type = substr($type, 0, $pos);
					}
					$retz[$keys[$i]] = $type;
				}
				$ret[$name] = $retz;
			} else if($columns && $andType && $full) {
				$ret[$name] = $fields;
			}
		}
		return $ret;
	}
	
	final public static function getTable($name) {
		$list = self::getTables();
		if(defined("PREFIX_DB") && PREFIX_DB!=="" && isset($list[PREFIX_DB.$name])) {
			$name = PREFIX_DB.$name;
		}
		return (isset($list[$name]) ? $list[$name] : false);
	}
	
	final public static function getColumnForTable($name, $field) {
		$list = self::getTables();
		if(defined("PREFIX_DB") && PREFIX_DB!=="" && isset($list[PREFIX_DB.$name])) {
			$name = PREFIX_DB.$name;
		}
		return (isset($list[$name]) && isset($list[$name][$field]) ? $list[$name][$field] : false);
	}

    /**
     * Creator timer for query time
     * @return string Time with microseconds
     */
    final private static function time() {
    	$time = microtime();
    	if(strpos($time, " ")!==false) {
    		$time = explode(" ", $time);
    		$time = current($time);
    	}
		return $time;
	}

    /**
     * Type error if query return error
     * @param int $int Type error
     */
    final public function set_type($int = 2) {
		self::$driver->set_type($int);
	}

    /**
     * Prepare data for execute
     * @param $sql Needed query
     */
    final public static function prepare($sql) {
		self::$param['sql'] = $sql;
	}

    /**
     * List parameters for query
     */
    final public static function param() {
		$params = func_get_args();
		if(is_array($params[0])) {
			$param = $params[0];
		} else {
			$param = array_merge(self::$param['param'], array($params[0] => $params[1]));
		}
		self::$param['param'] = $param;
	}

    /**
     * Execute prepared query
     * @return bool Result query
     */
    final public static function execute() {
		if(is_bool(self::$driver) || empty(self::$driver)) {
			return false;
		}
		$sql = self::$param['sql'];
		foreach(self::$param['param'] as $n => $v) {
			$sql = str_replace(array("::".$n, ":".$n, "$".$n), $v, $sql);
		}
		self::$param = array("sql" => "", "param" => array());
		return self::query($sql);
	}

    /**
     * Return mysql version in integer
     * @return int Mysql version in integer
     */
    final public static function version() {
		if(is_bool(self::$driver) || empty(self::$driver)) {
			return 0;
		}
		$ver = self::query("SELECT VERSION() AS `v`");
		$version = self::fetch_assoc($ver);
		$version = $version['v'];
		$version = preg_replace("/^([0-9]{0,2})\.([0-9]{0,2})\.([0-9]{0,2})(-|)(.*?)$/is", "$1.$2.$3", $version);
		$exVersion = explode(".", $version);
		$version = $exVersion[0]*10000+$exVersion[1]*100+$exVersion[2];
		return $version;
	}

    /**
     * Saved executor query's
     * @param string $query Needed query
     * @param string $only Only execute without try return array
     * @param bool $check Only execute query and return object
     * @return $this|bool This object or query or associative array
     */
    final public static function doquery($query, $only = "") {
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
			if(function_exists("errorHeader")) { errorHeader(); }
			$message = 'Привет, я не знаю то, что Вы пробовали сделать, но команда, которую Вы только послали базе данных, не выглядела очень дружественной и она была заблокированна.<br /><br />Ваш IP, и другие данные переданны администрации сервера. Удачи!.';
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
			$fp = fopen((defined("ROOT_PATH") ? PATH_LOGS : dirname(__FILE__).DIRECTORY_SEPARATOR).'badqrys.txt', 'a');
			fwrite($fp, $report);
			fclose($fp);
			die($message);
		}
		self::$qid = self::query($query);
        if(strpos($query, "SELECT") !== false || strpos($query, "SHOW TABLE") !== false) {
            if(!empty($only)) {
                return self::$qid;
            } else {
                return self::fetch_assoc();
            }
        } else {
            return self::$qid;
        }
	}

    /**
     * Return last int auto_increment in table
     * @param string $table Needed table
     * @return string Int value last id element in table
     */
    final public static function last_id($table, $withoutPrefix = false) {
		if(!$withoutPrefix && defined("PREFIX_DB") && PREFIX_DB!=="") {
			$table = PREFIX_DB.$table;
		}
		$table = self::doquery("SHOW TABLE STATUS LIKE '".$table."'");
		return $table['Auto_increment'];
	}

    /**
     * Try repair all tables in database
     */
    final private static function RePair() {
		if(class_exists("config")) {
			$db_name = config::Select('db','db');
		} else {
			if(!isset(self::$configInit['db'])) {
				if(function_exists("errorHeader")) { errorHeader(); }
				throw new Exception("Error! DB is not set");
			} else {
				$db_name = self::$configInit['db'];
			}
		}
		$sel = self::query("SHOW FULL TABLES");
		while($row = self::fetch_assoc($sel)) {
			db::query("REPAIR TABLE `".$row['Tables_in_'.$db_name]."`");
		}
	}

    /**
     * Try return associative array or false if exists errors
     * @param string $query Needed query
     * @return array|bool Return associative array or false if exists errors
     */
    final public static function select_query($query) {
		if(is_bool(self::$driver) || empty(self::$driver)) {
			return false;
		}
		if(strpos($query, "SELECT") !== false || strpos($query, "SHOW TABLE") !== false || strpos($query, "SHOW COLUMNS") !== false) {
			$qid = self::query($query);
			$array = array();
			while($row = self::fetch_assoc($qid)) {
				$array[] = $row;
			}
			return $array;
		} else {
			return false;
		}
	}

	final private static function getTrace($backtrace) {
		$file = __FILE__;
		$ret = false;
		for($i=0;$i<sizeof($backtrace);$i++) {
			if($backtrace[$i]['file']!=$file) {
				$ret = $backtrace[$i];
				foreach($ret as $k => $v) {
					if(is_string($v) && defined("ROOT_PATH") && defined("DS")) {
						$ret[$k] = str_replace(ROOT_PATH, DS, $v);
					}
				}
				break;
			}
		}
		return $ret;
	}

	final public static function getQuery($query) {
		if(strpos($query, '{{') !== false && defined("PREFIX_DB") && PREFIX_DB!=="") {
			if(preg_match("/CREATE|DROP/", $query)) {
				$query = str_replace(array('{{', '}}'), array("`".PREFIX_DB, "`"), $query);
			} else {
				$arr = self::getTables(false);
				$arr = array_keys($arr);
				for($i=0;$i<sizeof($arr);$i++) {
					$t = str_replace(PREFIX_DB, "", $arr[$i]);
					$query = str_replace(array('{{'.$arr[$i].'}}', '{{'.$t.'}}'), "`".PREFIX_DB . $t."`", $query);
				}
			}
		}
		if(strpos($query, '{{') !== false) {
			$query = str_replace(array("{{", "}}"), "`", $query);
		}
		return $query;
	}

    /**
     * Just execute query
     * @param string $query Query for execute
     * @return bool|mixed Just execute query
     */
    final public static function query($query) {
		if(is_bool(self::$driver) || empty(self::$driver)) {
			return false;
		}
		$caller = self::getTrace(debug_backtrace());
		$query = self::getQuery($query);
		$stime = self::time();
		self::$qid = $return = self::$driver->query($query);
		$etime = self::time()-$stime;
		if($etime<0) {
			$etime = 0;
		}
		self::$time += $etime;
		self::$num += 1;
		$arr = array("time" => $etime, "query" => htmlspecialchars($query));
		$arr = array_merge($arr, $caller);
		self::$querys[] = $arr;
	return $return;
	}

    /**
     * Return last affected rows
     * @return bool|mixed Return last affected rows
     */
    final public static function affected_rows() {
		if(is_bool(self::$driver) || empty(self::$driver)) {
			return false;
		}
		return self::$driver->affected_rows();
	}

    /**
     * Last exists id in last insert query
     * @return bool|int Last id in last insert query
     */
    final public static function insert_id() {
		if(is_bool(self::$driver) || empty(self::$driver)) {
			return false;
		}
		return self::$driver->insert_id();
	}

    /**
     * Return fields query
     * @return bool|int Number fields in query
     */
    final public static function num_fields() {
		if(is_bool(self::$driver) || empty(self::$driver)) {
			return false;
		}
		return self::$driver->field_count();
	}

    /**
     * Return array without associative
     * @param string $query Needed query
     * @return bool|array Return array without associative
     */
    final public static function fetch_row($query = "") {
		if(is_bool(self::$driver) || empty(self::$driver)) {
			return false;
		}
		if(empty($query)) {
			$query = self::$qid;
		}
		return self::$driver->fetch_row($query);
	}

    /**
     * Return array with associative
     * @param string $query Needed query
     * @return bool|array Return array with associative
     */
    final public static function fetch_array($query = "") {
		if(is_bool(self::$driver) || empty(self::$driver)) {
			return false;
		}
		if(empty($query)) {
			$query = self::$qid;
		}
		return self::$driver->fetch_array($query);
	}

    /**
     * Return array only associative
     * @param string $query Needed query
     * @return bool|array Return array only associative
     */
    final public static function fetch_assoc($query = "") {
		if(is_bool(self::$driver) || empty(self::$driver)) {
			return false;
		}
		if(empty($query)) {
			$query = self::$qid;
		}
		return self::$driver->fetch_assoc($query);
	}

    /**
     * Return array only associative
     * @param string $query Needed query
     * @param string $class_name Class for returned
     * @param array $params Parameters send in constructor classes
     * @return bool|array Return object
     */
    final public static function fetch_object($query = "", $class_name = "", $params = array()) {
		if(is_bool(self::$driver) || empty(self::$driver)) {
			return false;
		}
		if(empty($query)) {
			$query = self::$qid;
		}
		if(sizeof($params)>0) {
			return self::$driver->fetch_object($query, $class_name, $params);
		} else {
			return self::$driver->fetch_object($query, $class_name);
		}
	}

    /**
     * Return number rows in query
     * @param string $query Needed query
     * @return bool|int Number rows in query
     */
    final public static function num_rows($query = "") {
		if(is_bool(self::$driver) || empty(self::$driver)) {
			return false;
		}
		if(empty($query)) {
			$query = self::$qid;
		}
		return self::$driver->num_rows($query);
	}

    /**
     * Free memory after execute query
     * @param string $query Needed query
     * @return bool|mixed Return result free or return false if error
     */
    final public static function free($query = "") {
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

    /**
     * Closed connection
     * @return bool Return result closing
     */
    final public static function close() {
		if(is_bool(self::$driver) || empty(self::$driver)) {
			return false;
		}
		return self::$driver->close();
	}

    /**
     * Result error info
     * @param array $arr Info of query
     */
    final public static function error($arr) {
		if(function_exists("errorHeader")) { errorHeader(); }
		$mysql_error = $arr['mysql_error'];
		$mysql_error_num = $arr['mysql_error_num'];
		$query = $arr['query'];
		
		if(in_array($mysql_error_num, array(145, 144, 135, 136, 126, 127))) {
			self::RePair();
		}

		if(!defined("DEBUG_DB")) {
			return "";
		}

		if($query) {
			// Safify query
			$query = preg_replace("/([0-9a-f]){32}/", "********************************", $query); // Hides all hashes
		}

		$queryS = htmlspecialchars($query, ENT_QUOTES, 'ISO-8859-1');
		$mysql_error = htmlspecialchars($mysql_error, ENT_QUOTES, 'ISO-8859-1');

		$trace = debug_backtrace();

		$level = sizeof($trace)-1;
		for($i=$level;$i>=0;$i--) {
			if($trace[$i]['function']=="query" || $trace[$i]['function']=="doquery") {
				$level = $i;
				break;
			}
		}
		if(defined("ROOT_PATH")) {
			$trace[$level]['file'] = str_replace(ROOT_PATH, "", $trace[$level]['file']);
		}
		if(!defined("IS_CLI") && self::$driver->get_type() === 1 && class_exists("modules") && method_exists("modules", "init_templates")) {
            $tmp = modules::init_templates();
            $tmp->dir_skins("skins".DS);
            $tmp->assign_vars(array(
				"query" => $queryS,
				"error" => $mysql_error,
				"error_num" => $mysql_error_num,
				"file" => $trace[$level]['file'],
				"line" => $trace[$level]['line'],
			));
			echo $tmp->completed_assign_vars("mysql_error", "core");
		} elseif(!defined("IS_CLI")) {
			echo "<center><br />".$trace[$level]['file'].":".$trace[$level]['line']."<hr />Query:<br /><textarea cols=\"40\" rows=\"5\">".$queryS."</textarea><hr />[".$mysql_error_num."] ".$mysql_error."<br />";
		} elseif(defined("IS_CLI")) {
			echo "\e[0;41m[ERROR]\e[0m\n".$trace[$level]['file']." [".$trace[$level]['line']."]\n\n\e[0;36m[".$mysql_error_num."] ".$mysql_error."\e[0m\n\n-------\n\n".escapeshellcmd($query)."\n\n-------\n\n\n";
		}
		exit();
	}
	
	function backupDB($tables = '*', $path = "") {
		if(empty($path) && defined("ROOT_PATH") && defined("DS") && (!file_exists(PATH_CACHE_USERDATA) || !is_writable(PATH_CACHE_USERDATA))) {
			return false;
		}
        $pathToSave = "";
		if(!empty($path) && defined("ROOT_PATH") && (!file_exists(ROOT_PATH.$path) || !is_writable(ROOT_PATH.$path))) {
			return false;
		} else {
			$pathToSave = ROOT_PATH.$path;
		}
		if(!empty($path) && (!file_exists(dirname(__FILE__). DIRECTORY_SEPARATOR .$path) || !is_writable(dirname(__FILE__). DIRECTORY_SEPARATOR .$path))) {
			return false;
		} else {
			$pathToSave = dirname(__FILE__). DIRECTORY_SEPARATOR .$path;
		}
		if(empty($pathToSave)) {
			return false;
		}
		$len = DIRECTORY_SEPARATOR;
		$exp = explode($len, $pathToSave);
		$end = end($exp);
		if(empty($end)) {
			$pathToSave .= $len;
		}
		if($tables == '*') {
			$tables = array();
			$result = self::query('SHOW TABLES');
			while($row = self::fetch_row($result)) {
				$tables[] = $row[0];
			}
		} else {
			$tables = is_array($tables) ? $tables : explode(',',$tables);
		}
        $return = "";
		foreach($tables as $table) {
			$result = self::query('SELECT * FROM '.$table);
			$num_fields = self::num_fields($result);
			$return .= 'DROP TABLE '.$table.';';
			$tableCreate = self::query('SHOW CREATE TABLE '.$table);
			$row2 = self::fetch_row($tableCreate);
			$return.= "\n\n".$row2[1].";\n\n";
			for($i=0;$i<$num_fields;$i++) {
				while($row = self::fetch_row($result)) {
					$return.= 'INSERT INTO '.$table.' VALUES(';
					for($j=0;$j<$num_fields;$j++) {
						if(!get_magic_quotes_gpc()) {
							$row[$j] = addslashes($row[$j]);
						}
						$row[$j] = preg_replace("#\n#", "\\n", $row[$j]);
						if(isset($row[$j])) {
							$return.= '"'.$row[$j].'"' ;
						} else {
							$return.= '""';
						}
						if($j<($num_fields-1)) {
							$return.= ',';
						}
					}
					$return.= ");\n";
				}
			}
			$return .= "\n\n\n";
		}
		file_put_contents($pathToSave.'db-backup-'.time().'-'.(md5(implode(',',$tables))).'.sql', $return);
		return true;
	}
	
    /**
     * Close connection
     */
    function __destruct() {
		//self::close();
	}

}