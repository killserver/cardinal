<?php

class AText {
	
	private $uri = "";
	private $lang = "";
	private $text = array();
	
	function __construct() {
		if(defined("WITHOUT_DB") && !db::connected()) {
			return $this;
		}
		if(file_exists(ROOT_PATH."core".DS."cache".DS."system".DS) && is_writable(ROOT_PATH."core".DS."cache".DS."system".DS) && !file_exists(ROOT_PATH."core".DS."cache".DS."system".DS."aText.lock")) {
			db::query("CREATE TABLE IF NOT EXISTS `aText` ( `aId` int not null auto_increment, `lang` varchar(255) not null, `page` varchar(255) not null, `text` longtext not null, primary key `id`(`aId`), fulltext `page`(`page`), fulltext `lang`(`lang`), fulltext `text`(`text`(200)) ) ENGINE=MyISAM;");
			file_put_contents(ROOT_PATH."core".DS."cache".DS."system".DS."aText.lock", "");
		}
		$this->uri = str_replace(array($_SERVER['PHP_SELF']."?", $_SERVER['PHP_SELF']."/"), "", $_SERVER['REQUEST_URI']);
		if(strpos($this->uri, "?")!==false) {
			$page = explode("?", $this->uri);
			if(strlen($page[0])>0) {
				if(isset($page[1])) {
					unset($page[1]);
				}
				$this->uri = implode("", $page);
				if(preg_match("#^([a-zA-Z0-9]+){2}(/?)#", $this->uri, $match)) {
					if(isset($match[1])) {
						$this->lang = $match[1];
					}
				}
			}
		}
	}
	
	public static function init() {
		if(defined("WITHOUT_DB") && !db::connected()) {
			return false;
		}
		if(file_exists(ROOT_PATH."core".DS."cache".DS."system".DS) && is_writable(ROOT_PATH."core".DS."cache".DS."system".DS) && !file_exists(ROOT_PATH."core".DS."cache".DS."system".DS."aText.lock")) {
			db::query("CREATE TABLE IF NOT EXISTS `aText` ( `aId` int not null auto_increment, `lang` varchar(255) not null, `page` varchar(255) not null, `text` longtext not null, primary key `id`(`aId`), fulltext `page`(`page`), fulltext `lang`(`lang`), fulltext `text`(`text`(200)) ) ENGINE=MyISAM;");
			file_put_contents(ROOT_PATH."core".DS."cache".DS."system".DS."aText.lock", "");
			return true;
		} else if(file_exists(ROOT_PATH."core".DS."cache".DS."system".DS."aText.lock")) {
			return true;
		} else {
			return false;
		}
	}
	
	private function cacheSave($data = array()) {
		$files = array();
		if(file_exists(ROOT_PATH."core".DS."cache".DS."AText".(!empty($this->lang) ? "_".$this->lang : "").".txt") && is_readable(ROOT_PATH."core".DS."cache".DS."AText".(!empty($this->lang) ? "_".$this->lang : "").".txt")) {
			$files = file_get_contents(ROOT_PATH."core".DS."cache".DS."AText".(!empty($this->lang) ? "_".$this->lang : "").".txt");
			$files = unserialize($files);
		}
		$data = array_merge($files, $data);
		if(file_exists(ROOT_PATH."core".DS."cache".DS) && is_writable(ROOT_PATH."core".DS."cache".DS)) {
			file_put_contents(ROOT_PATH."core".DS."cache".DS."AText".(!empty($this->lang) ? "_".$this->lang : "").".txt", serialize($data));
			return true;
		} else {
			return false;
		}
	}
	
	private function cacheExist() {
		if(file_exists(ROOT_PATH."core".DS."cache".DS."AText".(!empty($this->lang) ? "_".$this->lang : "").".txt") && is_readable(ROOT_PATH."core".DS."cache".DS."AText".(!empty($this->lang) ? "_".$this->lang : "").".txt")) {
			$file = file_get_contents(ROOT_PATH."core".DS."cache".DS."AText".(!empty($this->lang) ? "_".$this->lang : "").".txt");
			$file = unserialize($file);
			if(isset($file[$this->uri])) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	private function cacheRead() {
		if(file_exists(ROOT_PATH."core".DS."cache".DS."AText".(!empty($this->lang) ? "_".$this->lang : "").".txt") && is_readable(ROOT_PATH."core".DS."cache".DS."AText".(!empty($this->lang) ? "_".$this->lang : "").".txt")) {
			$file = file_get_contents(ROOT_PATH."core".DS."cache".DS."AText".(!empty($this->lang) ? "_".$this->lang : "").".txt");
			$file = unserialize($file);
			if(isset($file[$this->uri])) {
				return unserialize($file[$this->uri]);
			} else {
				return array();
			}
		} else {
			return array();
		}
	}
	
	function get() {
		if(!$this->cacheExist($this->uri)) {
			db::doquery("SELECT `text` FROM `aText` WHERE".(!empty($this->lang) ? "`lang` LIKE \"".$this->lang."\" AND " : "")."`page` LIKE \"".$this->uri."%\" LIMIT 1", true);
			if(db::num_rows()==1) {
				$row = db::fetch_assoc();
				if(isset($row['text'])) {
					$this->text = unserialize($row['text']);
					$this->cacheSave($this->text);
				}
			}
		} else {
			$row = $this->cacheRead();
			if(isset($row['text'])) {
				$this->text = unserialize($row['text']);
			}
		}
		return $this;
	}
	
	function getArray() {
		$keys = array_keys($this->text);
		$arr = array();
		for($i=0;$i<sizeof($keys);$i++) {
			$arr[] = array("textInfo".($i+1) => $this->text[$keys[$i]]);
		}
		return $arr;
	}
	
	function __getHTML($tmp = false) {
		if($tmp!==false) {
			$keys = array_keys($this->text);
			for($i=0;$i<sizeof($keys);$i++) {
				$tmp->assign_vars(array("textInfo".($i+1) => $this->text[$keys[$i]]));
			}
			return true;
		} else {
			$keys = array_keys($this->text);
			for($i=0;$i<sizeof($keys);$i++) {
				templates::assign_vars(array("textInfo".($i+1) => $this->text[$keys[$i]]));
			}
			return true;
		}
	}
	
}

?>