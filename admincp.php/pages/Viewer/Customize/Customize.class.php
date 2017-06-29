<?php

class Customize extends Core {
	
	function __construct() {
		$this->ModuleList("Customize", array(&$this, "changeMenu"));
		config::Set("FullMenu", "1");
		$this->Prints("getCustomize");
	}
	
	function changeMenu() {
		templates::resetVars("menu");
		templates::assign_vars(array(
			"value" => "{L_'Просмотр темы'}<br><b>{C_skins[skins]}</b>",
			"link" => "",
			"is_now" => "0",
			"type_st" => "",
			"type_end" => "",
			"icon" => " ",
		), "menu", 1);
	}
	
}