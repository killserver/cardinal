<?php

class Antivirus extends Core {
	
	function read($dir, &$files, &$dirs) {
		$read = read_dir($dir, "dir", true);
		if(in_array_strpos("excludeAntivirus.lock", $read, true)) {
			return false;
		}
		for($i=0;$i<sizeof($read);$i++) {
			if(is_string($read[$i]) && is_dir($read[$i])) {
				$dirs[] = array(
					"path" => $read[$i].DS,
					"chmod" => get_chmod($read[$i]),
				);
				$this->read($read[$i].DS, $files, $dirs);
			} else {
				$files[] = $read[$i];
			}
		}
		return true;
	}
	
	function rebuildTypes($arr, $search = false) {
		$arrRes = array();
		$arrK = array_keys($arr);
		$arrV = array_values($arr);
		for($i=0;$i<sizeof($arrK);$i++) {
			if(!$search || in_array_strpos($arrV[$i], $search)!==false) {
				$arrRes[$arrV[$i]] = $arrK[$i];
			}
		}
		return $arrRes;
	}
	
	function readFiles($link) {
		if(!file_exists($link)) {
			return false;
		}
		$r = fopen($link, "r");
		$read = fread($r, 100);
		fclose($r);
		if(stripos($read, '<?')!==false && stripos($read, '<?x')===false && stripos($read, '<?B')===false) {
			$ret = true;
		} else {
			$ret = false;
		}
		return $ret;
	}
	
	function checks($file, $types) {
		$ret = false;
		$files = pathinfo($file);
		for($i=0;$i<sizeof($types);$i++) {
			if(!isset($files['extension'])) {
				$ret = false;
				break;
			}
			if($types[$i]==$files['extension'] && ROOT_EX!=$files['extension']) {
				$ret = true;
				break;
			}
		}
		return $ret;
	}
	
	function initialize() {
		$fileList = array();
		$dirList = array();
		if(!file_exists(PATH_CACHE_SYSTEM."fileMask.sha1") || !file_exists(PATH_CACHE_SYSTEM."dirMask.sha1")) {
			$this->read(ROOT_PATH, $fileList, $dirList);
			if(!file_exists(PATH_CACHE_SYSTEM."fileMask.sha1")) {
				$md5 = array();
				for($i=0;$i<sizeof($fileList);$i++) {
					if(strpos($fileList[$i], PATH_CACHE_SYSTEM)!==false || strpos($fileList[$i], PATH_CACHE_PAGE)!==false) {
						continue;
					}
					$path = str_replace(ROOT_PATH, "", $fileList[$i]);
					$md5[$path] = sha1_file($fileList[$i]);
				}
				if(is_writable(PATH_CACHE_SYSTEM) && !file_exists(PATH_CACHE_SYSTEM."fileMask.sha1")) {
					file_put_contents(PATH_CACHE_SYSTEM."fileMask.sha1", serialize($md5));
				}
			}
			if(is_writable(PATH_CACHE_SYSTEM) && !file_exists(PATH_CACHE_SYSTEM."dirMask.sha1")) {
				file_put_contents(PATH_CACHE_SYSTEM."dirMask.sha1", serialize($dirList));
			}
		}
		callAjax();
		cardinal::RegAction("Инициализация антивируса");
		HTTP::echos("done");
		die();
	}
	
	function scan($mask = false) {
		if(file_exists(PATH_CACHE_SYSTEM."fileMask.sha1")) {
			$md5 = file_get_contents(PATH_CACHE_SYSTEM."fileMask.sha1");
			$md5 = unserialize($md5);
		}
		if(file_exists(PATH_CACHE_SYSTEM."dirMask.sha1")) {
			$dirList = file_get_contents(PATH_CACHE_SYSTEM."dirMask.sha1");
			$dirList = unserialize($dirList);
		}
		$types = HTTP::getContentTypes();
		$types = $this->rebuildTypes($types, array("image", "font", "text"));
		$types = array_values($types);
		$maskInit = false;
		$exclude = array();
		if($mask) {
			$maskInit = true;
		} else {
			if(file_exists(PATH_CACHE_SYSTEM."fileMask.db")) {
				$exclude = file_get_contents(PATH_CACHE_SYSTEM."fileMask.db");
				$exclude = unserialize($exclude);
			}
		}
		$warning = array();
		$count = 0;
		for($i=0;$i<sizeof($dirList);$i++) {
			if(in_array($dirList[$i]['path'], $exclude) || (strpos($dirList[$i]['path'], PATH_CACHE)!==false || strpos($dirList[$i]['path'], PATH_CACHE_SYSTEM)!==false || strpos($dirList[$i]['path'], PATH_CACHE_PAGE)!==false)) {
				// need  || strpos($dirList[$i]['path'], ROOT_PATH."uploads".DS)!==false) ??
				continue;
			}
			if($count>40) {
				break;
			}
			if(isset($dirList[$i]['chmod']) && stripos($dirList[$i]['chmod'], "0775")===false) {
				$warning[] = array("path" => str_replace(($mask ? ROOT_PATH : ""), "", $dirList[$i]['path']), "type" => "dir", "alert" => "warning");
				$count++;
			}
		}
		$count = 0;
		foreach($md5 as $file => $sha1) {
			if(in_array(ROOT_PATH.$file, $exclude)) {
				continue;
			}
			if($count>40) {
				break;
			}
			if($this->checks($file, $types) && $this->readFiles(ROOT_PATH.$file)) {
				$warning[] = array("path" => ($mask ? ROOT_PATH : "").$file, "type" => "file", "alert" => "alert");
				$count++;
			}
			if(file_exists(ROOT_PATH.$file) && sha1_file(ROOT_PATH.$file) != $sha1) {
				$warning[] = array("path" => ($mask ? ROOT_PATH : "").$file, "type" => "file", "alert" => "warning");
				$count++;
			}
		}
		callAjax();
		if($maskInit) {
			if(!is_writable(PATH_CACHE_SYSTEM)) {
				if(!isset($_SERVER['HTTP_CF_VISITOR'])) {
					header("HTTP/1.0 520 Unknown Error");
				} else {
					header("HTTP/1.0 404 Not found");
				}
				throw new Exception("Error write mask on server");
				die();
			}
			$mask = array();
			for($i=0;$i<sizeof($warning);$i++) {
				$mask[] = $warning[$i]['path'];
			}
			if(file_exists(PATH_CACHE_SYSTEM."fileMask.db")) {
				unlink(PATH_CACHE_SYSTEM."fileMask.db");
			}
			file_put_contents(PATH_CACHE_SYSTEM."fileMask.db", serialize($mask));
			cardinal::RegAction("Создание \"маски\" файлов в антивирусе");
			HTTP::echos("done");
			die();
		} else {
			HTTP::echos(json_encode($warning));
			die();
		}
	}
	
	function __construct() {
		if(Arr::get($_POST, "page", false) && Arr::get($_POST, "page")=="Init") {
			$this->initialize();
		}
		if(Arr::get($_POST, "page", false) && Arr::get($_POST, "page")=="Scan") {
			$this->scan();
		}
		if(Arr::get($_POST, "page", false) && Arr::get($_POST, "page")=="Mask") {
			$this->scan(true);
		}
		$this->Prints("Antivirus");
	}
	
}

?>