<?php

class Request {
	
	public $post = array();
	public $get = array();
	public $server = array();
	public $env = array();
	public $files = array();
	public $session = array();
	
	public function __construct() {
		$this->post = new RequestMethod($_POST, "post");
		$this->get = new RequestMethod($_GET, "get");
		$this->server = new RequestMethod($_SERVER, "server");
		$this->env = new RequestMethod($_ENV, "env");
		$this->files = new RequestMethod($_FILES, "files");
		cardinal::StartSession();
		global $session;
		$session = $_SESSION;
		$this->session = new RequestMethod($_SESSION, "session");
	}
	
}

class RequestMethod implements Countable {
	
	private $data = array();
	private $type = "";
	
	final function __construct(array $arr, $type = "") {
		$this->data = $arr;
		$this->type = $type;
		return $this;
	}

	final public function count() {
		return sizeof($this->data);
	}

	final public function add($key, $val) {
	global $session;
		$this->data[$key] = $val;
		switch($this->type) {
			case 'post':
				$_POST[$key] = $val;
			break;
			case 'get':
				$_GET[$key] = $val;
			break;
			case 'server':
				$_SERVER[$key] = $val;
			break;
			case 'files':
				$_FILES[$key] = $val;
			break;
			case 'session':
				$_SESSION[$key] = $val;
				$session = $_SESSION;
			break;
			case 'env':
				$_ENV[$key] = $val;
			break;
		}
		return true;
	}

	final public function delete($key) {
	global $session;
		if(isset($this->data[$key])) {
			unset($this->data[$key]);
		} else {
			return false;
		}
		switch($this->type) {
			case 'post':
				if(isset($_POST[$key])) {
					unset($_POST[$key]);
				} else {
					return false;
				}
			break;
			case 'get':
				if(isset($_GET[$key])) {
					unset($_GET[$key]);
				} else {
					return false;
				}
			break;
			case 'server':
				if(isset($_SERVER[$key])) {
					unset($_SERVER[$key]);
				} else {
					return false;
				}
			break;
			case 'files':
				if(isset($_FILES[$key])) {
					unset($_FILES[$key]);
				} else {
					return false;
				}
			break;
			case 'session':
				if(isset($_SESSION[$key])) {
					unset($_SESSION[$key]);
					$session = $_SESSION;
				} else {
					return false;
				}
			break;
			case 'env':
				if(isset($_ENV[$key])) {
					unset($_ENV[$key]);
				} else {
					return false;
				}
			break;
		}
		return true;
	}

	final public function exists($key) {
	global $session;
		$ret = isset($this->data[$key]) ? true : false;
		switch($this->type) {
			case 'post':
				$ret = isset($_POST[$key]) ? true : false;
			break;
			case 'get':
				$ret = isset($_GET[$key]) ? true : false;
			break;
			case 'server':
				$ret = isset($_SERVER[$key]) ? true : false;
			break;
			case 'files':
				$ret = isset($_FILES[$key]) ? true: false;
			break;
			case 'session':
				$ret = isset($_SESSION[$key]) ? true : false;
				$session = $_SESSION;
			break;
			case 'env':
				$ret = isset($_ENV[$key]) ? true : false;
			break;
		}
		return $ret;
	}

	final public function __set($k, $v) {
		return $this->add($k, $v);
	}

	final public function __get($k) {
		return $this->get($k);
	}
	
	final public function __unset($k) {
		if(isset($this->data[$k])) {
			unset($this->data[$k]);
			return true;
		} else {
			return false;
		}
	}
	
	final public function __isset($k) {
		return isset($this->data[$k]);
	}

	final public function __debugInfo() {
		return $this->GetAll();
	}
	
	final public function get($key = "", $default = "") {
		return Arr::get($this->data, $key, $default);
	}
	
	final public function found($keys = "", $default = "") {
		return Arr::found($this->data, $keys, $default);
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