<?php
if(!defined("IS_CORE")) {
	die();
}

class changelog {
	
	function __construct() {
		modules::manifest_log('load_modules', array('changelog', __FILE__));
		Route::Set('changelog', "changelog.php")->defaults(array(
			'page' => 'changelog',
			'method'     => 'change',
		));
		modules::manifest_set(array('class_pages', 'changelog'), array("object" => &$this, "func" => "change"));
	}
	
	function change() {
		header("Content-Type: text/plain; charset=utf-8");
		$dir = ROOT_PATH."/changelog/";
		$files = array();
		if(is_dir($dir)) {
			if($dh = dir($dir)) {
				while(($file = $dh->read()) !== false) {
					if(is_file($dir.$file) && (strpos($file, ".txt")!==false) && (strpos($file, "changelog.txt")===false) && (strpos($file, "list.txt")===false)) {
						$files[] = $file;
					}
				}
			$dh->close();
			}
		}
		sort($files);
		$echo = file_get_contents($dir."changelog.txt")."\n\n\n\n";
		for($i=0;$i<sizeof($files);$i++) {
			$echo .= file_get_contents($dir.$files[$i])."\n\n\n\n";
		}
		echo trim($echo);
		die();
	}
	
}

?>