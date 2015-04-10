<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

class Api {
	
	private static $active = false;
	private static $params = array();
	
	public static function init() {
	global $config;
		self::$params['post'] = $_POST;
		self::$params['get'] = $_GET;
		if(isset(self::$params['get']['api']) && isset(self::$params['post']['api_key'])) {
			if($config['api_key']!=self::$params['post']['api_key']) {
				echo serialize(array("error" => "error api key 1", "params" => array("get" => self::$params['get'], "post" => self::$params['post'])));
				die();
			}
			self::$active = true;
		} else {
			echo serialize(array("error" => "error api key 2", "params" => array("get" => self::$params['get'], "post" => self::$params['post'])));
			die();
		}
		if(isset(self::$params['post']['method'])) {
			$method = self::$params['post']['method'];
		} else {
			$method = "";
		}
		switch($method) {
			case "db":
				return self::db();
			break;
			case "config":
				return self::config();
			break;
		}
	}
	
	public static function config() {
		$cfg = modules::get_config(self::$params['post']['query']);
		if(is_bool($cfg)) {
			return serialize(array("error" => "error api key 2", "params" => array("get" => self::$params['get'], "post" => self::$params['post'])));
		} else {
			return serialize($cfg);
		}
	}
	
	public static function db() {
		if(isset(self::$params['post']['db_all']) && self::$params['post']['db_all']=="true") {
			db::doquery(self::$params['post']['query'], true);
			$arr = array();
			while($row = db::fetch_assoc()) {
				$arr[] = $row;
			}
			return serialize($arr);
		} else {
			return serialize(db::doquery(self::$params['post']['query']));
		}
	}
	
	public static function post_db($url, $sql, $all = false) {
	global $config;
		$json = array("api_key" => $config["api_key"], "method" => "db", "query" => $sql, "db_all" => ($all? "true" : "false"));
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_REFERER, $config["default_http_host"]);
		curl_setopt($curl, CURLOPT_USERAGENT, "OnlineBot - ".$config["default_http_host"]);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_TIMEOUT, 35);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
		//curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		if(($datas = curl_exec($curl)) === false) {
			return array("error" => 'error curl: ' . curl_error($curl));
		} else {
			return unserialize($datas);
		}
		curl_close($curl);
	}
	
	public static function post_config($url, $cfg) {
	global $config;
		$json = array("api_key" => $config["api_key"], "method" => "config", "query" => $cfg);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_REFERER, $config["default_http_host"]);
		curl_setopt($curl, CURLOPT_USERAGENT, "OnlineBot - ".$config["default_http_host"]);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_TIMEOUT, 35);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
		//curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		if(($datas = curl_exec($curl)) === false) {
			return array("error" => 'error curl: ' . curl_error($curl));
		} else {
			return unserialize($datas);
		}
		curl_close($curl);
	}
	
}
?>