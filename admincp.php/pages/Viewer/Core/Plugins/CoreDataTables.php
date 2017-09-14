<?php

class CoreDataTables extends Core {
	
	function __construct() {
		$this->InsertList("DataTables1", config::Select("default_http_local").(defined("ADMINCP_DIRECTORY") ? ADMINCP_DIRECTORY : "admincp.php")."/assets/".config::Select('skins','admincp')."/js/datatables/js/jquery.dataTables.min.js?1", "js");
		$this->InsertList("DataTables2", config::Select("default_http_local").(defined("ADMINCP_DIRECTORY") ? ADMINCP_DIRECTORY : "admincp.php")."/assets/".config::Select('skins','admincp')."/js/datatables/dataTables.bootstrap.js?1", "js");
		$this->InsertList("DataTables3", config::Select("default_http_local").(defined("ADMINCP_DIRECTORY") ? ADMINCP_DIRECTORY : "admincp.php")."/assets/".config::Select('skins','admincp')."/js/datatables/yadcf/jquery.dataTables.yadcf.js?1", "js");
		$this->InsertList("DataTables4", config::Select("default_http_local").(defined("ADMINCP_DIRECTORY") ? ADMINCP_DIRECTORY : "admincp.php")."/assets/".config::Select('skins','admincp')."/js/datatables/tabletools/dataTables.tableTools.min.js?1", "js");
		$this->InsertList("BootstrapTourCSS", config::Select("default_http_local").(defined("ADMINCP_DIRECTORY") ? ADMINCP_DIRECTORY : "admincp.php")."/assets/".config::Select('skins','admincp')."/css/bootstrap-tour.css", "css");
		$this->InsertList("BootstrapTourJS", config::Select("default_http_local").(defined("ADMINCP_DIRECTORY") ? ADMINCP_DIRECTORY : "admincp.php")."/assets/".config::Select('skins','admincp')."/js/bootstrap-tour.js", "js");
		$this->InsertList("Yui", config::Select("default_http_local").(defined("ADMINCP_DIRECTORY") ? ADMINCP_DIRECTORY : "admincp.php")."/assets/".config::Select('skins','admincp')."/js/tour.min.js", "js");
	}
	
}

?>