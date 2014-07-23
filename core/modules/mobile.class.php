<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
exit();
}

class mobile {

	function __construct() {
		if(!isset($_COOKIE['moby'])) {
			$detect = new Mobile_Detect();
			if($detect->isMobile() || $detect->isTablet()) {
				setcookie("moby", "true", time()+(120*24*60*60), "/", ".".$config['default_http_hostname'], false, true);
				define("MOBILE", true);
			}
		}
		if(isset($_COOKIE['moby']) || isset($_GET['mob'])) {
			define("MOBILE", true);
		}
	}

}

?>