<?php
/*
 *
 * @version 2015-09-30 13:30:44 1.25.6-rc3
 * @copyright 2014-2015 KilleR for Cardinal Engine
 *
 * Version Engine: 1.25.6-rc3
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

	private static $type = CACHE_NONE;
	private static $connect = false;
	private static $live_time = 2592000;
	private static $conn_link = null;
	private static $conn_path = null;

	public function cache() {
	global $config;
		if(defined("INSTALLER")) {
			return;
		}
		self::$type = $config['cache']['type'];
		self::$conn_path = $config['cache']['path'];
		if(class_exists("Memcached") && $config['cache']['type'] == CACHE_MEMCACHED) {
			self::$connect = new Memcached();
			self::$connect->addServer($config['cache']['server'], $config['cache']['port']) or die ("Could not connect");
		} elseif(class_exists('Memcache') && $config['cache']['type'] == CACHE_MEMCACHE) {
			self::$connect = new Memcache();
			self::$connect->addServer($config['cache']['server'], $config['cache']['port']) or die ("Could not connect");
		} elseif(self::$type == CACHE_FTP && self::$connect !==false) {
			self::$connect = ftp_connect($config['cache']['server'], $config['cache']['port']);
			ftp_login(self::$connect, $config['cache']['login'], $config['cache']['pass']);
			self::$conn_link = "ftp://".$config['cache']['login'].":".$config['cache']['pass']."@".$config['cache']['server'].":".$config['cache']['port'].self::$conn_path;
		}
	}

	public static function Mtime($data) {
		if(self::Exists($data)) {
			if(self::$type == CACHE_MEMCACHE || self::$type == CACHE_MEMCACHED) {
				$data = self::$connect->get($data);
				return $data['time'];
			} elseif(self::$type == CACHE_FILE) {
				return filemtime(ROOT_PATH."core".DS."cache".DS.$data.".txt");
			} elseif(self::$type == CACHE_FTP && self::$connect !==false) {
				return ftp_mdtm(self::$connect, self::$conn_path.$data.".txt");
			} elseif(self::$type == CACHE_XCACHE) {
				$arr = xcache_get("cardinal_".$data);
				return $arr['mktime'];
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}

	public static function Get($data) {
		if(self::Exists($data)) {
			if(self::$type == CACHE_MEMCACHE || self::$type == CACHE_MEMCACHED) {
				$data = self::$connect->get($data);
				return $data['data'];
			} elseif(self::$type == CACHE_FILE) {
				if(file_exists(ROOT_PATH."core".DS."cache".DS.$data.".txt")) {
					return unserialize(file_get_contents(ROOT_PATH."core".DS."cache".DS.$data.".txt"));
				} else {
					return false;
				}
			} elseif(self::$type == CACHE_FTP && self::$connect !==false) {
				return unserialize(file_get_contents(self::$conn_link.$data.".txt"));
			} elseif(self::$type == CACHE_XCACHE) {
				$arr = xcache_get("cardinal_".$data);
				return $arr['data'];
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public static function Get_timelive() {
		return self::$live_time;
	}

	public static function Exists($data) {
		if(self::$type == CACHE_MEMCACHE || self::$type == CACHE_MEMCACHED) {
			if(@(self::$connect->get($data))) {
					return true;
			} else {
				return false;
			}
		} elseif(self::$type == CACHE_FILE) {
			return file_exists(ROOT_PATH."core".DS."cache".DS.$data.".txt");
		} elseif(self::$type == CACHE_FTP && self::$connect !==false) {
			return (ftp_size(self::$connect, self::$conn_path.$data.".txt")>0);
		} elseif(self::$type == CACHE_XCACHE) {
			return xcache_isset("cardinal_".$data);
		} else {
			return false;
		}
	}

	public static function Set($name, $val) {
		if(self::$type == CACHE_MEMCACHE || self::$type == CACHE_MEMCACHED) {
			return self::$connect->set($name, array("time" => time(), "data" => $val), MEMCACHE_COMPRESSED, self::$live_time);
		} elseif(self::$type == CACHE_FILE) {
			return file_put_contents(ROOT_PATH."core".DS."cache".DS.$name.".txt", serialize($val));
		} elseif(self::$type == CACHE_FTP && self::$connect !==false) {
			return file_put_contents(self::$conn_link.$name.".txt", serialize($val), 0, stream_context_create(array('ftp' => array('overwrite' => true))));
		} elseif(self::$type == CACHE_XCACHE) {
			return xcache_set("cardinal_".$name, array("mktime" => time(), "data" => $val));
		} else {
			return false;
		}
	}

	public static function Delete($name) {
		if(self::Exists($name)) {
			if(self::$type == CACHE_MEMCACHE || self::$type == CACHE_MEMCACHED) {
				return self::$connect->delete($name);
			} else if(self::$type == CACHE_FILE && file_exists(ROOT_PATH."core".DS."cache".DS.$name.".txt") && !is_dir(ROOT_PATH.'core'.DS.'cache'.DS.$name.".txt")) {
				return unlink(ROOT_PATH."core".DS."cache".DS.$name.".txt");
			} elseif(self::$type == CACHE_FTP && self::$connect !==false) {
				return ftp_delete(self::$connect, self::$conn_path.$name.".txt");
			} elseif(self::$type == CACHE_XCACHE) {
				return xcache_unset("cardinal_".$name);
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public static function Clear_cache($cache_areas = false) {
		if(self::$type == CACHE_MEMCACHE || self::$type == CACHE_MEMCACHED) {
			self::$connect->flush();
		} elseif(self::$type == CACHE_XCACHE) {
			xcache_unset_by_prefix("cardinal_");
		}
		if($cache_areas) {
			if(!is_array($cache_areas)) {
				$cache_areas = array($cache_areas);
			}
		}
		$fdir = opendir(ROOT_PATH.'core'.DS.'cache');
		while($file = readdir($fdir)) {
			if($file != '.' && $file != '..' && $file != '.htaccess' && $file != 'index.'.ROOT_EX && !is_dir(ROOT_PATH.'core'.DS.'cache'.DS.$file)) {
				if($cache_areas) {
					foreach($cache_areas as $cache_area) {
						if(strpos($file, $cache_area)!==false && file_exists(ROOT_PATH.'core'.DS.'cache'.DS.$file)) {
							unlink(ROOT_PATH.'core'.DS.'cache'.DS.$file);
						}
					}
				} else {
					if(file_exists(ROOT_PATH.'core'.DS.'cache'.DS.$file)) {
						unlink(ROOT_PATH.'core'.DS.'cache'.DS.$file);
					}
				}
			}
		}
	}

	public function __destruct() {
		if(self::$type == CACHE_MEMCACHED) {
			self::$connect->quit();
		} elseif(self::$type == CACHE_MEMCACHE) {
			self::$connect->close();
		} elseif(self::$type == CACHE_FTP && self::$connect !==false) {
			ftp_close(self::$connect);
		}
	}

}

?>