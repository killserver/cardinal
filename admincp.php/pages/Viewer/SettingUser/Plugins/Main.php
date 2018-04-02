<?php

class SettingUser_Main extends SettingUser {

	function __construct() {
		self::add("{include templates=\"Main.tpl,SettingUser\"}", "{L_'Настройка системы'}");
	}

}