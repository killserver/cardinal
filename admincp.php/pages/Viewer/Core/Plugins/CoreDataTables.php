<?php

class CoreDataTables extends Core {
	
	function __construct() {
		$this->InsertList("DataTables1", "assets/".config::Select('skins','admincp')."/js/datatables/js/jquery.dataTables.min.js?1", "js");
		$this->InsertList("DataTables2", "assets/".config::Select('skins','admincp')."/js/datatables/dataTables.bootstrap.js?1", "js");
		$this->InsertList("DataTables3", "assets/".config::Select('skins','admincp')."/js/datatables/yadcf/jquery.dataTables.yadcf.js?1", "js");
		$this->InsertList("DataTables4", "assets/".config::Select('skins','admincp')."/js/datatables/tabletools/dataTables.tableTools.min.js?1", "js");
	}
	
}

?>