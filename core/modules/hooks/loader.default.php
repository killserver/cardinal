<?php
if(!defined("IS_CORE")) {
	echo "403 ERROR";
	die();
}

$hooksLoad = array_merge($hooksLoad, array(
	"base" => true,
));

?>