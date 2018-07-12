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

function hex2rgb($hex) {
	// Copied
	$hex = str_replace("#", "", $hex);
	switch (strlen($hex)) {
		case 1:
			$hex = $hex.$hex;
		case 2:
			$r = hexdec($hex);
			$g = hexdec($hex);
			$b = hexdec($hex);
		break;
		case 3:
			$r = hexdec(substr($hex,0,1).substr($hex,0,1));
			$g = hexdec(substr($hex,1,1).substr($hex,1,1));
			$b = hexdec(substr($hex,2,1).substr($hex,2,1));
		break;
		default:
			$r = hexdec(substr($hex,0,2));
			$g = hexdec(substr($hex,2,2));
			$b = hexdec(substr($hex,4,2));
		break;
	}
	$rgb = array($r, $g, $b);
	return implode(",", $rgb); 
}

function random_color() {
	return str_pad(dechex(mt_rand(0, 100)), 2, '0', STR_PAD_LEFT);
}

if(!function_exists('imagepalettetotruecolor')) {
	function imagepalettetotruecolor(&$src) {
		if(imageistruecolor($src)) {
			return true;
		}
		$dst = imagecreatetruecolor(imagesx($src), imagesy($src));
		imagecopy($dst, $src, 0, 0, 0, 0, imagesx($src), imagesy($src));
		imagedestroy($src);
		$src = $dst;
		return true;
	}
}

?>