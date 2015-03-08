<?php
/*
*
* Version Engine: 1.25.3
* Version File: 2
*
* 2.0
* fix function die for php 5.4
* add check exists data in cache, return false if data is not exists in cache
*
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

final class cache {

	private static $type = "file";
	private static $connect = false;
	private static $live_time = 2592000;

	public function cache() {
	global $config;
		if((class_exists("Memcached") && $config['cache']['type'] == 2) || (class_exists("Memcached") && $config['cache']['type'] == 1)) {
			self::$type = "memcached";
			self::$connect = new Memcached();
			self::$connect->addServer($config['cache']['server'], $config['cache']['port']) or die ("Could not connect");
		} elseif((class_exists('Memcache') && $config['cache']['type'] == 2) || (class_exists("Memcache") && $config['cache']['type'] == 2)) {
			self::$type = "memcache";
			self::$connect = new Memcache();
			self::$connect->addServer($config['cache']['server'], $config['cache']['port']) or die ("Could not connect");
		}
	}

	public static function Mtime($data) {
		if(self::Exists($data)) {
			if(self::$type !== "file") {
				$data = self::$connect->get($data);
				return $data['time'];
			} else {
				return filemtime(ROOT_PATH."core/cache/".$data.".txt");
			}
		} else {
			return 0;
		}
	}

	public static function Get($data) {
		if(self::Exists($data)) {
			if(self::$type !== "file") {
				$data = self::$connect->get($data);
				return $data['data'];
			} else {
				if(file_exists(ROOT_PATH."core/cache/".$data.".txt")) {
					return unserialize(file_get_contents(ROOT_PATH."core/cache/".$data.".txt"));
				} else {
					return false;
				}
			}
		} else {
			return false;
		}
	}

	public static function Get_timelive() {
		return self::$live_time;
	}

	public static function Exists($data) {
		if(self::$type !== "file") {
			if(@(self::$connect->get($data))) {
					return true;
			} else {
				return false;
			}
		} else {
			return file_exists(ROOT_PATH."core/cache/".$data.".txt");
		}
	}

	public static function Set($name, $val) {
		if(self::$type !== "file") {
			return self::$connect->set($name, array("time" => time(), "data" => $val), MEMCACHE_COMPRESSED, self::$live_time);
		} else {
			return file_put_contents(ROOT_PATH."core/cache/".$name.".txt", serialize($val));
		}
	}

	public static function Delete($name) {
		if(self::Exists($name)) {
			if(self::$type !== "file") {
				return self::$connect->delete($name);
			} else if(file_exists(ROOT_PATH."core/cache/".$name.".txt")) {
				return unlink(ROOT_PATH."core/cache/".$name.".txt");
			}
		} else {
			return false;
		}
	}

	public static function Clear_cache($cache_areas = false) {
		if(self::$type !== "file") {
			self::$connect->flush();
		}

		if($cache_areas) {
			if(!is_array($cache_areas)) {
				$cache_areas = array($cache_areas);
			}
		}

		$fdir = opendir(ROOT_PATH.'core/cache');
		while($file = readdir($fdir)) {
			if($file != '.' && $file != '..' && $file != '.htaccess' && $file != 'index.php') {
				if($cache_areas) {
					foreach($cache_areas as $cache_area)
						if(strpos($file, $cache_area) !== false)
							@unlink(ROOT_PATH.'core/cache/'.$file);
				} else {
					@unlink(ROOT_PATH.'core/cache/'.$file);
				}
			}
		}
	}

	public function __destruct() {
		if(self::$type === "memcached") {
			self::$connect->quit();
		} elseif(self::$type === "memcache") {
			self::$connect->close();
		}
	}

}

?>