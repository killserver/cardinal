<?php
class Recyclebinclear {
	
	function __construct() {
		if(file_exists(PATH_CACHE_USERDATA."trashBin.lock")) {
			$days = 30;
			if(defined("EMPTY_TRASH_DAYS")) {
				if(is_numeric(EMPTY_TRASH_DAYS) && EMPTY_TRASH_DAYS>0) {
					$days = EMPTY_TRASH_DAYS;
				} else if(is_bool(EMPTY_TRASH_DAYS) && EMPTY_TRASH_DAYS===false) {
					$days = 0;
				}
			}
			db::doquery("DELETE FROM {{trashBin}} WHERE `tTime` < (UNIX_TIMESTAMP()-".$days."*24*60*60)");
		}
	}
	
}
?>