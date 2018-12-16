<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

class dbFile {

	private static $fileDefault = "dbData.dbFile";

	final public static function setFile($file) {
		self::$fileDefault = $file;
		return true;
	}
	
	final public static function open($file = "", $dir = "") {
		if(empty($file)) {
			$file = self::$fileDefault;
		}
		$files = (defined("PATH_CACHE_USERDATA") ? PATH_CACHE_USERDATA : dirname(__FILE__).DIRECTORY_SEPARATOR.$dir);
		if(!is_writeable($files)) {
			@chmod($files, 0777);
		}
		$files .= $file.".dbFile";
		$file = $files;
		if(file_exists($file)) {
			$file = file_get_contents($file);
			$file = preg_replace("#\<\?php(.*?)\?\>#is", "", $file);
			try {
				$file = json_decode($file, true);
			} catch(Exception $ex) {}
			if(is_array($file)) {
				return $file;
			} else {
				return array();
			}
		}
		return false;
	}
	
	final public static function openObject($file = "", $dir = "") {
		if(empty($file)) {
			$file = self::$fileDefault;
		}
		$files = (defined("PATH_CACHE_USERDATA") ? PATH_CACHE_USERDATA : dirname(__FILE__).DIRECTORY_SEPARATOR.$dir);
		if(!is_writeable($files)) {
			@chmod($files, 0777);
		}
		$files .= $file.".dbFile";
		$file = $files;
		if(file_exists($file)) {
			$file = file_get_contents($file);
			$file = preg_replace("#\<\?php(.*?)\?\>#is", "", $file);
			try {
				$file = json_decode($file);
			} catch(Exception $ex) {}
			return (is_string($file) ? new stdClass() : $file);
		}
		return false;
	}
	
	final public static function save($data, $file = "", $dir = "") {
		if(empty($file)) {
			$file = self::$fileDefault;
		}
		$files = (defined("PATH_CACHE_USERDATA") ? PATH_CACHE_USERDATA : dirname(__FILE__).DIRECTORY_SEPARATOR.$dir);
		if(!is_writeable($files)) {
			@chmod($files, 0777);
		}
		$files .= $file.".dbFile";
		$file = $files;
		if(!file_exists($file)) {
			@file_put_contents($file, '');
		}
		if(file_exists($file)) {
			$data = self::json_encode_unicode($data);
			$data = self::normalizer($data);
			try {
				$data = '<?php'.PHP_EOL.'if(!defined("IS_CORE") {'.PHP_EOL.'echo "403 ERROR!!!";die();'.PHP_EOL.'}'.PHP_EOL.'?>'.PHP_EOL.$data;
				@file_put_contents($file, $data);
				return true;
			} catch(Exception $ex) {
				return false;
			}
		}
		return false;
	}
	
	final public static function delete($file = "", $dir = "") {
		if(empty($file)) {
			$file = self::$fileDefault;
		}
		$file = (defined("PATH_CACHE_USERDATA") ? PATH_CACHE_USERDATA : dirname(__FILE__).DIRECTORY_SEPARATOR.$dir);
		if(!is_writeable($file)) {
			@chmod($file, 0777);
		}
		$file .= $file.".dbFile";
		if(file_exists($file)) {
			@unlink($file);
			return true;
		}
		return false;
	}

	final public static function json_encode_unicode($arr, $params = "") {
		if(defined('JSON_UNESCAPED_UNICODE')) {
			if($params !== "") {
				return json_encode($arr, $params | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
			} else {
				return json_encode($arr, JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
			}
		} else {
			return preg_replace_callback('/(?<!\\\\)\\\\u([0-9a-f]{4})/i', "dbFile::json_encode_unicode_fn", json_encode($arr, $params));
		}
	}

	final private static function json_encode_unicode_fn($m) {
		$d = pack("H*", $m[1]);
		$r = mb_convert_encoding($d, "UTF8", "UTF-16BE");
		return $r!=="?" && $r!=="" ? $r : $m[0];
	}
	
	final private static function normalizer($data) {
		$arr = array();
		$tab = 1;
		$d = false;
		for($f=0;$f<strlen($data);$f++) {
			$bytes = $data[$f];
			if($d && $bytes === $d) {
				$data[$f - 1] !== "\\" && ($d = !1);
			} else if(!$d && ($bytes === '"' || $bytes === "'")) {
				$d = $bytes;
			} else if(!$d && ($bytes === " " || $bytes === "\t")) {
				$bytes = "";
			} else if(!$d && $bytes === ":") {
				$bytes = $bytes." ";
			} else if(!$d && $bytes === ",") {
				$bytes = $bytes."\n";
				$bytes = str_pad($bytes, ($tab * 2), " ");
			} else if(!$d && ($bytes === "[" || $bytes === "{")) {
				$tab++;
				$bytes .= "\n";
				$bytes = str_pad($bytes, ($tab * 2), " ");
			} else if(!$d && ($bytes === "]" || $bytes === "}")) {
				$tab--;
				$bytes = str_pad("\n", ($tab * 2), " ").$bytes;
			}
			array_push($arr, $bytes);
		}
		return implode("", $arr);
	}

}

?>