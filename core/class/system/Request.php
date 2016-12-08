<?php

class Request {
	
	public $post = array();
	public $get = array();
	public $server = array();
	public $files = array();
	
	public function __construct() {
		$this->post = new RequestMethod($_POST);
		$this->get = new RequestMethod($_GET);
		$this->server = new RequestMethod($_SERVER);
		$this->files = new RequestMethod($_FILES);
	}
	
}

class RequestMethod {
	
	private $data = array();
	
	final function __construct(array $arr) {
		$this->data = $arr;
		return $this;
	}
	
	final public function get($key = "", $default = "") {
		return Arr::get($this->data, $key, $default);
	}
	
	final public function found($keys = "", $default = "") {
		return Arr::found($this->data, $key, $default);
	}
	
	final public function push($mixed = "") {
		return Arr::push($this->data, $mixed);
	}
	
	final public function foundValues($array = array()) {
		return Arr::foundValues($this->data, $array);
	}
	
	final public function map($callback = "") {
		return Arr::map($this->data, $callback);
	}
	
	final public function GetAll() {
		return $this->data;
	}
	
	final public function Gets() {
		$arr = array($this->data, func_get_args());
		return call_user_func_array("Arr::GetAll", $arr);
	}
	
}

?>