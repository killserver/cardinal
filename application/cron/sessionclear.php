<?php
class Sessionclear {
	
	function __construct() {
		$file = PATH_CACHE_SESSION;
		$list = read_dir($file);
		for($i=0;$i<sizeof($list);$i++) {
			if($list[$i]!=".htaccess" && $list[$i]!="index.html" && $list[$i]!="index.php") {
				@unlink($file.$list[$i]);
			}
		}
	}
	
}
?>