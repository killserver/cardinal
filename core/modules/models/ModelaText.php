<?php

class ModelaText extends DBObject {
	
	public $aId;
	public $lang;
	public $page;
	public $text;
	
	public function init_model() {
		$this->SetTable(PREFIX_DB."aText");
	}
	
}