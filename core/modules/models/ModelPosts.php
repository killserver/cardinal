<?php

class ModelPosts extends DBObject {

	public $id;
	public $title;
	public $alt_name;
	public $image;
	public $descr;
	public $cat_id;
	public $time;
	public $added;
	public $stat;
	public $active = 'no';
	public $type = 'post';

	function init_model($type) {
		$this->SetTable(PREFIX_DB."posts");
		$this->WhereTo("type", "LIKE", $type);
	}
}

?>