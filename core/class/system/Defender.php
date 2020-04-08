<?php

class Defender {

	private static $tryRead = 0;
	private static $tryWrite = 0;

	final public static function readFile($files, $php = false) {
		if(!file_exists($files)) {
			file_put_contents($files, "");
			@chmod($files, 0777);
		}
		$chmod = substr(sprintf('%o', fileperms($files)), -4);
		if((strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' && ($fp1 = fopen($files, "r+"))!==false) || is_readable($files) && !($chmod=="0777" || $chmod=="0400" || $chmod=="0200") || (is_readable($files) && $chmod=="0777" && ($fp1 = fopen($files, "r+"))!==false)) {
			@chmod($files, 0400);
			$file = file_get_contents($files);
			@fclose($fp1);
			@chmod($files, 0777);
		} else if(self::$tryRead>999999) {
			throw new Exception("Error reading user list", 1);
			die();
		} else {
			usleep(800);
			self::$tryRead++;
			return self::readFile($files, $php);
		}
		self::$tryRead = 0;
		if($php) {
			$file = preg_replace("#\<\?(.+?)\?\>#is", "", $file);
		}
		$test = trim($file);
		if(Validate::json($test)) {
			$file = json_decode($file, true);
		}
		return $file;
	}

	final public static function safeSave($file, $d, $php = false) {
		if(!file_exists($file)) {
			file_put_contents($file, "");
			@chmod($file, 0777);
		}
		if(!is_string($d)) {
			$d = json_encode($d);
		}
		$chmod = substr(sprintf('%o', fileperms($file)), -4);
		$ret = false;
		if((strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' && ($fp = fopen($file, "w+"))!==false) || (is_writable($file) && $chmod=="0777" && ($fp = fopen($file, "w+"))!==false)) {
			@chmod($file, 0200);
			if($php) {
				$d = '<?php die(); ?>'.$d;
			}
			fwrite($fp, $d);
			fclose($fp);
			@chmod($file, 0777);
			$ret = true;
		} else if(self::$tryWrite>999999) {
			throw new Exception("Error writing user list", 1);
			die();
		} else {
			usleep(800);
			self::$tryWrite++;
			return self::safeSave($file, $d, $php);
		}
		self::$tryWrite = 0;
		return $ret;
	}

	public static function appendSave($file, $data = "", $php = false) {
		if(!file_exists($file)) {
			file_put_contents($file, "");
		}
		if(!is_string($data)) {
			$data = json_encode($data);
		}
		$chmod = substr(sprintf('%o', fileperms($file)), -4);
		$ret = false;
		if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' || (is_writable($file) && $chmod=="0777" && ($fp = fopen($file, "a"))!==false)) {
			@chmod($file, 0200);
			if($php) {
				$data = '<?php die(); ?>'.$data;
			}
			fwrite($fp, $data.PHP_EOL);
			fclose($fp);
			@chmod($file, 0777);
			$ret = true;
		} else if(self::$tryWrite>999999) {
			throw new Exception("Error writing user list", 1);
			die();
		} else {
			usleep(800);
			self::$tryWrite++;
			return self::appendSave($file, $data, $php);
		}
		self::$tryWrite = 0;
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