<?php

if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

function create_pass($pass) {
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

?>