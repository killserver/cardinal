<?php

class Login_MainChange extends Login {
	
	function __construct() {
		if(file_exists(ROOT_PATH.(defined("ADMINCP_DIRECTORY") ? ADMINCP_DIRECTORY : "admincp.php").DS."assets".DS.config::Select('skins','admincp').DS."css".DS."mainPage.css")) {
			$this->InsertList("MainChange", config::Select("default_http_local").(defined("ADMINCP_DIRECTORY") ? ADMINCP_DIRECTORY : "admincp.php")."/assets/".config::Select('skins','admincp')."/css/mainPage.css", "css");
		}
		if(file_exists(ROOT_PATH.(defined("ADMINCP_DIRECTORY") ? ADMINCP_DIRECTORY : "admincp.php").DS."assets".DS.config::Select('skins','admincp')."/css".DS."login.css")) {
			$this->InsertList("MainChange", config::Select("default_http_local").(defined("ADMINCP_DIRECTORY") ? ADMINCP_DIRECTORY : "admincp.php")."/assets/".config::Select('skins','admincp')."/css/login.css", "css");
		} else if(file_exists(ROOT_PATH.(defined("ADMINCP_DIRECTORY") ? ADMINCP_DIRECTORY : "admincp.php").DS."assets".DS.config::Select('skins','admincp').DS."css".DS."login-default.css")) {
			$this->InsertList("MainChange", config::Select("default_http_local").(defined("ADMINCP_DIRECTORY") ? ADMINCP_DIRECTORY : "admincp.php")."/assets/".config::Select('skins','admincp')."/css/login-default.css", "css");
		}
	}
	
}

?>