<?php

class Main_UserOnline extends Main {

	public function __construct() {
		if(is_writeable(PATH_CACHE_SESSION)) {
			clearstatcache();
			$SessionDir = PATH_CACHE_SESSION;
			$Timeout = 3 * 60;
			$usersOnline = 0;
			$online = 0;
			if($Handler = @scandir($SessionDir)) {
				for($i=0;$i<sizeof($Handler);$i++) {
					if($Handler[$i]=="index.html"||$Handler[$i]=="index.".ROOT_EX||$Handler[$i]==".htaccess"||$Handler[$i]=="."||$Handler[$i]=="..") {
						continue;
					}
					if(time()-@filemtime($SessionDir.$Handler[$i])<$Timeout) {
						$usersOnline++;
					}
				}
			}
			templates::assign_var("IsUserOnline", "1");
			templates::assign_var("UserOnline", $usersOnline);
		} else {
			templates::assign_var("IsUserOnline", "0");
			templates::assign_var("UserOnline", "0");
		}
	}

}

?>