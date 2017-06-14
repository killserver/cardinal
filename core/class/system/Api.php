<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

/**
 * Class Api
 */
class Api {

    /**
     * @var bool Activation API
     */
    private static $active = false;
    /**
     * @var array List parameters $_GET and $_POST
     */
    private static $params = array();
	
	private static $apiKey = "";

    /**
     * Initialize API
     * @return string Return result API
     */
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
			case "user":
				return self::user();
			break;
			default:
				return serialize(array("error" => "error api key 3", "params" => array("get" => self::$params['get'], "post" => self::$params['post'])));
			break;
		}
	}
	
	public static function setApiKey($api) {
		self::$apiKey = $api;
	}

    /**
     * Result get config element
     * @return string Get config element
     */
    public static function config() {
		$cfg = modules::get_config(self::$params['post']['query']);
		if(is_bool($cfg)) {
			return serialize(array("error" => "error api key 2", "params" => array("get" => self::$params['get'], "post" => self::$params['post'])));
		} else {
			return serialize($cfg);
		}
	}
	
	public static function user() {
		$user = unserialize(self::$params['post']['user']);
		if(!User::reg("", $user['username'], $user['pass'], $user['email'], LEVEL_USER, "yes", true)) {
			return serialize(array("error" => "error api reg user", "params" => array("get" => self::$params['get'], "post" => self::$params['post'])));
		} else {
			return serialize("done");
		}
	}

    /**
     * Result query
     * @return string Return result query
     */
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

    /**
     * Install modules
     * @return string Result load module
     */
    public static function Install() {
		if(!isset(self::$params['post']['is_install'])) {
			try {
				if(isset(self::$params['post']['is_xml'])) {
					$fp = fopen(PATH_MODULES.'xml'.DS.self::$params['post']['name'].'.xml', 'w+');
				} else {
					$fp = fopen(PATH_CACHE_SYSTEM.self::$params['post']['name'].'.tar', 'w+');
				}
				$ch = curl_init(str_replace(" ", "%20", self::$params['post']['url']));
				curl_setopt($ch, CURLOPT_TIMEOUT, 50);
				curl_setopt($ch, CURLOPT_FILE, $fp); 
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

    /**
     * Send query to another API
     * @param string $url Link another API
     * @param string $sql Query for execute API
     * @param bool $all Return all elements in another query API
     * @return mixed Return result query in another API
     */
    public static function post_db($url, $sql, $all = false) {
		$json = array("method" => "db", "query" => $sql, "db_all" => ($all? "true" : "false"));
		return self::post($url, $json);
	}

    /**
     * Element config in another API
     * @param string $url Link another API
     * @param string $cfg Get element config in another query API
     * @return mixed Return result get element config in another API
     */
    public static function post_config($url, $cfg) {
		$json = array("method" => "config", "query" => $cfg);
		return self::post($url, $json);
	}
	
    public static function post_user($url, $user) {
		$json = array("method" => "user", "user" => $user);
		return self::post($url, $json);
	}

    /**
     * Send post in another API
     * @param string $url Link to another API
     * @param array $jsons Data for send to another API
     * @return mixed Result another API
     */
    public static function post($url, $jsons) {
	global $config;
		$datas = "";
		$json = array("api_key" => (!empty(self::$apiKey) ? self::$apiKey : $config["api_key"]));
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