<?php
/*
 *
 * @version 1.25.6-rc5
 * @copyright 2014-2015 KilleR for Cardinal Engine
 *
 * Version Engine: 1.25.6-rc5
 * Version File: 1
 *
 * 1.1
 * add changelog for files, start autoupdater and view changelog new version on admin-panel
 *
*/
header("Content-Type: text/plain");
$dir = dirname(__FILE__)."/changelog/";
$files = array();
if(is_dir($dir)) {
	if($dh = dir($dir)) {
		while(($file = $dh->read()) !== false) {
			if(is_file($dir.$file) && (strpos($file, ".txt")!==false)) {
				$files[] = $file;
			}
		}
	$dh->close();
	}
}
$echo = file_get_contents(dirname(__FILE__)."/changelog.txt")."\n\n\n\n";
for($i=0;$i<sizeof($files);$i++) {
	$echo .= file_get_contents($dir.$files[$i])."\n\n\n\n";
}
echo trim($echo);

?>