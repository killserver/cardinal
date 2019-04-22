<?php
/*
Name: Установка модулей, плагинов, тем и разделов для движка CE
Version: 1.9.0
Author: killserver
OnlyUse: true
 */
if(!defined("IS_CORE")) {
	echo "403 ERROR";
	die();
}

class installerAdmin extends modules {

	function __construct() {
		addEvent("loadUserLevels", array($this, "installerLevel"));
	}

	function installerLevel($levels) {
		$levels[LEVEL_CREATOR]['access_installer'] = "yes";
		return $levels;
	}

	public static $version = "1.9.0";

}

?>