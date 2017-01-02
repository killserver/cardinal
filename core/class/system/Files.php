<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

/**
 * Class Files
 * Class for working with files
 */
class Files {

    /**
     * Check necessary type file
     * @param array $file Element array file
     * @param string $type Needed type file
     * @return bool Check necessary type file
     */
    final private static function typeFile($file, $type) {
		if(!is_array($file) || !isset($file['type']) || !isset($file['error']) || $file['error'] != 0) {
			return false;
		}
		if(!isset($file['error']) || !isset($file['name']) || !isset($file['type']) || !isset($file['tmp_name']) || !isset($file['size'])) {
			return false;
		}
		$exp = explode("/", $file['type']);
		$rt = current($exp);
		$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
		if(is_string($type)) {
			return (($rt === $type) || ($ext === $type));
		} elseif(is_array($type)) {
			return (in_array($rt, $type) || in_array($ext, $type));
		}
	}

    /**
     * Save file to directory
     * @param array $file Array $_FILES
     * @param string $filename Needed file name. If not set uses unique name
     * @param string $directory Directory for save file
     * @param int $chmod Charter access. If not set uses 0644
     * @param string $type Needed type. If not set can upload all files
     * @param bool $force If file exists system delete him
     * @param bool $copy Copy instead move file
     * @return bool|string Name uploaded file
     */
    final public static function saveFile(array $file, $filename = "", $directory = "", $chmod = 0644, $type = "", $force = false, $copy = false) {
		if(!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
			return false;
		}
		if(!is_int($chmod)) {
			$chmod = 0664;
		}
		if(empty($filename)) {
			$filename = uniqid().$file['name'];
		}
		$filename = preg_replace('/\s+/u', '_', $filename);
		if(empty($directory)) {
			$directory = ROOT_PATH."uploads";
		}
		if(!is_dir($directory) || !is_writable(realpath($directory))) {
			return false;
		}
		if(!empty($type) && !self::typeFile($file, $type)) {
			return false;
		}
		$filename = realpath($directory).DS.$filename;
		if($force && file_exists($filename)) {
			unlink($filename);
		}
		if(!$copy) {
			if(move_uploaded_file($file['tmp_name'], $filename)) {
				if($chmod !== false) {
					chmod($filename, $chmod);
				}
				return str_replace(ROOT_PATH, "", $filename);
			}
		} else {
			if(copy($file['tmp_name'], $filename)) {
				if($chmod !== false) {
					chmod($filename, $chmod);
				}
				return str_replace(ROOT_PATH, "", $filename);
			}
		}
		return false;
	}

    /**
     * Rebuild array. Order first, name is second in result array
     * @param array $file_post Array $_FILES
     * @return array Order first, name is second in result array
     */
    final public static function reArrayFiles(&$file_post) {
		$file_ary = array();
		$file_count = count($file_post['name']);
		$file_keys = array_keys($file_post);
		for($i=0;$i<$file_count;$i++) {
			foreach($file_keys as $key) {
				$file_ary[$i][$key] = $file_post[$key][$i];
			}
		}
		return $file_ary;
	}

    /**
     * Get info of file
     * @param string $path Full path to needed file
     * @return array Result data of file
     */
    final public static function getInfoFile($path) {
		$trimPath = rtrim($path, '/');
		$slashPos = strrpos($trimPath, '/');
		if($slashPos !== false) {
			$dirName = substr($trimPath, 0, $slashPos) ? '.' : '/';
			$baseName = substr($trimPath, $slashPos + 1);
		} else {
			$dirName = '.';
			$baseName = $trimPath;
		}

		$dotPos = strrpos($baseName, '.');
		if($dotPos !== false) {
			$fileName = substr($baseName, 0, $dotPos);
			$extension = substr($baseName, $dotPos + 1);
		} else {
			$extension = '';
			$fileName = $baseName;
		}
		return array("dirName" => $dirName, "ext" => $extension, "filename" => $fileName);
	}
	
}