<?php

final class cache {

	private $type = "file";
	private $connect;
	private $live_time = 2592000;

	function __construct() {
	global $config;
		if(class_exists("Memcached") || (class_exists("Memcached") && $config['cache']['type'] == 1)) {
			$this->type = "memcached";
			$this->connect = new Memcached();
			$this->connect->addServer($config['cache']['server'], $config['cache']['port']) or die ("Could not connect");
		} elseif(class_exists('Memcache') || (class_exists("Memcache") && $config['cache']['type'] == 2)) {
			$this->type = "memcache";
			$this->connect = new Memcache();
			$this->connect->addServer($config['cache']['server'], $config['cache']['port']) or die ("Could not connect");
		}
	}

	function mtime($data) {
		if($this->type !== "file") {
			$data = $this->connect->get($data);
			return $data['time'];
		} else {
			return filemtime(ROOT_PATH."core/cache/".$data.".txt");
		}
	}

	function get($data) {
		if($this->type !== "file") {
			$data = $this->connect->get($data);
			return $data['data'];
		} else {
			if(file_exists(ROOT_PATH."core/cache/".$data.".txt")) {
				return unserialize(file_get_contents(ROOT_PATH."core/cache/".$data.".txt"));
			} else {
				return false;
			}
		}
	}

	function get_timelive() {
		return $this->live_time;
	}

	function exists($data) {
		if($this->type !== "file") {
			if(@($this->connect->get($data))) {
					return true;
			} else {
				return false;
			}
		} else {
			return file_exists(ROOT_PATH."core/cache/".$data.".txt");
		}
	}

	function set($name, $val) {
		if($this->type !== "file") {
			return $this->connect->set($name, array("time" => time(), "data" => $val), MEMCACHE_COMPRESSED, $this->live_time);
		} else {
			return file_put_contents(ROOT_PATH."core/cache/".$name.".txt", serialize($val));
		}
	}

	function delete($name) {
		if($this->type !== "file") {
			return $this->connect->delete($name);
		} else {
			return @unlink(ROOT_PATH."core/cache/".$name.".txt");
		}
	}

	function clear_cache($cache_areas = false) {
		if($this->type !== "file") {
			$this->connect->flush();
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

	function __destruct() {
		if($this->type === "memcached") {
			$this->connect->quit();
		} elseif($this->type === "memcache") {
			$this->connect->close();
		}
	}

}

?>