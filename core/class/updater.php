<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
exit();
}

class updater {
	
	final private static function UpVersion($version) {
		config::Update("db_version", $version);
		cache::Delete("config");
	}
	
	final public static function update($version, $db_version) {
		if(defined("WITHOUT_DB")) {
			return false;
		}
		if($version == $db_version) {
			return true;
		}
		$update = false;
		switch($version) {
			case "1.25.5a2":
				db::query("insert into `".PREFIX_DB."config` set `config_name` = \"db_version\", `config_value` = \"1.25.5a2\"");
				$update = true;
			break;
			case "1.25.5a3":
				db::query("alter table `".PREFIX_DB."comments` add `guest` varchar(255) not null");
				$update = true;
			break;
			case "1.25.7-a1":
				db::query("CREATE TABLE IF NOT EXISTS `".PREFIX_DB."posts` (`id` int(11) not null auto_increment, `title` varchar(255) not null, `alt_name` varchar(255) not null, `image` varchar(255) not null, `descr` longtext not null, `cat_id` varchar(255) not null, `time` int(11) not null, `added` varchar(255) not null, `active` enum('yes','no') not null default 'no', PRIMARY KEY `id`(`id`), FULLTEXT `title_name` (`title`, `alt_name`), FULLTEXT `category` (`cat_id`), FULLTEXT `idescr`(`image`, `descr`), FULLTEXT `added`(`added`), KEY `active_time`(`active`, `time`) ) ENGINE=MyISAM;");
				$update = true;
			break;
			case "1.25.7-a2":
				db::query("insert into `".PREFIX_DB."config` set `config_name` = \"cardinal_time\", `config_value` = \"0\"");
				$update = true;
			break;
			case "2.4":
				db::query("alter table `".PREFIX_DB."modules` add `file` varchar(255) NOT NULL, add FULLTEXT KEY `file` (`file`);");
				$update = true;
			break;
			case "3.4":
				db::query("alter table `".PREFIX_DB."modules` add `type` enum('admincp', 'site') NOT NULL DEFAULT 'site', add KEY `type` (`type`);");
				$update = true;
			break;
			case "5.6":
				db::query("alter table `".PREFIX_DB."posts` add `type` varchar(255) NOT NULL DEFAULT 'post', add KEY `type` (`type`);");
				$update = true;
			break;
			case "6.4":
				$tables = array(
					"category" => PREFIX_DB."category",
					"config" => PREFIX_DB."config",
					"comments" => PREFIX_DB."comments",
					"error_log" => PREFIX_DB."error_log",
					"hackers" => PREFIX_DB."hackers",
					"lang" => PREFIX_DB."lang",
					"menu" => PREFIX_DB."menu",
					"modules" => PREFIX_DB."modules",
					"posts" => PREFIX_DB."posts",
					"users" => PREFIX_DB."users",
					"userlevels" => PREFIX_DB."userlevels",
					"category" => PREFIX_DB."category",
					"category" => PREFIX_DB."category",
				);
				foreach($tables as $k => $v) {
					$isTableExist = db::query("SELECT count(*) FROM information_schema.tables WHERE table_schema = '".config::Select("db", "db")."' AND table_name = '".$k."'");
					if($isTableExist) {
						db::query("RENAME TABLE `".$k."` TO `".$v."`");
					}
				}
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