<?php
/*
 *
 * @version 1.25.6-rc4
 * @copyright 2014-2015 KilleR for Cardinal Engine
 *
 * Version Engine: 1.25.6-rc4
 * Version File: 3
 *
 * 3.1
 * add support created password for modules
 * 3.2
 * add support writing and deleting data in cookies
 * 3.3
 * delete old function set/get/delete cookie and transfer logic to HTTP class
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

function create_pass($pass){return function_call('create_pass', array($pass));}
function or_create_pass($pass) {
	$pass = md5(md5($pass).$pass);
	$pass = strrev($pass);
	$pass = sha1($pass);
	//$pass = crypt($pass);
	$pass = bin2hex($pass);
return md5(md5($pass).$pass);
}

//ToDo: Надо с этой функцией что-то делать... она баластом в движке валяется
function user_link($user_link, $user = "", $type = "", $added = "") {
	if(empty($user_link)) {
		$user_link = "404";
	}
	if(empty($type)) {
		return "{C_default_http_host}user/".$user_link;
	} elseif($type == "href" && !empty($user)) {
		return "<a href=\"{C_default_http_host}user/".$user_link."\">".$user."</a>";
	} elseif($type == "user" && !empty($user)) {
		return $user;
	}
return "";
}

?>