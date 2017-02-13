<?php

class page {
	
	function __construct() {
		header("Content-Type: text/cache-manifest");
		$files = Arr::get($_GET, "f", false);
		if(!$files && !file_exists(ROOT_PATH."uploads".DS."manifest".DS.$files.".txt")) {
			die();
		}
		echo "CACHE MANIFEST\n\nCACHE:\n";
		$files = file_get_contents(ROOT_PATH."uploads".DS."manifest".DS.$files.".txt");
		$files = unserialize($files);
		for($i=0;$i<sizeof($files);$i++) {
			echo ($files[$i])."\n";
		}
		echo "\nNETWORK:\n*";
		die();
	}
	
}

?>