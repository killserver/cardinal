<?php
/*
 *
 * @version 4.2
 * @copyright 2014-2016 KilleR for Cardinal Engine
 *
 * Version Engine: 4.2
 * Version File: 2
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
exit();
}

class mobile extends modules {
	
	function location($link, $time=0, $exit=true, $code = "") {
		if($time == 0) {
			header("Location: ".($link), true, $code);
		} else {
			header("Refresh: ".$time."; url=".($link), true, $code);
		}
		if($exit) {
			exit();
		}
	}
	
	function is_ssl() {
		return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']!='off') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO']=='https') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
	}
	
	function protocol() {
		return ($this->is_ssl() ? "https" : "http");
	}

	function __construct() {
	global $mobileDetect;
		if(!is_object($mobileDetect)) {
			$mobileDetect = new Mobile_Detect();
		}
		$moby = ($mobileDetect->isMobile() || $mobileDetect->isTablet());
		$mobyActive = $this->get_config("mobyActive");
		$host = $this->get_config("default_http_hostname");
		$mobyHost = $this->get_config("default_http_mobyhost");
		$mobyLink = $this->protocol()."://".$mobyHost;
		$siteLink = $this->protocol()."://".$host;
		if($mobyActive && !empty($mobyHost)) {
			if(getenv('SERVER_NAME')==$mobyHost) {
				if(!$moby) {
					$this->location($siteLink, 0, true, 301);
				}
				define("MOBILE", true);
			} elseif($moby && getenv('SERVER_NAME')!=$mobyHost) {
				$this->location($mobyLink, 0, true, 301);
			}
		} else {
			if(!isset($_COOKIE['moby']) && $moby) {
				HTTP::set_cookie("moby", "true");
				define("MOBILE", true);
			} else {
				HTTP::set_cookie("moby", "", true);
			}
		}
		if((isset($_COOKIE['moby']) || isset($_GET['mob'])) && !defined("MOBILE")) {
			define("MOBILE", true);
		}
	}

}

?>