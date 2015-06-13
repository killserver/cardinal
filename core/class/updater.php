<?php
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
		}
		if($update) {
			self::UpVersion($version);
		}
		return false;
	}
	
}

?>