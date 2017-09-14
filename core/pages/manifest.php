<?php

class page {
	
	function __construct() {
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
		header('Cache-Control: no-store, no-cache, must-revalidate'); 
		header('Pragma: no-cache');
		header("Content-Type: text/cache-manifest");
		$filePath = Arr::get($_GET, "f", false);
		if(!$filePath || !file_exists(ROOT_PATH."uploads".DS."manifest".DS.$filePath.".txt")) {
			die();
		}
		echo "CACHE MANIFEST\n#v0.0.2\n\nCACHE:\n";
		$files = file_get_contents(ROOT_PATH."uploads".DS."manifest".DS.$filePath.".txt");
		$files = unserialize($files);
		for($i=0;$i<sizeof($files);$i++) {
			echo ($files[$i])."\n";
		}
		echo "\nNETWORK:\n*";
		unlink(ROOT_PATH."uploads".DS."manifest".DS.$filePath.".txt");
		die();
	}
	
}

?>