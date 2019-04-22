<?php

class LogInAdmin extends Core {
	
	function __construct() {
		cardinal::InitRegAction();
		$dir = PATH_CACHE_USERDATA;
		$file = $dir."logInAdmin.txt";
		if(is_writable($dir)) {
			$log = "FILE";
		}
		if(empty($log)) {
			return false;
		}
		if(file_exists($file)) {
			$file = file($file);
			$file = array_filter($file);
			$file = array_map("trim", $file);
			for($i=0;$i<sizeof($file);$i++) {
				$file[$i] = unserialize($file[$i]);
				$file[$i]['lAction'] = str_replace("\\\"", "\"", $file[$i]['lAction']);
				$file[$i]['lId'] = ($i+1);
				templates::assign_vars($file[$i], "logs", "logs".$i);
			}
		}
		$this->Prints("LogInAdmin");
	}
	
}

?>