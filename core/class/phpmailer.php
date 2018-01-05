<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

if(version_compare(PHP_VERSION, '5.6.0') >= 0) {
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."PHPMailer7.php");
} else {
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."PHPMailer5.php");
}