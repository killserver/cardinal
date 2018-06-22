<?php

class SettingUser_Main extends SettingUser {

	function __construct() {
		$echos = "{include templates=\"Main.tpl,SettingUser\"}";
		$echos = execEvent("settinguser_main", $echos);
		self::add($echos, "{L_'Настройка системы'}");
	}

}