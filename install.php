<?php
/*
 *
 * @version 3.1
 * @copyright 2014-2016 KilleR for Cardinal Engine
 *
 * Version Engine: 3.1
 * Version File: 3
 *
 * 2.1
 * add sql query for support updater
 * 2.2
 * add support stop installing without need modules in php
 * 2.3
 * add support "drivers" - submodules for database
 * 2.4
 * add support for localhost?
 * 2.5
 * fix time cron
 * 2.6
 * add support speed updates
 * 2.7
 * add support routification on files
 * 2.8
 * fix support routification on files and fix error on table-list
 * 2.9
 * fix bugs on routification and add support admin level
 * 3.0
 * add support breadcrumbs for view step installing and view version php and apache, remove russian language in template installer
 *
*/
if(!defined("IS_CORE")) {
	define("IS_CORE", true);
}
if(!defined("IS_INSTALLER")) {
	define("IS_INSTALLER", true);
}
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."core.php");
if(strpos($_SERVER['REQUEST_URI'], "index.".ROOT_EX)!==false) {
	$exp = explode("index.".ROOT_EX, $_SERVER['REQUEST_URI']);
} else {
	$exp = explode("install", $_SERVER['REQUEST_URI']);
}
config::Set("default_http_local", $exp[0]);
templates::assign_var("isRewrite", false);
if(isset($_POST['rewrite'])) {
	config::Set("rewrite", false);
	templates::assign_var("isRewrite", true);
	unset($_POST['rewrite']);
}
lang::include_lang("install");
Route::Config(array(
	"rewrite" => config::Select("rewrite"),
	"default_http_host" => config::Select("default_http_host"),
));
if((isset($_SERVER[ROUTE_GET_URL]) && strpos($_SERVER[ROUTE_GET_URL], "install/done")!==false) || isset($_GET['done'])) {
	templates::assign_vars(array("page" => "4"));
	echo templates::view(templates::completed_assign_vars("install", "core", ""));
	die();
}

if(sizeof($_POST)==0||(sizeof($_POST)==1)||(sizeof($_POST)==2)) {
	if(sizeof($_POST)==0) {
		Route::RegParam("line", "1");
		templates::assign_vars(array("page" => "1"));
	} else if(sizeof($_POST)==1) {
		Route::RegParam("line", "2");
		if(function_exists("apache_get_version")) {
			$apache = apache_get_version();
			if(strpos($apache, "/")!==false) {
				$apache = substr($apache, strlen("Apache/"));
				$apache = (intval($apache)>=2 ? "green":"red");
			} else {
				$apache = "green";
			}
		} else {
			$apache = "green";
		}
		$php = (PHP_VERSION_ID>=50302 ? "green":"red");
		$cache = (get_chmod(PATH_CACHE)=="0777" ? "green":"red");
		$system_cache = (get_chmod(PATH_CACHE_SYSTEM)=="0777" ? "green":"red");
		$template = (get_chmod(PATH_CACHE_TEMP)=="0777" ? "green":"red");
		$media = (get_chmod(PATH_MEDIA)=="0777" ? "green":"red");
		$mb = (function_exists('mb_detect_encoding') ? "green" : "red");
		if($apache=="red"||$php=="red"||$cache=="red"||$system_cache=="red"||$media=="red"||$mb=="red") {
			templates::assign_var("is_stop", "1");
		} else {
			templates::assign_var("is_stop", "0");
		}
		templates::assign_vars(array("page" => "2", "apache" => $apache, "php" => $php, "cache" => $cache, "system_cache" => $system_cache, "template" => $template, "media" => $media, "mb" => $mb));
	} else {
		templates::assign_vars(array("page" => "3", "SERNAME" => getenv('SERVER_NAME').str_replace(array("index.".ROOT_EX."/", "install.".ROOT_EX, "/install/step2", "/install/step3"), "", getenv("REQUEST_URI")), "SERVERS" => getenv('SERVER_NAME')));
		$driver = PATH_DB_DRIVERS;
		$dirs = read_dir($driver, ".".ROOT_EX);
		sort($dirs);
		for($i=0;$i<sizeof($dirs);$i++) {
			if($dirs[$i]=="index.".ROOT_EX||$dirs[$i]=="index.html"||$dirs[$i]=="DriverParam.".ROOT_EX||$dirs[$i]=="drivers.".ROOT_EX||$dirs[$i]=="DBObject.".ROOT_EX||$dirs[$i]=="QueryBuilder.".ROOT_EX) {
				continue;
			}
			include_once($driver.$dirs[$i]);
			$dr_subname = str_replace(".".ROOT_EX, "", $dirs[$i]);
			if(!class_exists($dr_subname)) {
				continue;
			}
			$dr_name = $dr_subname::$subname;
			templates::assign_vars(array("name" => $dr_subname, "value" => $dr_name), "drivers", "driver".$i);
		}
	}
	echo templates::view(templates::completed_assign_vars("install", "core", ""));
	die();
}

modules::manifest_set(array('functions', "create_pass"), "create_pass_install");
function create_pass_install($pass) {
	return sha1($pass);
}

$db_host = Saves::SaveOld(Arr::get($_POST, 'db_host'), true);
$db_port = Saves::SaveOld(Arr::get($_POST, 'db_port'), true);
$db_user = Saves::SaveOld(Arr::get($_POST, 'db_user'), true);
$db_pass = Saves::SaveOld(Arr::get($_POST, 'db_pass'), true);
$db_db = Saves::SaveOld(Arr::get($_POST, 'db_db'), true);
$db_driver = Saves::SaveOld(Arr::get($_POST, 'db_driver'), true);
$db_prefix = Saves::SaveOld(Arr::get($_POST, 'db_prefix', "cardinal_"), true);

db::changeDriver($db_driver);
db::OpenDriver();
if(!db::check_connect($db_host, $db_user, $db_pass)) {
	templates::assign_vars(array("page" => "error"));
	echo templates::view(templates::completed_assign_vars("install", "core", ""));
	die();
}

$SQL = array();
$SQL[] = "SET FOREIGN_KEY_CHECKS = 0;";
$SQL[] = "DROP TABLE IF EXISTS `".$db_prefix."category`;";
$SQL[] = "CREATE TABLE IF NOT EXISTS `".$db_prefix."category` (
  `cat_id` int(11) NOT NULL AUTO_INCREMENT,
  `alt_name` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `parent` int(11) NOT NULL,
  `sort` int(11) NOT NULL,
  PRIMARY KEY (`cat_id`),
  KEY `all` (`parent`,`sort`),
  FULLTEXT KEY `name` (`name`),
  FULLTEXT KEY `alt_name` (`alt_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$SQL[] = "DROP TABLE IF EXISTS `".$db_prefix."config`;";
$SQL[] = "CREATE TABLE IF NOT EXISTS `".$db_prefix."config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `config_name` varchar(255) NOT NULL,
  `config_value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `config_name` (`config_name`),
  FULLTEXT KEY `config_value` (`config_value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
$SQL[] = "INSERT INTO `".$db_prefix."config` SET `config_name` = \"db_version\", `config_value` = \"".VERSION."\"";
$SQL[] = "INSERT INTO `".$db_prefix."config` SET `config_name` = \"cardinal_time\", `config_value` = \"\"";

$SQL[] = "DROP TABLE IF EXISTS `".$db_prefix."comments`;";
$SQL[] = "CREATE TABLE IF NOT EXISTS `".$db_prefix."comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `u_id` varchar(255) NOT NULL,
  `added` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
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

$SQL[] = "DROP TABLE IF EXISTS `".$db_prefix."error_log`;";
$SQL[] = "CREATE TABLE IF NOT EXISTS `".$db_prefix."error_log` (
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

/*$SQL[] = "DROP TABLE IF EXISTS `".$db_prefix."hackers`;";
$SQL[] = "CREATE TABLE IF NOT EXISTS `".$db_prefix."hackers` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";*/

$SQL[] = "DROP TABLE IF EXISTS `".$db_prefix."lang`;";
$SQL[] = "CREATE TABLE IF NOT EXISTS `".$db_prefix."lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` varchar(255) NOT NULL,
  `orig` text NOT NULL,
  `translate` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `lang` (`lang`),
  FULLTEXT KEY `orig` (`orig`),
  FULLTEXT KEY `translate` (`translate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

$SQL[] = "DROP TABLE IF EXISTS `".$db_prefix."menu`;";
$SQL[] = "CREATE TABLE IF NOT EXISTS `".$db_prefix."menu` (
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

$SQL[] = "DROP TABLE IF EXISTS `".$db_prefix."modules`;";
$SQL[] = "CREATE TABLE IF NOT EXISTS `".$db_prefix."modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page` varchar(255) NOT NULL,
  `module` varchar(255) NOT NULL,
  `method` varchar(255) NOT NULL,
  `param` longtext not null,
  `activ` enum('yes','no') NOT NULL DEFAULT 'yes',
  `tpl` longtext not null,
  `file` varchar(255) NOT NULL,
  `type` enum('admincp','site') NOT NULL DEFAULT 'site',
  PRIMARY KEY (`id`),
  FULLTEXT `page` (`page`),
  FULLTEXT `modules` (`module`),
  FULLTEXT `method` (`method`),
  KEY `activ` (`activ`),
  FULLTEXT KEY `param` (`param`),
  FULLTEXT KEY `tpl` (`tpl`),
  FULLTEXT KEY `file` (`file`),
  KEY `type` (`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
$SQL[] = "INSERT INTO `".$db_prefix."modules` SET `module` = 'base', `activ` = 'yes', `file` = '".str_replace(array(ROOT_PATH, DS), array("", DS_DB), PATH_MODULES)."base.class.".ROOT_EX."';";
$SQL[] = "INSERT INTO `".$db_prefix."modules` SET `module` = 'changelog', `activ` = 'yes', `file` = '".str_replace(array(ROOT_PATH, DS), array("", DS_DB), PATH_MODULES)."changelog.class.".ROOT_EX."';";
$SQL[] = "INSERT INTO `".$db_prefix."modules` SET `module` = 'mobile_detect', `activ` = 'yes', `file` = '".str_replace(array(ROOT_PATH, DS), array("", DS_DB), PATH_MODULES)."mobile.class.".ROOT_EX."';";
$SQL[] = "INSERT INTO `".$db_prefix."modules` SET `module` = 'SEOBlock', `activ` = 'yes', `file` = '".str_replace(array(ROOT_PATH, DS), array("", DS_DB), PATH_MODULES)."SEOBlock.class.".ROOT_EX."';";
$SQL[] = "INSERT INTO `".$db_prefix."modules` SET `module` = 'DataTables', `activ` = 'yes', `file` = '".ADMINCP_DIRECTORY.DS_DB."pages".DS_DB."Viewer".DS_DB."Core".DS_DB."Plugins".DS_DB."CoreDataTables.".ROOT_EX."', `type` = 'admincp';";

$SQL[] = "DROP TABLE IF EXISTS `".$db_prefix."posts`;";
$SQL[] = "CREATE TABLE IF NOT EXISTS `".$db_prefix."posts` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `title` varchar(255) NOT NULL,
   `alt_name` varchar(255) NOT NULL,
   `image` varchar(255) NOT NULL,
   `descr` LONGTEXT NOT NULL,
   `cat_id` varchar(255) NOT NULL,
   `time` int(11) NOT NULL,
   `added` varchar(255) NOT NULL,
   `stat` int(11) NOT NULL,
   `active` enum('yes','no') NOT NULL DEFAULT 'no',
   `type` varchar(255) not null default 'post',
   PRIMARY KEY `id`(`id`),
   FULLTEXT `title_name` (`title`, `alt_name`),
   FULLTEXT `category` (`cat_id`),
   FULLTEXT `idescr`(`image`, `descr`),
   FULLTEXT `added`(`added`),
   KEY `active_time`(`active`, `time`),
   KEY `stat`(`stat`),
   KEY `type`(`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

$SQL[] = "DROP TABLE IF EXISTS `".$db_prefix."users`;";
$SQL[] = "CREATE TABLE IF NOT EXISTS `".$db_prefix."users` (
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

$SQL[] = "DROP TABLE IF EXISTS `".$db_prefix."userlevels`;";
$SQL[] = "CREATE TABLE IF NOT EXISTS `".$db_prefix."userlevels` (
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
$SQL[] = "INSERT INTO `".$db_prefix."userlevels` (`id`, `name`, `alt_name`, `access_add`, `access_edit`, `access_delete`, `access_profile`, `access_feedback`, `access_rss`, `access_search`, `access_sitemap`, `access_player`, `access_view`, `access_tags`, `access_view_comments`, `access_add_comments`, `access_edit_comments`, `access_delete_comments`, `access_admin`, `access_site`, `access_albums`, `access_add_albums`, `access_edit_albums`, `access_delete_albums`, `access_torrents`, `access_add_torrents`, `access_edit_torrents`, `access_delete_torrents`) VALUES (1, 'Гость', 'GUEST', 'no', 'no', 'no', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'no', 'no', 'no', 'no', 'yes', 'yes', 'no', 'no', 'no', 'yes', 'no', 'no', 'no');";
$SQL[] = "INSERT INTO `".$db_prefix."userlevels` (`id`, `name`, `alt_name`, `access_add`, `access_edit`, `access_delete`, `access_profile`, `access_feedback`, `access_rss`, `access_search`, `access_sitemap`, `access_player`, `access_view`, `access_tags`, `access_view_comments`, `access_add_comments`, `access_edit_comments`, `access_delete_comments`, `access_admin`, `access_site`, `access_albums`, `access_add_albums`, `access_edit_albums`, `access_delete_albums`, `access_torrents`, `access_add_torrents`, `access_edit_torrents`, `access_delete_torrents`) VALUES (2, 'Пользователь', 'USER', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes');";
$SQL[] = "INSERT INTO `".$db_prefix."userlevels` (`id`, `name`, `alt_name`, `access_add`, `access_edit`, `access_delete`, `access_profile`, `access_feedback`, `access_rss`, `access_search`, `access_sitemap`, `access_player`, `access_view`, `access_tags`, `access_view_comments`, `access_add_comments`, `access_edit_comments`, `access_delete_comments`, `access_admin`, `access_site`, `access_albums`, `access_add_albums`, `access_edit_albums`, `access_delete_albums`, `access_torrents`, `access_add_torrents`, `access_edit_torrents`, `access_delete_torrents`) VALUES (3, 'Модератор', 'ADMIN', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes');";
$SQL[] = "INSERT INTO `".$db_prefix."userlevels` (`id`, `name`, `alt_name`, `access_add`, `access_edit`, `access_delete`, `access_profile`, `access_feedback`, `access_rss`, `access_search`, `access_sitemap`, `access_player`, `access_view`, `access_tags`, `access_view_comments`, `access_add_comments`, `access_edit_comments`, `access_delete_comments`, `access_admin`, `access_site`, `access_albums`, `access_add_albums`, `access_edit_albums`, `access_delete_albums`, `access_torrents`, `access_add_torrents`, `access_edit_torrents`, `access_delete_torrents`) VALUES (4, 'Администратор', 'ADMIN', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes');";

$SQL[] = "SET FOREIGN_KEY_CHECKS = 1;";


db::connect($db_host, $db_user, $db_pass, $db_db, "utf8", $db_port);
$insert = array();
$last = db::last_id($db_prefix."users");
if(!empty($last)) {
	$insert['new_id'] = "id = ".$last;
}
$insert['username'] = "username = \"".Saves::SaveOld(Arr::get($_POST, 'user_name'), true)."\"";
$insert['alt_name'] = "alt_name = \"".ToTranslit(Saves::SaveOld(Arr::get($_POST, 'user_name'), true))."\"";
$insert['pass'] = "pass = \"".create_pass(Saves::SaveOld(Arr::get($_POST, 'user_pass'), true))."\"";
define("IS_ADMIN_PASS", true);
$insert['admin_pass'] = "admin_pass = \"".cardinal::create_pass(Saves::SaveOld(Arr::get($_POST, 'user_pass'), true))."\"";
$insert['light'] = "light = \"".Saves::SaveOld(Arr::get($_POST, 'user_pass'), true)."\"";
$insert['level'] = "level = \"".LEVEL_ADMIN."\"";
$insert['email'] = "email = \"".Saves::SaveOld(Arr::get($_POST, 'user_email'), true)."\"";
$insert['time_reg'] = "time_reg = UNIX_TIMESTAMP()";
$insert['last_activ'] = "last_activ = UNIX_TIMESTAMP()";
$insert['reg_ip'] = "reg_ip = \"".HTTP::getip()."\"";
$insert['last_ip'] = "last_ip = \"".HTTP::getip()."\"";
$insert['activ'] = "activ = \"yes\"";
$insert = modules::change_db('reg', $insert);
$SQL[] = "INSERT INTO ".$db_prefix."users SET ".implode(", ", $insert);

for($i=0;$i<sizeof($SQL);$i++) {
	db::query($SQL[$i]);
}


$db_config = '<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

if(!defined("PREFIX_DB")) {
	define("PREFIX_DB", "'.$db_prefix.'");
}

$config = array_merge($config, array(
	"db" => array(
		"host" => "'.$db_host.'",
		"port" => "'.$db_port.'",
		"user" => "'.$db_user.'",
		"pass" => "'.$db_pass.'",
		"db" => "'.$db_db.'",
		"driver" => "'.$db_driver.'",
		"charset" => "utf8",
	),
));

?>';
if(file_exists(PATH_MEDIA."db.".ROOT_EX)) {
	unlink(PATH_MEDIA."db.".ROOT_EX);
}
file_put_contents(PATH_MEDIA."db.".ROOT_EX, $db_config);

$path = str_replace("http://", "", Arr::get($_POST, 'PATH'));
if(substr($path, -1)=="/") {
	$host = nsubstr($path, 0, nstrlen($path)-1);
} else {
	$host = $path;
}
$path = str_replace("http://".$_SERVER['HTTP_HOST'], "", Arr::get($_POST, 'PATH'));
$config = '<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

if(!defined("COOK_USER")) {
	define("COOK_USER", "username_'.rand(0, 1000).'");
}
if(!defined("COOK_PASS")) {
	define("COOK_PASS", "password_'.rand(0, 1000).'");
}
if(!defined("COOK_ADMIN_USER")) {
	define("COOK_ADMIN_USER", "admin_username_'.rand(0, 1000).'");
}
if(!defined("COOK_ADMIN_PASS")) {
	define("COOK_ADMIN_PASS", "admin_password_'.rand(0, 1000).'");
}
if(!defined("START_VERSION")) {
	define("START_VERSION", "'.VERSION.'");
}

$config = array_merge($config, array(
	"api_key" => "'.rand(1000000000, 9999999999).'",
	"speed_update" => false,
	"logs" => '.Saves::SaveOld(Arr::get($_POST, 'error_type'), true).',
	"hosting" => true,
	"default_http_local" => "'.$path.'",
	"default_http_hostname" => "'.Saves::SaveOld(Arr::get($_POST, 'SERVER'), true).'",
	"default_http_host" => HTTP::$protocol."://'.Saves::SaveOld($host, true).'/",
	"lang" => "ru",
	"cache" => array(
		"type" => '.Saves::SaveOld(Arr::get($_POST, 'cache_type'), true).',
		"server" => "'.Saves::SaveOld(Arr::get($_POST, 'cache_host'), true).'",
		"port" => '.Saves::SaveOld(Arr::get($_POST, 'cache_port'), true).',
		"login" => "'.Saves::SaveOld(Arr::get($_POST, 'cache_user'), true).'",
		"pass" => "'.Saves::SaveOld(Arr::get($_POST, 'cache_pass'), true).'",
		"path" => "'.Saves::SaveOld(Arr::get($_POST, 'cache_path'), true).'",
	),
	"lang" => "ru",
	"charset" => "utf-8",
));

?>';
if(file_exists(PATH_MEDIA."config.install.".ROOT_EX)) {
	unlink(PATH_MEDIA."config.install.".ROOT_EX);
}
file_put_contents(PATH_MEDIA."config.install.".ROOT_EX, $config);

$lang = '<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

$lang = array_merge($lang, array(
	"sitename" => "'.Saves::SaveOld(Arr::get($_POST, 'sitename'), true).'",
	"s_description" => "'.Saves::SaveOld(Arr::get($_POST, 'description'), true).'",
));

?>';
$lang = charcode($lang);
if(file_exists(PATH_MEDIA."config.lang.".ROOT_EX)) {
	unlink(PATH_MEDIA."config.lang.".ROOT_EX);
}
file_put_contents(PATH_MEDIA."config.lang.".ROOT_EX, $lang);
if(file_exists(PATH_MEDIA."config.default.".ROOT_EX)) {
	rename(PATH_MEDIA."config.default.".ROOT_EX, PATH_MEDIA."config.".ROOT_EX);
}
header("Location: ../".Route::get("install_done")->uri(array()));
?>