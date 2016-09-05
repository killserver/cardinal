<?php
/*
 *
 * @version 4.0a
 * @copyright 2014-2016 KilleR for Cardinal Engine
 *
 * Version Engine: 4.0a
 * Version File: 1
 *
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
exit();
}

class mobile extends modules {

	function __construct() {
	global $mobileDetect;
		if(!isset($_COOKIE['moby'])) {
			if(!is_object($mobileDetect)) {
				$mobileDetect = new Mobile_Detect();
			}
			if($mobileDetect->isMobile() || $mobileDetect->isTablet()) {
				setcookie("moby", "true", time()+(120*24*60*60), "/", ".".$this->get_config('default_http_hostname'), false, true);
				define("MOBILE", true);
			}
		}
		if(isset($_COOKIE['moby']) || isset($_GET['mob'])) {
			define("MOBILE", true);
		}
	}

}

?>