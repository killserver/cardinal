<?php
class Recyclebinclear {
	
	function __construct() {
		if(file_exists(PATH_CACHE_USERDATA."trashBin.lock")) {
			db::doquery("DELETE FROM {{trashBin}} WHERE `tTime` < (UNIX_TIMESTAMP()-30*24*60*60)");
		}
	}
	
}
?>