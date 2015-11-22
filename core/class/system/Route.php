<?php
/*
 *
 * @version 1.25.7-a3
 * @copyright 2014-2015 KilleR for Cardinal Engine
 *
 * Version Engine: 1.25.7-a3
 * Version File: 1
 *
 * 1.1
 * create routification for engine
 * 1.2
 * add error for routification
 * 1.3
 * fix data on links
 * 1.4
 * add support return list parameters
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

final class Route {
	
	private static $uri = "";
	private static $pattern = "";
	private static $params = array();
	
	public static function GetParams(&$params = array()) {
		$paramz = array();
		preg_match("#".self::$pattern."#is", self::$uri, $param);
		$params['name_module'] = substr($param[0], 1, strpos(substr($param[0], 1), "/"));
		unset($param[0]);
		$paramz = array_values($param);
		$p_id = 0;
		for($i=0;$i<sizeof($paramz);$i++) {
			if(isset(self::$params[$i])) {
				if(self::$params[$i]=="name_module") {
					$p_id++;
					$params[self::$params[$i].($p_id>0 ? $p_id : "")] = $paramz[$i];
				} else {
					$params[self::$params[$i]] = $paramz[$i];
				}
			}
		}
	}
	
	public static function Get($return) {
		$uri = getenv("PATH_INFO");
		if(!empty($uri) && strlen($uri)>1) {
			self::$uri = $uri;
			$return = "error";
			$replace = array(
				"\(" => "(",
				"\)" => ")",
				"\[" => "[",
				"\]" => "]",
				"\-" => "-",
				"\+" => "+",
				"\|" => "|",
				"/\\?" => "/?",
			);
			$uri_list = modules::manifest_get('route');
			$pattern = array_keys($uri_list);
			$page = array_values($uri_list);
			for($i=0;$i<sizeof($pattern);$i++) {
				try {
					$match = str_replace(array_keys($replace), array_values($replace), preg_quote($pattern[$i]));
					if(preg_match("#".$match."#is", $uri)) {
						self::$pattern = $match;
						$return = $page[$i][0];
						self::$params = $page[$i][1];
					}
				} catch(Exception $ex) {}
			}
		}
		return $return;
	}
	
	public static function Set($pattern, $page, $params = array()) {
		if(!is_array($params)) {
			$params = array();
		}
		return modules::manifest_set(array('route', $pattern), array($page, $params));
	}
	
}

?>