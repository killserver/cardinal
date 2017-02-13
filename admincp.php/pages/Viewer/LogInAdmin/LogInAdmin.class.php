<?php

class LogInAdmin extends Core {
	
	function __construct() {
		cardinal::InitRegAction();
		$dir = ROOT_PATH."core".DS."cache".DS."system".DS;
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
			db::doquery("SELECT * FROM `logInAdmin` ORDER BY `lId` DESC", true);
			while($row = db::fetch_assoc()) {
				templates::assign_vars($row, "logs", $row['lId']);
			}
		} elseif($log==="FILE") {
			$file = file($file);
			$file = array_map("trim", $file);
			for($i=0;$i<sizeof($file);$i++) {
				templates::assign_vars(unserialize($file[$i]), "logs", "logs".$i);
			}
		}
		$this->Prints("LogInAdmin");
	}
	
}

?>