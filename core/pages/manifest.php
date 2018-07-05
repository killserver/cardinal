<?php

class page {
	
	function __construct() {
		callAjax();
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
		header('Cache-Control: no-store, no-cache, must-revalidate'); 
		header('Pragma: no-cache');
		header("Content-Type: text/cache-manifest");
		$filePath = Arr::get($_GET, "f", false);
		if(!$filePath || !file_exists(PATH_MANIFEST.$filePath.".txt")) {
			die();
		}
		$echo = "CACHE MANIFEST\n#v0.0.2\n\nCACHE:\n";
		$files = file_get_contents(PATH_MANIFEST.$filePath.".txt");
		$files = unserialize($files);
		for($i=0;$i<sizeof($files);$i++) {
			$echo .= ($files[$i])."\n";
		}
		$echo .= "\nNETWORK:\n*";
		HTTP::echos($echo);
		//unlink(PATH_MANIFEST.$filePath.".txt");
		die();
	}
	
}

?>