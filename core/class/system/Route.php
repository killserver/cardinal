<?php
/*
 *
 * @version 1.25.6-rc5
 * @copyright 2014-2015 KilleR for Cardinal Engine
 *
 * Version Engine: 1.25.6-rc5
 * Version File: 1
 *
 * 1.1
 * create routification for engine
 * 1.2
 * add error for routification
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

final class Route {
	
	public static function Get($return) {
		$uri = getenv("PATH_INFO");
		if(!empty($uri) && strlen($uri)>1) {
			$return = "error";
			$replace = array(
				"\(" => "(",
				"\)" => ")",
				"\[" => "[",
				"\]" => "]",
				"\-" => "-",
				"\+" => "+",
				"\|" => "|",
			);
			$uri_list = modules::manifest_get('route');
			$pattern = array_keys($uri_list);
			$page = array_values($uri_list);
			for($i=0;$i<sizeof($pattern);$i++) {
				try {
					$match = str_replace(array_keys($replace), array_values($replace), preg_quote($pattern[$i]));
					if(preg_match("#".$match."#is", $uri)) {
						$return = $page[$i];
					}
				} catch(Exception $ex) {}
			}
		}
		return $return;
	}
	
	public static function Set($pattern, $page) {
		return modules::manifest_set(array('route', $pattern), $page);
	}
	
}

?>