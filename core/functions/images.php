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

?>