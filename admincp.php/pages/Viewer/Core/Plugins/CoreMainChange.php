<?php

class CoreMainChange extends Core {
	
	function __construct() {
		if(file_exists(ROOT_PATH.(defined("ADMINCP_DIRECTORY") ? ADMINCP_DIRECTORY : "admincp.php")."/assets/".config::Select('skins','admincp')."/css/mainPage.css")) {
			$this->InsertList("MainChange", config::Select("default_http_local").(defined("ADMINCP_DIRECTORY") ? ADMINCP_DIRECTORY : "admincp.php")."/assets/".config::Select('skins','admincp')."/css/mainPage.css?".time(), "css");
		}
	}
	
}

?>