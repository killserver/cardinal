<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
exit();
}

final class updater {
	
	public static function update($version, $db_version) {
		if($version==$db_version) {
			return true;
		}
		switch($version) {
			case "1.25.5a2":
				db::query("insert into `config` set `config_name` = \"db_version\", `config_value` = \"1.25.5a2\"");
			break;
			case "1.25.5a3":
				db::query("alter table `comments` add `guest` varchar(255) not null");
			break;
		}
		cache::Delete("config");
		return false;
	}
	
}

?>