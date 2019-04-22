<?php
if(!defined("IS_CORE")) {
	echo "403 ERROR";
	die();
}

$modulesLoad = array_merge($modulesLoad, array(
	"application".DS."modules".DS."base.class.".ROOT_EX => true,
	"application".DS."modules".DS."mobile.class.".ROOT_EX => true,
	"application".DS."modules".DS."changelog.class.".ROOT_EX => true,
	"application".DS."modules".DS."installerAdmin.class.".ROOT_EX => true,
));

?>