<?php

class ModelSeoBlock extends DBObject {
	
	public $sId;
	public $sPage;
	public $sLang;
	public $sTitle;
	public $sMetaDescr;
	public $sMetaRobots;
	public $sMetaKeywords;
	public $sRedirect;
	public $sImage;

	function init_model() {
		$this->SetTable(PREFIX_DB."seoBlock");
	}
	
}

?>