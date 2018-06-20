<?php

class Settings_Uptime extends Settings {
	
	function __construct() {
		Settings::AddFunc(array("name" => "Uptime", "func" => array(&$this, "PluginSave")));
		Settings::AddNav(array(array(
			"subname" => "uptime",
			"name" => "{L_\"Время работы\"}",
			"options" => "{include templates=\"Uptime.tpl,\"}",
		)));
	}
	
	public function PluginSave($args) {
		$return = "\n'uptime' => array(\n";
		if(isset($args['uptimerobot_api']) && $args['uptimerobot_api']!=config::Select("uptime", "uptimerobot_api")) {
			$return .= "\t'uptimerobot_api' => '".saves($args['uptimerobot_api'])."',\n";
		} else {
			$return .= "\t'uptimerobot_api' => '".config::Select("uptime", "uptimerobot_api")."',\n";
		}
		if(isset($args['uptimerobot_id']) && $args['uptimerobot_id']!=config::Select("uptime", "uptimerobot_id")) {
			$return .= "\t'uptimerobot_id' => '".saves($args['uptimerobot_id'])."',\n";
		} else {
			$return .= "\t'uptimerobot_id' => '".config::Select("uptime", "uptimerobot_id")."',\n";
		}
		if(isset($args['ping_admin_api']) && $args['ping_admin_api']!=config::Select("uptime", "ping_admin_api")) {
			$return .= "\t'ping_admin_api' => '".saves($args['ping_admin_api'])."',\n";
		} else {
			$return .= "\t'ping_admin_api' => '".config::Select("uptime", "ping_admin_api")."',\n";
		}
		if(isset($args['syslab_api']) && $args['syslab_api']!=config::Select("uptime", "syslab_api")) {
			$return .= "\t'syslab_api' => '".saves($args['syslab_api'])."',\n";
		} else {
			$return .= "\t'syslab_api' => '".config::Select("uptime", "syslab_api")."',\n";
		}
		if(isset($args['syslab_id']) && $args['syslab_id']!=config::Select("uptime", "syslab_id")) {
			$return .= "\t'syslab_id' => '".saves($args['syslab_id'])."',\n";
		} else {
			$return .= "\t'syslab_id' => '".config::Select("uptime", "syslab_id")."',\n";
		}
		$return .= "),";
		return $return;
	}
	
}

?>