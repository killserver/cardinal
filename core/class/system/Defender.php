<?php

class Defender {

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