<?php

class Settings_Skins extends Settings {
	
	function __construct() {
		$this->ParseSkins();
		$this->ParseSkins("", "mobile", "_mobile");
		$this->ParseSkins(ROOT_PATH.ADMINCP_DIRECTORY."/temp/", "admincp", "_admin");
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
		if(isset($args['skins_mobile']) && $args['skins_mobile']!=config::Select("skins", "mobile")) {
			$return .= "\t'mobile' => '".saves($args['skins_mobile'])."',\n";
		} else {
			$return .= "\t'mobile' => '".config::Select("skins", "mobile")."',\n";
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