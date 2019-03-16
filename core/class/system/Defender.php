<?php

class Defender {

	private static $tryRead = 0;
	private static $tryWrite = 0;

	final public static function readFile($files) {
		chmod($files, 0400);
		if(($fp1 = fopen($files, "r"))!==false) {
			$file = fread($fp1, filesize($files));
			fclose($fp1);
		} else if(self::$tryRead>5) {
			throw new Exception("Error reading user list", 1);
			die();
		} else {
			usleep(300);
			self::$tryRead++;
			return self::readFile($files);
		}
		self::$tryRead = 0;
		chmod($files, 0644);
		return $file;
	}

	final public static function safeSave($file, $d) {
		if(!file_exists($file)) {
			file_put_contents($file, "");
		}
		chmod($file, 0200);
		$ret = false;
		if(($fp = fopen($file, "w"))!==false) {
			fwrite($fp, $d);
			fclose($fp);
			$ret = true;
		} else if(self::$tryWrite>5) {
			throw new Exception("Error writing user list", 1);
			die();
		} else {
			usleep(300);
			self::$tryWrite++;
			return self::safeSave($file, $d);
		}
		self::$tryWrite = 0;
		chmod($file, 0644);
		return $ret;
	}

	public static function read($file, $default = false) {
		$def = $default;
		if(file_exists($file) && is_readable($file)) {
			$file = file_get_contents($file);
			$file = preg_replace("#\<\?(.+?)\?\>#is", "", $file);
			$def = trim($file);
		}
		return $def;
	}

	public static function save($file, $data = "", $params = false) {
		$dir = dirname($file);
		if(!is_writeable($dir)) {
			@chmod($dir, 0777);
		}
		if(!is_writeable($dir)) {
			return false;
		}
		if(file_exists($file)) {
			unlink($file);
		}
		if($params===false) {
			@file_put_contents($file, '<?php die(); ?>'.$data);
		} else {
			@file_put_contents($file, '<?php die(); ?>'.$data, $params);
		}
		return true;
	}

	public static function append($file, $data = "") {
		return self::save($file, $data, FILE_APPEND);
	}

}