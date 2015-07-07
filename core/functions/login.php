<?php
/*
*
* Version Engine: 1.25.5a8
* Version File: 3
*
* 3.1
* add support created password for modules
*
* add support writing and deleting data in cookies
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

function user_link($user_link, $user=null, $type=null, $added=null) {
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

function ToCookie($name, $value=null, $method="write") {
	if($method=="delete") {
		setcookie($name, $value, time()-(60*24*60*60), "/", ".".config::Select('default_http_hostname'), 1);
	} else {
		setcookie($name, $value, time()+(120*24*60*60), "/", ".".config::Select('default_http_hostname'), false, true);
	}
}

?>