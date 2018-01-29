<?php
if(!defined("IS_CORE")) {
	echo "403 ERROR";
	die();
}

class Pinger {

	private static $servers = array(
		"http://blogsearch.google.com/ping/RPC2" => "http://blogsearch.google.com/ping/RPC2",
		"http://rpc.pingomatic.com/" => "http://rpc.pingomatic.com/",
	);

	private static $fileList = "pingList.txt";
	private static $fileInfo = "pingInfo.txt";
	private static $force = false;

	final private static function readFromFile() {
		if(defined("PATH_SYSTEM") && file_exists(PATH_SYSTEM.self::$fileList)) {
			try {
				$file = file_get_contents(PATH_SYSTEM.self::$fileList);
				$file = json_decode($file, true);
				foreach($file as $v) {
					self::$servers[$v] = $v;
				}
			} catch(Exception $ex) {}
			return true;
		} else {
			return false;
		}
	}

	final public static function force($forced = true) {
		self::$force = $forced;
	}

	final public static function addServers($url) {
		self::$servers[$url] = $url;
		return true;
	}

	final public static function removeServers($url) {
		$count = 0;
		foreach(self::$servers as $k => $v) {
			if(strpos($k, $url)!==false) {
				$count++;
				unset(self::$servers[$k]);
			}
		}
		if($count>0) {
			return true;
		} else {
			return false;
		}
	}

	final public static function ping($title, $url, $rssLink = "") {
		if(class_exists("config") && method_exists("config", "Select")) {
			if(config::Select("pingList")) {
				self::$fileList = config::Select("pingList");
			}
			if(config::Select("pingInfo")) {
				self::$fileInfo = config::Select("pingInfo");
			}
		}
		self::readFromFile();
		$resp = array();
		$forced = false;
		if(!self::$force) {
			if(defined("PATH_SYSTEM") && file_exists(PATH_SYSTEM.self::$fileInfo) && (filemtime(PATH_SYSTEM.self::$fileInfo)-24*60*60)<=time()) {
				return $resp;
			}
		} else {
			$forced = true;
		}
		$link_rss = "";
		$rss = false;
		if(defined("ROOT_PATH") && $rssLink === "") {
			if(!file_exists(ROOT_PATH."rss.xml")) {
				$rss = Route::Name("rss");
				if($rss) {
					$link = Route::get("rss");
					$link_rss = $link->uri(array());
				}
			} else if(file_exists(ROOT_PATH."rss.xml")) {
				global $config;
				$link_rss = $config['default_http_host']."rss.xml";
				$rss = true;
			}
		} else {
			$rss = true;
			$link_rss = $rssLink;
		}
		$xml = '<?xml version="1.0" encoding="UTF-8"?>
		<methodCall>
		    <methodName>weblogUpdates.ping</methodName>
		    <params>
		        <param>
		            <value>'.$title.'</value>
		        </param>
		        <param>
		            <value>'.$url.'</value>
		        </param>';
				if($rss && !empty($link_rss)) {
					$xml .= '<param>
					            <value>'.$link_rss.'</value>
					        </param>';
				}
		$xml .= '</params>
		</methodCall>';
		foreach(self::$servers as $server) {
		    $target = parse_url($server);
		    $header = array();
			$header[] = "Host: ".$target["host"];
			$header[] = "Content-type: text/xml";
			$header[] = "Content-length: ".strlen($xml)."\r\n";
			$header[] = $xml;
			$prs = new Parser($server);
			$prs->headers($header);
			$prs->post(array(null => $xml));
			$prs->customRequest("POST");
			$result = $prs->get();
			$resp[$server] = (strpos($result, "flerror</name><value><boolean>0")!==false ? "1" : $result);
		}
		if(defined("PATH_SYSTEM")) {
			$arr = array();
			if(!$forced && file_exists(PATH_SYSTEM.self::$fileInfo)) {
				unlink(PATH_SYSTEM.self::$fileInfo);
			} else if($forced && file_exists(PATH_SYSTEM.self::$fileInfo)) {
				try {
					$file = file_get_contents(PATH_SYSTEM.self::$fileInfo);
					$file = json_decode($file, true);
					$arr = array_merge($arr, $file);
				} catch(Exception $ex) {}
				$resp = array_merge($resp, $arr);
			}
			@file_put_contents(PATH_SYSTEM.self::$fileInfo, json_encode($resp));
		}
		return $resp;
	}

}

?>