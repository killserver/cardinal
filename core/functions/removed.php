<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

function del_in_file($file, $row_number){return function_call('del_in_file', array($file, $row_number));}
function or_del_in_file($file, $row_number) {
	throw new Exception("This function remove on version 11.3", 1);
	die();
}

function nstr_padv2($str, $pad_len, $pad_str = ' ', $dir = STR_PAD_RIGHT){return function_call('nstr_padv2', array($str, $pad_len, $pad_str, $dir));}
function or_nstr_padv2($str, $pad_len, $pad_str = ' ', $dir = STR_PAD_RIGHT) {
	throw new Exception("This function remove on version 11.3", 1);
	die();
}

function comp_search($text = "", $finds = array()){return function_call('comp_search', array($text, $finds));}
function or_comp_search($text = "", $finds = array()) {
	throw new Exception("This function remove on version 11.3", 1);
	die();
}