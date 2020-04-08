<?php
/*
 *
 * @version 1.25.7-a1
 * @copyright 2014-2015 KilleR for Cardinal Engine
 *
 * Version Engine: 1.25.7-a1
 * Version File: 1
 *
 * 1.1
 * add error for routification
 *
*/
class page {
	
	function __construct() {
		HTTP::sendError();
		if(templates::check_exists("404")) {
			$file = file_get_contents(PATH_TEMPLATE."404.".templates::changeTypeTpl());
			$file = str_replace("{THEME}", get_module_url("404/", PATH_SKINS."core".DS."404"), $file);
		} else {
			$file = file_get_contents(PATH_SKINS."core".DS."404".DS."index.html");
			$file = str_replace("{THEME}", get_module_url("404/", PATH_SKINS."core".DS."404"), $file);
		}
		HTTP::echos(templates::view($file));
	}
	
}

?>