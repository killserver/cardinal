<?php
/*
 *
 * @version 1.25.7-a1
 * @copyright 2014-2015 KilleR for Cardinal Engine
 *
 * Version Engine: 1.25.7-a1
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
exit();
}

final class updater {
	
	private static function UpVersion($version) {
		db::query("update `config` set `config_value` = \"".$version."\" WHERE `config_name` = \"db_version\"");
		cache::Delete("config");
	}
	
	public static function update($version, $db_version) {
		if($version==$db_version) {
			return true;
		}
		$update = false;
		switch($version) {
			case "1.25.5a2":
				db::query("insert into `config` set `config_name` = \"db_version\", `config_value` = \"1.25.5a2\"");
				$update = true;
			break;
			case "1.25.5a3":
				db::query("alter table `comments` add `guest` varchar(255) not null");
				$update = true;
			break;
			case "1.25.7-a1":
				db::query("CREATE TABLE IF NOT EXISTS `posts` (`id` int(11) not null auto_increment, `title` varchar(255) not null, `alt_name` varchar(255) not null, `image` varchar(255) not null, `descr` longtext not null, `time` int(11) not null, `added` varchar(255) not null, `active` enum('yes','no') not null default 'no', PRIMARY KEY `id`(`id`), FULLTEXT `title_name` (`title`, `alt_name`), FULLTEXT `idescr`(`image`, `descr`), FULLTEXT `added`(`added`), KEY `active_time`(`active`, `time`) ) ENGINE=MyISAM;");
				$update = true;
			break;
		}
		if($update) {
			self::UpVersion($version);
		}
		return false;
	}
	
}

?>