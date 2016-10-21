<?php
if(!defined("IS_CORE")) {
	echo "403 ERROR";
	die();
}

$modulesLoad = array_merge($modulesLoad, array(
	"core".DS."modules".DS."base.class.".ROOT_EX => true,
));