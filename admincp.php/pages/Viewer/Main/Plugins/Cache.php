<?php

class Main_Cache extends Main {
	
	function formatSize($size) {
		$filesizename = array(" B", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
		return $size ? round($size/pow(1024, ($i = floor(log($size, 1024)))), 2).$filesizename[$i] : '0 '.$filesizename[0];
	}
	
	public function Main_Cache() {
		if(isset($_GET['clear']) && isset($_GET['tmp'])) {
			$path = ROOT_PATH."core".DS."cache".DS."tmp".DS;
			$files = read_dir($path);
			for($i=0;$i<sizeof($files);$i++) {
				if($files[$i] != "index.php" && $files[$i] != "index.html") {
					unlink($path.$files[$i]);
				}
			}
			$path = ROOT_PATH."core".DS."cache".DS."page".DS;
			$files = read_dir($path);
			for($i=0;$i<sizeof($files);$i++) {
				if($files[$i] != "index.php" && $files[$i] != "index.html") {
					unlink($path.$files[$i]);
				}
			}
			echo "Done";
			die();
		}
		if(isset($_GET['clear']) && isset($_GET['cache'])) {
			$path = ROOT_PATH."core".DS."cache".DS;
			$files = read_dir($path);
			for($i=0;$i<sizeof($files);$i++) {
				if($files[$i] != "index.php" && $files[$i] != "index.html") {
					unlink($path.$files[$i]);
				}
			}
			echo "Done";
			die();
		}
		$size = 0;
		$path = ROOT_PATH."core".DS."cache".DS;
		$files = read_dir($path);
		for($i=0;$i<sizeof($files);$i++) {
			if($files[$i]=="index.php" || $files[$i] == "index.html") {
				unset($files[$i]);
			} else {
				$size += filesize($path.$files[$i]);
			}
		}
		templates::assign_var("Cache", $this->formatSize($size));
		$size = 0;
		$path = ROOT_PATH."core".DS."cache".DS."tmp".DS;
		$files = read_dir($path);
		for($i=0;$i<sizeof($files);$i++) {
			if($files[$i] == "index.php" || $files[$i] == "index.html") {
				unset($files[$i]);
			} else {
				$size += filesize($path.$files[$i]);
			}
		}
		$path = ROOT_PATH."core".DS."cache".DS."page".DS;
		$files = read_dir($path);
		for($i=0;$i<sizeof($files);$i++) {
			if($files[$i]=="index.php" || $files[$i] == "index.html") {
				unset($files[$i]);
			} else {
				$size += filesize($path.$files[$i]);
			}
		}
		templates::assign_var("CachePHP", $this->formatSize($size));
	}
	
}

?>