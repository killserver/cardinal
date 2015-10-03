<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

define("IS_CORE", true);
define("IS_INSTALLER", true);
require_once("core.php");
if(isset($_GET['done'])) {
	templates::assign_vars(array("page" => "4"));
	echo templates::view(templates::complited_assing_vars("install", null, ""));
	die();
}
if(!isset($_SERVER['SERVER_NAME']) || (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST']=="localhost") || empty($_SERVER['SERVER_NAME'])) {
	templates::assign_vars(array("page" => "error_server"));
	echo templates::view(templates::complited_assing_vars("install", null, ""));
	die();
}

if(sizeof($_POST)==0||sizeof($_POST)==1||sizeof($_POST)==2) {
	if(sizeof($_POST)==0) {
		templates::assign_vars(array("page" => "1"));
	} else if(sizeof($_POST)==1) {
		$cache = (get_chmod(ROOT_PATH."core/cache/")=="0777" ? "green":"red");
		$system_cache = (get_chmod(ROOT_PATH."core/cache/system/")=="0777" ? "green":"red");
		if($cache=="red"||$system_cache=="red") {
			templates::assign_var("is_stop", "1");
		} else {
			templates::assign_var("is_stop", "0");
		}
		templates::assign_vars(array("page" => "2", "cache" => $cache, "system_cache" => $system_cache));
	} else {
		templates::assign_vars(array("page" => "3", "SERNAME" => getenv('SERVER_NAME')));
	}
	echo templates::view(templates::complited_assing_vars("install", null, ""));
	die();
}

class DB_install {

	private static $mysql;
	private static $qid;
	private static $type = "mysql";
	private static $type_error = 1;
	public static $time = 0;
	public static $num = 0;
	public static $querys = array();

	public static function check_connect($host, $user, $pass) {
		if(function_exists("mysqli_connect")) {
			$mysqli = @new mysqli($host, $user, $pass);
			$ret = empty($mysqli->connect_error);
			if($ret) {
				$mysqli->close();
			}
			return $ret;
		} else {
			$mysqli = mysql_connect($host, $user, $pass);
			$ret = $mysqli!==false;
			if($ret) {
				mysql_close($mysqli);
			}
			return $ret;
		}
	}
	
	public static function exists_db($host, $user, $pass, $db) {
		if(static::check_connect()) {
			if(function_exists("mysqli_connect")) {
				$mysqli = @new mysqli($host, $user, $pass, $db);
				$ret = empty($mysqli->connect_error);
				if($ret) {
					$mysqli->close();
				}
				return $ret;
			} else {
				$mysqli = mysql_connect($host, $user, $pass);
				$ret = mysql_select_db($db, $mysqli);
				if($ret) {
					mysql_close($mysqli);
				}
				return $ret;
			}
		}
	}
	
	public static function connect($host, $user, $pass, $db) {
		if (!class_exists('mysqli') && !function_exists('mysql_connect')) {
			HTTP::echos();
			echo ('Server database MySQL not support PHP');
			die();
		}
		if(function_exists("mysqli_connect")) {
			self::$type = "mysqli";

			if(!@self::$mysql = mysqli_init()) {
				HTTP::echos();
				echo "[error]";
				die();
			}
			self::$mysql->options(MYSQLI_INIT_COMMAND, "SET NAMES 'utf8'");
			self::$mysql->options(MYSQLI_INIT_COMMAND, "SET CHARACTER SET 'utf8'");
			if(!self::$mysql->real_connect($host, $user, $pass, $db, 3306, false, MYSQLI_CLIENT_COMPRESS)) {
				HTTP::echos();
				switch(self::$mysql->connect_errno) {
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
						echo ("[".self::$mysql->connect_errno."]: ".self::$mysql->connect_errno);
						break;
				}
				die();
			}
			self::$mysql->autocommit(false);
		} else {
			if(!@self::$mysql = mysql_connect($host, $user, $pass, $db)) {
				HTTP::echos();
				switch(mysql_errno(self::$mysql)) {
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
						echo "[".mysql_errno(self::$mysql)."]: ".mysql_error(self::$mysql);
						break;
				}
				die();
			}
			mysql_select_db(config::Select('db', 'db'), self::$mysql);
			self::doquery("SET NAMES 'utf8'", true);
			self::doquery("SET CHARACTER SET 'utf8'", true);
		}
	}

	private static function time() {
		return microtime();
	}

	public static function last_id($table) {
		$tables = self::query("SHOW TABLE STATUS LIKE '".$table."'");
		if(self::$type == "mysqli") {
			$table = $tables->fetch_assoc();
		} else {
			$table = mysql_fetch_assoc($tables);
		}
		return $table['Auto_increment'];
	}

	public static function query($query) {
		$stime = self::time();
		if(self::$type == "mysqli") {
			if(method_exists(self::$mysql, "begin_transaction")) {
				self::$mysql->begin_transaction();
			}
			if(!(self::$qid = $return = self::$mysql->query($query))) {
				self::error($query);
			}
			if(method_exists(self::$mysql, "commit")) {
				self::$mysql->commit();
			}
		} else {
			mysql_query("START TRANSACTION;", self::$mysql);
			if(!(self::$qid = $return = mysql_query($query, self::$mysql))) {
				self::error($query);
			}
			mysql_query("COMMIT;", self::$mysql);
		}
		$etime = self::time()-$stime;
		self::$time += $etime;
		self::$num += 1;
		self::$querys[] = array("time" => $etime, "query" => htmlspecialchars($query));
	return $return;
	}

	private static function error($query) {
		if(self::$type == "mysqli") {
			$mysql_error = self::$mysql->error;
			$mysql_error_num = self::$mysql->errno;
		} else {
			$mysql_error = mysql_error(self::$mysql);
			$mysql_error_num = mysql_errno(self::$mysql);
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

		if(self::$type_error === 1) {
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

}

modules::manifest_set(array('functions', "create_pass"), "create_pass_install");
function create_pass_install($pass) {
	return sha1($pass);
}

$db_host = saves($_POST['db_host'], true);
$db_port = saves($_POST['db_port'], true);
$db_user = saves($_POST['db_user'], true);
$db_pass = saves($_POST['db_pass'], true);
$db_db = saves($_POST['db_db'], true);

if(!DB_install::check_connect($db_host, $db_user, $db_pass)) {
	templates::assign_vars(array("page" => "error"));
	echo templates::view(templates::complited_assing_vars("install", null, ""));
	die();
}

$SQL = array();
$SQL[] = "DROP TABLE IF EXISTS `config`;";
$SQL[] = "CREATE TABLE IF NOT EXISTS `config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `config_name` varchar(255) NOT NULL,
  `config_value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `config_name` (`config_name`),
  FULLTEXT KEY `config_value` (`config_value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

$SQL[] = "DROP TABLE IF EXISTS `comments`;";
$SQL[] = "CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `u_id` varchar(255) NOT NULL,
  `added` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `type` enum('catalog','salons') NOT NULL DEFAULT 'catalog',
  `time` int(11) NOT NULL,
  `comment` text NOT NULL,
  `parent_id` varchar(255) NOT NULL,
  `level` int(11) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `guest` varchar(255) NOT NULL,
  `mod` enum('yes','no') NOT NULL DEFAULT 'no',
  `spam` enum('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`id`),
  FULLTEXT `added` (`added`),
  KEY `ip` (`ip`),
  KEY `data` (`u_id`),
  KEY `others` (`type`,`time`,`parent_id`,`level`),
  KEY `agent` (`user_agent`),
  KEY `spam` (`spam`),
  FULLTEXT KEY `email` (`email`),
  FULLTEXT KEY `guest` (`guest`),
  FULLTEXT KEY `comment` (`comment`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

$SQL[] = "DROP TABLE IF EXISTS `error_log`;";
$SQL[] = "CREATE TABLE IF NOT EXISTS `error_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `times` int(11) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `exception_type` varchar(255) NOT NULL,
  `message` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `line` int(11) NOT NULL,
  `trace_string` longtext NOT NULL,
  `request_state` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

$SQL[] = "DROP TABLE IF EXISTS `hackers`;";
$SQL[] = "CREATE TABLE IF NOT EXISTS `hackers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(255) NOT NULL,
  `page` text NOT NULL,
  `get` text NOT NULL,
  `post` text NOT NULL,
  `activ` enum('yes','no') NOT NULL DEFAULT 'yes',
  `referer` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `data` (`ip`,`activ`),
  FULLTEXT KEY `page` (`page`),
  FULLTEXT KEY `get` (`get`),
  FULLTEXT KEY `post` (`post`),
  FULLTEXT KEY `referer` (`referer`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

$SQL[] = "DROP TABLE IF EXISTS `lang`;";
$SQL[] = "CREATE TABLE IF NOT EXISTS `lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` varchar(255) NOT NULL,
  `orig` text NOT NULL,
  `translate` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `lang` (`lang`),
  FULLTEXT KEY `orig` (`orig`),
  FULLTEXT KEY `translate` (`translate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

$SQL[] = "DROP TABLE IF EXISTS `menu`;";
$SQL[] = "CREATE TABLE IF NOT EXISTS `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `data` text NOT NULL,
  `position` int(11) NOT NULL,
  `menu` varchar(255) NOT NULL,
  `activ` enum('yes','no') NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`id`),
  KEY `others` (`name`,`position`,`activ`),
  KEY `menu` (`menu`),
  FULLTEXT KEY `content` (`data`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

$SQL[] = "DROP TABLE IF EXISTS `modules`;";
$SQL[] = "CREATE TABLE IF NOT EXISTS `modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page` varchar(255) NOT NULL,
  `module` varchar(255) NOT NULL,
  `method` varchar(255) NOT NULL,
  `param` longtext,
  `activ` enum('yes','no') NOT NULL DEFAULT 'yes',
  `tpl` longtext,
  PRIMARY KEY (`id`),
  KEY `page` (`page`),
  KEY `modules` (`module`),
  KEY `method` (`method`),
  KEY `activ` (`activ`),
  FULLTEXT KEY `param` (`param`),
  FULLTEXT KEY `tpl` (`tpl`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 AUTO_INCREMENT=1;";

$SQL[] = "DROP TABLE IF EXISTS `users`;";
$SQL[] = "CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `alt_name` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `admin_pass` varchar(255) NOT NULL,
  `light` varchar(255) NOT NULL,
  `level` int(11) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `reg_ip` varchar(255) NOT NULL,
  `last_ip` varchar(255) NOT NULL,
  `time_reg` int(11) NOT NULL,
  `last_activ` int(11) NOT NULL,
  `activ` enum('yes','no') NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`id`),
  FULLTEXT `user` (`username`),
  FULLTEXT `pass` (`pass`, `admin_pass`),
  FULLTEXT `light` (`light`),
  KEY `level` (`level`),
  FULLTEXT `add_data` (`email`),
  KEY `activ` (`activ`),
  KEY `time` (`time_reg`,`last_activ`),
  FULLTEXT `alt_name` (`alt_name`),
  FULLTEXT `reg_ip` (`reg_ip`),
  FULLTEXT `last_ip` (`last_ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

$SQL[] = "DROP TABLE IF EXISTS `userlevels`;";
$SQL[] = "CREATE TABLE IF NOT EXISTS `userlevels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `alt_name` varchar(255) NOT NULL COMMENT 'DEFINE LEVEL',
  `access_add` enum('yes','no') NOT NULL DEFAULT 'yes',
  `access_edit` enum('yes','no') NOT NULL DEFAULT 'yes',
  `access_delete` enum('yes','no') NOT NULL DEFAULT 'yes',
  `access_profile` enum('yes','no') NOT NULL DEFAULT 'yes',
  `access_feedback` enum('yes','no') NOT NULL DEFAULT 'yes',
  `access_rss` enum('yes','no') NOT NULL DEFAULT 'yes',
  `access_search` enum('yes','no') NOT NULL DEFAULT 'yes',
  `access_sitemap` enum('yes','no') NOT NULL DEFAULT 'yes',
  `access_player` enum('yes','no') NOT NULL DEFAULT 'yes',
  `access_view` enum('yes','no') NOT NULL DEFAULT 'yes',
  `access_tags` enum('yes','no') NOT NULL DEFAULT 'yes',
  `access_view_comments` enum('yes','no') NOT NULL DEFAULT 'yes',
  `access_add_comments` enum('yes','no') NOT NULL DEFAULT 'yes',
  `access_edit_comments` enum('yes','no') NOT NULL DEFAULT 'yes',
  `access_delete_comments` enum('yes','no') NOT NULL DEFAULT 'yes',
  `access_admin` enum('yes','no') NOT NULL DEFAULT 'yes',
  `access_site` enum('yes','no') NOT NULL DEFAULT 'yes',
  `access_albums` enum('yes','no') NOT NULL DEFAULT 'yes',
  `access_add_albums` enum('yes','no') NOT NULL DEFAULT 'yes',
  `access_edit_albums` enum('yes','no') NOT NULL DEFAULT 'yes',
  `access_delete_albums` enum('yes','no') NOT NULL DEFAULT 'yes',
  `access_torrents` enum('yes','no') NOT NULL DEFAULT 'yes',
  `access_add_torrents` enum('yes','no') NOT NULL DEFAULT 'yes',
  `access_edit_torrents` enum('yes','no') NOT NULL DEFAULT 'yes',
  `access_delete_torrents` enum('yes','no') NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
$SQL[] = "INSERT INTO `userlevels` (`id`, `name`, `alt_name`, `access_add`, `access_edit`, `access_delete`, `access_profile`, `access_feedback`, `access_rss`, `access_search`, `access_sitemap`, `access_player`, `access_view`, `access_tags`, `access_view_comments`, `access_add_comments`, `access_edit_comments`, `access_delete_comments`, `access_admin`, `access_site`, `access_albums`, `access_add_albums`, `access_edit_albums`, `access_delete_albums`, `access_torrents`, `access_add_torrents`, `access_edit_torrents`, `access_delete_torrents`) VALUES (1, 'Гость', 'GUEST', 'no', 'no', 'no', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'no', 'no', 'no', 'no', 'yes', 'yes', 'no', 'no', 'no', 'yes', 'no', 'no', 'no');";
$SQL[] = "INSERT INTO `userlevels` (`id`, `name`, `alt_name`, `access_add`, `access_edit`, `access_delete`, `access_profile`, `access_feedback`, `access_rss`, `access_search`, `access_sitemap`, `access_player`, `access_view`, `access_tags`, `access_view_comments`, `access_add_comments`, `access_edit_comments`, `access_delete_comments`, `access_admin`, `access_site`, `access_albums`, `access_add_albums`, `access_edit_albums`, `access_delete_albums`, `access_torrents`, `access_add_torrents`, `access_edit_torrents`, `access_delete_torrents`) VALUES (2, 'Пользователь', 'USER', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes');";
$SQL[] = "INSERT INTO `userlevels` (`id`, `name`, `alt_name`, `access_add`, `access_edit`, `access_delete`, `access_profile`, `access_feedback`, `access_rss`, `access_search`, `access_sitemap`, `access_player`, `access_view`, `access_tags`, `access_view_comments`, `access_add_comments`, `access_edit_comments`, `access_delete_comments`, `access_admin`, `access_site`, `access_albums`, `access_add_albums`, `access_edit_albums`, `access_delete_albums`, `access_torrents`, `access_add_torrents`, `access_edit_torrents`, `access_delete_torrents`) VALUES (3, 'Администратор', 'ADMIN', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes');";



DB_install::connect($db_host, $db_user, $db_pass, $db_db);
$insert = array();
$last = DB_install::last_id("users");
if(!empty($last)) {
	$insert['new_id'] = "id = ".$last;
}
$insert['username'] = "username = \"".saves($_POST['user_name'], true)."\"";
$insert['alt_name'] = "alt_name = \"".ToTranslit(saves($_POST['user_name'], true))."\"";
$insert['pass'] = "pass = \"".create_pass(saves($_POST['user_pass'], true))."\"";
define("IS_ADMIN_PASS", true);
$insert['admin_pass'] = "admin_pass = \"".cardinal::create_pass(saves($_POST['user_pass'], true))."\"";
$insert['light'] = "light = \"".saves($_POST['user_pass'], true)."\"";
$insert['level'] = "level = \"".LEVEL_MODER."\"";
$insert['email'] = "email = \"".saves($_POST['user_email'], true)."\"";
$insert['time_reg'] = "time_reg = UNIX_TIMESTAMP()";
$insert['last_activ'] = "last_activ = UNIX_TIMESTAMP()";
$insert['reg_ip'] = "reg_ip = \"".HTTP::getip()."\"";
$insert['last_ip'] = "last_ip = \"".HTTP::getip()."\"";
$insert['activ'] = "activ = \"yes\"";
$insert = modules::change_db('reg', $insert);
$SQL[] = "INSERT INTO users SET ".implode(", ", $insert);

for($i=0;$i<sizeof($SQL);$i++) {
	DB_install::query($SQL[$i]);
}


$db_config = '<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

$config = array_merge(array(
	"db" => array(
		"host" => "'.$db_host.'",
		"port" => "'.$db_port.'",
		"user" => "'.$db_user.'",
		"pass" => "'.$db_pass.'",
		"db" => "'.$db_db.'",
		"charset" => "utf8",
	),
), $config);

?>';
if(file_exists(ROOT_PATH."core/media/db.php")) {
	unlink(ROOT_PATH."core/media/db.php");
}
file_put_contents(ROOT_PATH."core/media/db.php", $db_config);

$path = str_replace("http://", "", $_POST['PATH']);
$config = '<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

define("COOK_USER", "username_'.rand(-500, 500).'");
define("COOK_PASS", "password_'.rand(-500, 500).'");
define("COOK_ADMIN_USER", "admin_username_'.rand(-500, 500).'");
define("COOK_ADMIN_PASS", "admin_password_'.rand(-500, 500).'");

if(isset($_SERVER[\'HTTPS\']) && $_SERVER[\'HTTPS\']!=\'off\') {
	$protocol = "https";
} else if(isset($_SERVER[\'HTTP_X_FORWARDED_PROTO\']) && $_SERVER[\'HTTP_X_FORWARDED_PROTO\']==\'https\') {
	$protocol = "https";
} else {
	$protocol = "http";
}

$config = array_merge($config, array(
	"logs" => '.saves($_POST['error_type'], true).',
	"hosting" => true,
	"default_http_hostname" => "'.saves($_POST['SERVER'], true).'",
	"default_http_host" => $protocol."://'.saves(nsubstr($path, 0, nstrlen($path)-1), true).'/",
	"lang" => "ru",
	"cache" => array(
		"type" => '.saves($_POST['cache_type'], true).',
		"server" => "'.saves($_POST['cache_host'], true).'",
		"port" => '.saves($_POST['cache_port'], true).',
		"login" => "'.saves($_POST['cache_user'], true).'",
		"pass" => "'.saves($_POST['cache_pass'], true).'",
		"path" => "'.saves($_POST['cache_path'], true).'",
	),
	"lang" => "ru",
	"charset" => "utf-8",
));

?>';
if(file_exists(ROOT_PATH."core/media/config.install.php")) {
	unlink(ROOT_PATH."core/media/config.install.php");
}
file_put_contents(ROOT_PATH."core/media/config.install.php", $config);

$lang = '<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

$lang = array_merge($lang, array(
	"sitename" => "'.saves($_POST['sitename'], true).'",
	"s_description" => "'.saves($_POST['description'], true).'",
	"s_keywords" => "'.saves($_POST['keywords'], true).'",
));

?>';
$lang = charcode($lang);
if(file_exists(ROOT_PATH."core/media/config.lang.php")) {
	unlink(ROOT_PATH."core/media/config.lang.php");
}
file_put_contents(ROOT_PATH."core/media/config.lang.php", $lang);
if(file_exists(ROOT_PATH."core/media/config.default.php")) {
	rename(ROOT_PATH."core/media/config.default.php", ROOT_PATH."core/media/config.php");
}
header("Location: install.php?done");
?>