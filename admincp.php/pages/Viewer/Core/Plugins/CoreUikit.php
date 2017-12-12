<?php

class CoreUikit extends Core {
	
	function __construct() {
		$this->InsertList("uikit1", config::Select("default_http_local").(defined("ADMINCP_DIRECTORY") ? ADMINCP_DIRECTORY : "admincp.php")."/assets/".config::Select('skins','admincp')."/js/uikit/uikit.css", "css");
		$this->InsertList("uikit2", config::Select("default_http_local").(defined("ADMINCP_DIRECTORY") ? ADMINCP_DIRECTORY : "admincp.php")."/assets/".config::Select('skins','admincp')."/js/uikit/js/uikit.min.js", "js");
		$this->InsertList("uikit3", config::Select("default_http_local").(defined("ADMINCP_DIRECTORY") ? ADMINCP_DIRECTORY : "admincp.php")."/assets/".config::Select('skins','admincp')."/js/uikit/js/addons/nestable.min.js", "js");
	}
	
}

?>