<?php

class CoreSelect2 extends Core {
	
	function __construct() {
		$this->InsertList("Select2-1", config::Select("default_http_local").(defined("ADMINCP_DIRECTORY") ? ADMINCP_DIRECTORY : "admincp.php")."/assets/".config::Select('skins','admincp')."/js/select2/select2.css", "css");
		$this->InsertList("Select2-2", config::Select("default_http_local").(defined("ADMINCP_DIRECTORY") ? ADMINCP_DIRECTORY : "admincp.php")."/assets/".config::Select('skins','admincp')."/js/select2/select2-bootstrap.css", "css");
		$this->InsertList("Select2-3", config::Select("default_http_local").(defined("ADMINCP_DIRECTORY") ? ADMINCP_DIRECTORY : "admincp.php")."/assets/".config::Select('skins','admincp')."/js/select2/select2.min.js", "js");
		$this->InsertList("Select2-4", config::Select("default_http_local").(defined("ADMINCP_DIRECTORY") ? ADMINCP_DIRECTORY : "admincp.php")."/assets/".config::Select('skins','admincp')."/js/select2/select2-apply.js?".time(), "js");
	}
	
}

?>