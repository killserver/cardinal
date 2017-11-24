<?php

class LogInAdmin extends Core {
	
	function __construct() {
		cardinal::InitRegAction();
		$dir = PATH_CACHE_SYSTEM;
		$file = $dir."logInAdmin.txt";
		if(!defined("WITHOUT_DB") || db::connected() && (!file_exists($dir."logInAdmin.lock") && is_writable($dir)) || file_exists($dir."logInAdmin.lock")) {
			$log = "DB";
		} elseif(!(!defined("WITHOUT_DB") || db::connected()) && is_writable($dir)) {
			$log = "FILE";
		}
		if(empty($log)) {
			return false;
		}
		if($log==="DB") {
			db::doquery("SELECT * FROM {{logInAdmin}} ORDER BY `lId` DESC", true);
			while($row = db::fetch_assoc()) {
				templates::assign_vars($row, "logs", $row['lId']);
			}
		} elseif($log==="FILE") {
			if(file_exists($file)) {
				$file = file($file);
				$file = array_map("trim", $file);
				for($i=0;$i<sizeof($file);$i++) {
					$file[$i] = unserialize($file[$i]);
					$file[$i]['lAction'] = str_replace("\\\"", "\"", $file[$i]['lAction']);
					$file[$i]['lId'] = ($i+1);
					templates::assign_vars($file[$i], "logs", "logs".$i);
				}
			}
		}
		$this->Prints("LogInAdmin");
	}
	
}

?>