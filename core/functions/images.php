<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

//ToDo: А эта функция мне ещё на кой чёрт?!
function images($name, $save) {
	if(!is_uploaded_file($_FILES[$name]['tmp_name'])) {
		return "";
	}
	$exp = explode("/", $_FILES[$name]['type']);
	if(current($exp)!="image") {
		return "";
	}
	$img = getimagesize($_FILES[$name]['tmp_name']);
	if(empty($img)) {
		return "";
	}
	if(!move_uploaded_file($_FILES[$name]['tmp_name'], $save)) {
		return "";
	} else {
		return str_replace(ROOT_PATH, "", $save);
	}
}

function saveFile(array $file, $filename = "", $directory = "", $chmod = 0644, $type = "") {
	if(!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
		return false;
	}
	if(!is_int($chmod)) {
		$chmod = 0664;
	}
	if(!empty($filename)) {
		$filename = uniqid().$file['name'];
	}
	$filename = preg_replace('/\s+/u', '_', $filename);
	if(!empty($directory)) {
		$directory = ROOT_PATH."uploads";
	}
	if(!is_dir($directory) || !is_writable(realpath($directory))) {
		return false;
	}
	if(!empty($type) && !Validate::typeFile($file, $type)) {
		return false;
	}
	$filename = realpath($directory).DS.$filename;
	if(move_uploaded_file($file['tmp_name'], $filename)) {
		if($chmod !== false) {
			chmod($filename, $chmod);
		}
		return str_replace(ROOT_PATH, "", $filename);
	}
	return false;
}

?>