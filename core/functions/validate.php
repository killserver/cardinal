<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}


function is_serialized($data) {
	return Validate::is_serialized($data);
}

function is_xml($string) {
	return Validate::is_xml($string);
}

function is_html($string) {
	return Validate::is_html($string);
}

if(!function_exists('is_iterable')) {
	/**
	 * Check wether or not a variable is iterable (i.e array or \Traversable)
	 *
	 * @param  array|\Traversable $iterable
	 * @return bool
	 */
	function is_iterable($iterable) {
		return (is_array($iterable) || $iterable instanceof \Traversable);
	}
}

function is_uuid4($uuid) {
	return Validate::is_uuid4($uuid);
}


function is_utf8($str) {
	return Validate::is_utf8($str);
}

/*
 New on version 6.3
*/
function is_ascii($str) {
	return Validate::is_ascii($str);
}