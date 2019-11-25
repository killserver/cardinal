<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

function CheckCanGzip() {
	return BrowserSupport::gzip();
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