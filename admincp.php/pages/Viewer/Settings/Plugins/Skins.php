<?php

class Settings_Skins extends Settings {
	
	function __construct() {
		$this->ParseSkins();
		$this->ParseSkins(ROOT_PATH."admincp.php/temp/", "_admin");
		Settings::AddFunc(array("name" => "Skins", "func" => array(&$this, "PluginSave")));
		Settings::AddNav(array(array(
			"subname" => "skins",
			"name" => "{L_skins}",
			"options" => "{include templates=\"Skins.tpl,\"}",
		)));
	}
	
	public function PluginSave($args) {
		$return = "\n'skins' => array(\n";
		if(isset($args['skins']) && $args['skins']!=config::Select("skins", "skins")) {
			$return .= "\t'skins' => '".saves($args['skins'])."',\n";
		} else {
			$return .= "\t'skins' => '".config::Select("skins", "skins")."',\n";
		}
		if(isset($args['skins_admin']) && $args['skins_admin']!=config::Select("skins", "admincp")) {
			$return .= "\t'admincp' => '".saves($args['skins_admin'])."',\n";
		} else {
			$return .= "\t'admincp' => '".config::Select("skins", "admincp")."',\n";
		}
		$return .= "),";
		return $return;
	}
	
}

?>