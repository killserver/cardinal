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
	return call_user_func_array("Files::saveFile", func_get_args());
}

function reArrayFiles(&$file_post) {
	return call_user_func_array("Files::reArrayFiles", func_get_args());
}

function getInfoFile($path) {
	return call_user_func_array("Files::getInfoFile", func_get_args());
}

?>