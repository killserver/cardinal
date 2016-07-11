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
		if(!isset($_COOKIE['moby'])) {
			$detect = new Mobile_Detect();
			if($detect->isMobile() || $detect->isTablet()) {
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