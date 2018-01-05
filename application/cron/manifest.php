<?php
class Manifest {
	
	function __construct() {
		$file = PATH_UPLOADS."manifest".DS;
		$list = read_dir($file);
		for($i=0;$i<sizeof($list);$i++) {
			if($list[$i]!=".htaccess" || $list[$i]!="index.html" || $list[$i]!="index.php") {
				unlink($file.$list[$i]);
			}
		}
	}
	
}
?>