<?php

class Main_Cache extends Main {
	
	function formatSize($size, $only = false) {
		$filesizename = array(" B", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
		$i = 0;
		$sizes = $size ? round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) : 0;
		$ret = $sizes.$filesizename[$i];
		if($only=="text") {
			$ret = $filesizename[$i];
		} else if($only=="integer") {
			$ret = $sizes;
		}
		return $ret;
	}
	
	public function __construct() {
		if(isset($_GET['clear']) && isset($_GET['cache'])) {
			$path = PATH_CACHE;
			$files = read_dir($path);
			for($i=0;$i<sizeof($files);$i++) {
				if($files[$i] != "index.php" && $files[$i] != "index.html") {
					unlink($path.$files[$i]);
				}
			}
			$path = PATH_CACHE_SYSTEM;
			$files = read_dir($path);
			for($i=0;$i<sizeof($files);$i++) {
				if($files[$i] != "index.php" && $files[$i] != "index.html") {
					unlink($path.$files[$i]);
				}
			}
			cardinal::RegAction("Очистка кеша данных");
			Debug::activShow(false);
			templates::$gzip=false;
			echo "Done";
			die();
		}
		$size = 0;
		$path = PATH_CACHE;
		$files = read_dir($path);
		for($i=0;$i<sizeof($files);$i++) {
			if($files[$i] == "index.php" || $files[$i] == "index.html") {
				unset($files[$i]);
			} else {
				$size += filesize($path.$files[$i]);
			}
		}
		$path = PATH_CACHE_SYSTEM;
		$files = read_dir($path);
		for($i=0;$i<sizeof($files);$i++) {
			if($files[$i] == "index.php" || $files[$i] == "index.html") {
				unset($files[$i]);
			} else {
				$size += filesize($path.$files[$i]);
			}
		}
		templates::assign_var("Cache", $this->formatSize($size));
		templates::assign_var("CacheSizeS", $this->formatSize($size, "text"));
		templates::assign_var("CacheSize", $this->formatSize($size, "integer"));
	}
	
}

?>