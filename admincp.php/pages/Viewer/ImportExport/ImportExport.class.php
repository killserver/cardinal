<?php

class ImportExport extends Core {
	
	private $tree = array();
	private $all = false;

	function zip_flatten($zipfile, $dest = '.', $tmp = false) {
		if($tmp==false) {
			$tmp = dirname(__FILE__).DS."tmp".DS;
			if(!file_exists($tmp) || !is_dir($tmp)) {
				mkdir($tmp, 0777);
			}
			if(!is_writable($tmp)) {
				chmod($tmp, 0777);
			}
		}
		$zip = new ZipArchive();
		$files = $fileinfo = array();
		if($zip->open($zipfile)) {
			for($i=0;$i<$zip->numFiles;$i++) {
				$entry = $zip->getNameIndex($i);
				if(nsubstr($entry, -1) == '/') {
					continue; // skip directories
				}
				$file = str_replace(array("\\", "/"), DS, $dest.$entry);
				$file = iconv("cp866", "windows-1251//IGNORE", $file);
				$files[] = array("name" => $file, "id" => $i);
				$fp = $zip->getStream($entry);
				if(!$fp) {
					throw new Exception('Unable to extract the file.');
				}
				$ofp = fopen($tmp."un_tmp".$i, 'w+');
				if(!$ofp) {
					throw new Exception('Unable to extract the file.');
				}
				while(!feof($fp)) {
					fwrite($ofp, fread($fp, 8192));
				}
				fclose($fp);
				fclose($ofp);
			}
			$zip->close();
			for($i=0;$i<sizeof($files);$i++) {
				$id = $files[$i]['id'];
				$name = $files[$i]['name'];
				$file = file_get_contents($tmp."un_tmp".$i);
				$file = gzinflate($file);
				$file = gzinflate($file);
				$file = gzinflate($file);
				$file = gzinflate($file);
				file_put_contents($name, $file);
				unlink($tmp."un_tmp".$i);
			}
			return true;
		} else {
			return false;
		}
	}

	function getAll() {
		if(file_exists(dirname(__FILE__).DS."cache.txt")) { $this->all = json_decode(file_get_contents(dirname(__FILE__).DS."cache.txt"), true); return $this->all; }
		if(is_bool($this->all)) {
			$f = new Parser("https://api.github.com/repos/killserver/cardinal/git/trees/trunk?recursive=1");
			$f = $f->get();
			$f = json_decode($f, true);
			$this->all = $f;
		}
		file_put_contents(dirname(__FILE__).DS."cache.txt", json_encode($this->all));
		return $this->all;
	}

	function filter($arr) {
		return str_Replace(array(ROOT_PATH, DS), array("", "/"), $arr);
	}

	function createDir($file) {
		if(!file_exists($file) || !is_dir($file)) {
			mkdir($file, 0777);
		}
		if(!is_writeable($file)) {
			chmod($file, 0777);
		}
		if(!file_exists($file."index.html") || !is_file($file."index.html")) {
			copy(dirname(__FILE__).DS."index.html", $file."index.html");
		}
		if(!file_exists($file."index.php") || !is_file($file."index.php")) {
			copy(dirname(__FILE__).DS."index.php", $file."index.php");
		}
	}

	function __construct() {
		$relpathNow = dirname(__FILE__).DS;
		$this->createDir($relpathNow."tmp".DS);
		$this->createDir($relpathNow."uploads".DS);
		$this->createDir($relpathNow."zip".DS);
		if(Arr::get($_GET, "getDiff", false)!==false) {
			$exc = array();
			$exc[] = PATH_CACHE_TEMP;
			$exc[] = PATH_CACHE_SESSION;
			$exc[] = dirname(__FILE__).DS;

			$get = Arr::get($_POST, "text", "");
			if($get!=="") {
				$get = explode("\n", $get);
				$get = array_filter($get);
				if(sizeof($get)>0) {
					$exc = array_merge($exc, $get);
				}
			}

			$d = read_dir(ROOT_PATH, "all", true, true, $exc);
			$d = array_map(array($this, "filter"), $d);
			$this->getAll();
			$toZip = array();
			for($i=0;$i<sizeof($this->all['tree']);$i++) {
				for($z=0;$z<sizeof($d);$z++) {
					if(strcasecmp($this->all['tree'][$i]['path'], $d[$z])==0) {
						unset($d[$z]);
					}
				}
				$d = array_values($d);
			}
			ajax(array("listFile" => $d));
		}
		if(Arr::get($_GET, "archive", false)!==false) {
			$d = Arr::get($_POST, "files", false);
			$d = array_filter($d);
			if($d===false) {
				ajax(404);
			}
			if(!is_writable($relpathNow)) {
				@chmod($relpathNow, 0777);
			}
			if(!file_exists($relpathNow."tmp".DS) || !is_dir($relpathNow."tmp".DS)) {
				@mkdir($relpathNow."tmp".DS, 0777);
			}
			if(!is_writable($relpathNow."tmp".DS)) {
				@chmod($relpathNow."tmp".DS, 0777);
			}
			if(!file_exists($relpathNow."zip".DS) || !is_dir($relpathNow."zip".DS)) {
				@mkdir($relpathNow."zip".DS, 0777);
			}
			if(!is_writable($relpathNow."zip".DS)) {
				@chmod($relpathNow."zip".DS, 0777);
			}
			for($i=0;$i<sizeof($d);$i++) {
				if(empty($d[$i]) || $d[$i]=="null" || $d[$i]==null) {
					continue;
				}
				$get = file_get_contents(ROOT_PATH.$d[$i]);
				$get = gzdeflate($get, 9);
				$get = gzdeflate($get, 9);
				$get = gzdeflate($get, 9);
				$get = gzdeflate($get, 9);
				file_put_contents($relpathNow."tmp".DS."tmp".$i,$get);
				$d[$i] = iconv("windows-1251", "cp866", $d[$i]);
			}
			$name = generate_uuid4();
			$zip = new ZipArchive();
			$zip->open($relpathNow."zip".DS.$name.'.zip', ZIPARCHIVE::CREATE);
			for($i=0;$i<sizeof($d);$i++) {
				$zip->addFile($relpathNow."tmp".DS."tmp".$i, $d[$i]);
			}
			$zip->close();
			for($i=0;$i<sizeof($d);$i++) {
				if(file_exists($relpathNow."tmp".DS."tmp".$i)) {
					unlink($relpathNow."tmp".DS."tmp".$i);
				}
			}
			ajax(array("link" => get_site_path($relpathNow."zip".DS.$name.'.zip')));
		}
		if(Arr::get($_GET, "single", false)!==false) {
			$exc = array();
			$exc[] = PATH_CACHE_TEMP;
			$exc[] = PATH_CACHE_SESSION;
			$exc[] = dirname(__FILE__).DS;

			$get = Arr::get($_POST, "text", "");
			if($get!=="") {
				$get = explode("\n", $get);
				$get = array_filter($get);
				if(sizeof($get)>0) {
					$exc = array_merge($exc, $get);
				}
			}

			$d = read_dir(ROOT_PATH, "all", true, true, $exc);
			$d = array_map(array($this, "filter"), $d);
			$this->getAll();
			$toZip = array();
			for($i=0;$i<sizeof($this->all['tree']);$i++) {
				for($z=0;$z<sizeof($d);$z++) {
					if(strcasecmp($this->all['tree'][$i]['path'], $d[$z])==0) {
						unset($d[$z]);
					}
				}
				$d = array_values($d);
			}
			if(!is_writable($relpathNow)) {
				@chmod($relpathNow, 0777);
			}
			if(!file_exists($relpathNow."tmp".DS) || !is_dir($relpathNow."tmp".DS)) {
				@mkdir($relpathNow."tmp".DS, 0777);
			}
			if(!is_writable($relpathNow."tmp".DS)) {
				@chmod($relpathNow."tmp".DS, 0777);
			}
			if(!file_exists($relpathNow."zip".DS) || !is_dir($relpathNow."zip".DS)) {
				@mkdir($relpathNow."zip".DS, 0777);
			}
			if(!is_writable($relpathNow."zip".DS)) {
				@chmod($relpathNow."zip".DS, 0777);
			}
			for($i=0;$i<sizeof($d);$i++) {
				if(empty($d[$i]) || $d[$i]=="null" || $d[$i]==null) {
					continue;
				}
				$get = file_get_contents(ROOT_PATH.$d[$i]);
				$get = gzdeflate($get, 9);
				$get = gzdeflate($get, 9);
				$get = gzdeflate($get, 9);
				$get = gzdeflate($get, 9);
				file_put_contents($relpathNow."tmp".DS."tmp".$i,$get);
				$d[$i] = iconv("windows-1251", "cp866", $d[$i]);
			}
			$name = generate_uuid4();
			$zip = new ZipArchive();
			$zip->open($relpathNow."zip".DS.$name.'.zip', ZIPARCHIVE::CREATE);
			for($i=0;$i<sizeof($d);$i++) {
				$zip->addFile($relpathNow."tmp".DS."tmp".$i, $d[$i]);
			}
			$zip->close();
			for($i=0;$i<sizeof($d);$i++) {
				if(file_exists($relpathNow."tmp".DS."tmp".$i)) {
					unlink($relpathNow."tmp".DS."tmp".$i);
				}
			}
			vdump($zip);
			return false;
		}
		if(Arr::get($_GET, "upload", false)!==false) {
			include_once($relpathNow."UploadHandler.".ROOT_EX);
			callAjax();
			new UploadHandler();
			return false;
		}
		if(($names = Arr::get($_GET, "unpack", false))!==false) {
			copy($relpathNow."uploads".DS.$names, $relpathNow."zip".DS.$names);
			unlink($relpathNow."uploads".DS.$names);
			try {
				$this->zip_flatten($relpathNow."zip".DS.$names, ROOT_PATH);
				ajax(array("done" => "1"));
			} catch(Exception $ex) {
				ajax(array("error" => $ex));
			}
			return false;
		}
		$d = read_dir($relpathNow."zip".DS, "all", true, false, array("index.html", "index.php"));
		$struct = array();
		for($i=0;$i<sizeof($d);$i++) {
			$time = filemtime($d[$i]);
			$struct[$time] = $d[$i];
		}
		krsort($struct);
		$d = $struct;
		$d = array_values($d);
		for($i=0;$i<sizeof($d);$i++) {
			$arr = array(
				"time" => date("d-m-Y H:i:s", filemtime($d[$i])),
				"size" => round(filesize($d[$i])/1024/1024, 2)."&nbsp;MB",
				"path" => get_site_path($d[$i]),
			);
			templates::assign_vars($arr, "zip");
		}
		$this->Prints("ImportExport");
	}

	function get($file) {
		if(isset($this->tree[$file])) {
			return $this->tree[$file];
		}
		$this->getAll();
		for($i=0;$i<sizeof($this->all['tree']);$i++) {
			if($this->all['tree'][$i]['path']!=$file) {
				continue;
			}
			$t = new Parser($this->all['tree'][$i]['url']);
			$t = $t->get();
			$t = json_decode($t, true);
			if(isset($t['content'])) {
				$this->tree[$this->all['tree'][$i]['path']] = sha1(base64_decode($t['content']));
			}
		}
		return $this->tree[$file];
	}

}