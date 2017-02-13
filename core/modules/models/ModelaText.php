<?php

class ModelaText extends DBObject {
	
	public $aId;
	public $page;
	public $text;
	
	public function init_model() {
		$this->SetTable("aText");
	}
	
}