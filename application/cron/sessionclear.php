<?php
class Sessionclear {
	
	function __construct() {
		$file = PATH_CACHE_SESSION;
		$list = read_dir($file);
		for($i=0;$i<sizeof($list);$i++) {
			if($list[$i]!=".htaccess" && $list[$i]!="index.html" && $list[$i]!="index.php") {
				if(filemtime($file.$list[$i])<(time()-(12*60*60))) {
					@unlink($file.$list[$i]);
				}
			}
		}
	}
	
}
?>