<?php

class Settings_FullMenu extends Settings {
	
	function __construct() {
		Settings::AddFunc(array("name" => "FullMenu", "func" => array(&$this, "PluginSave")));
		Settings::AddNav(array(array(
			"subname" => "FullMenu",
			"name" => "{L_\"Полное боковое меню\"}",
			"options" => "{include templates=\"FullMenu.tpl,\"}",
		)));
	}
	
	public function PluginSave($args) {
		if(isset($args['FullMenu']) && $args['FullMenu']=="on") {
			$return = "\t'FullMenu' => true,\n";
		} else {
			$return = "\t'FullMenu' => false,\n";
		}
		return $return;
	}
	
}

?>