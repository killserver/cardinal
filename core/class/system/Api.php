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
				return serialize(array("error" => "error api key 1", "params" => array("get" => self::$params['get'], "post" => self::$params['post'])));
			}
			self::$active = true;
		} else {
			return serialize(array("error" => "error api key 2", "params" => array("get" => self::$params['get'], "post" => self::$params['post'])));
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
			case "install":
				return self::Install();
			break;
			default:
				return serialize(array("error" => "error api key 3", "params" => array("get" => self::$params['get'], "post" => self::$params['post'])));
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
	
	public static function Install() {
		if(!isset(self::$params['post']['is_install'])) {
			try {
				if(isset(self::$params['post']['is_xml'])) {
					$fp = fopen(ROOT_PATH.'core'.DS.'modules'.DS.'xml'.DS.self::$params['post']['name'].'.xml', 'w+');
				} else {
					$fp = fopen(ROOT_PATH.'core'.DS.'cache'.DS.'system'.DS.self::$params['post']['name'].'.tar', 'w+');
				}
				$ch = curl_init(str_replace(" ", "%20", self::$params['post']['url']));
				curl_setopt($ch, CURLOPT_TIMEOUT, 50);
				curl_setopt($ch, CURLOPT_FILE, $fp); 
				//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_exec($ch); 
				curl_close($ch);
				fclose($fp);
			} catch(Exception $ex) {
				return serialize($ex);
			}
			return serialize(true);
		} else {
			return serialize(modules::Install(self::$params['post']['name']));
		}
	}
	
	public static function post_db($url, $sql, $all = false) {
	global $config;
		$datas = "";
		$json = array("method" => "db", "query" => $sql, "db_all" => ($all? "true" : "false"));
		return self::post($url, $json);
	}
	
	public static function post_config($url, $cfg) {
	global $config;
		$json = array("method" => "config", "query" => $cfg);
		return self::post($url, $json);
	}
	
	public static function post($url, $jsons) {
	global $config;
		$datas = "";
		$json = array("api_key" => $config["api_key"]);
		$json = array_merge($json, $jsons);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_REFERER, $config["default_http_host"]);
		curl_setopt($curl, CURLOPT_USERAGENT, "OnlineBot - ".$config["default_http_host"]);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_TIMEOUT, 35);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
		if(($datas = curl_exec($curl)) === false) {
			return array("error" => 'error curl: ' . curl_error($curl));
		} else {
			$datas = str_replace("\xEF\xBB\xBF", "", $datas);
			$datas = unserialize($datas);
		}
		curl_close($curl);
		return $datas;
	}
	
}
?>