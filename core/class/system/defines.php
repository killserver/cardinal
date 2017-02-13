<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die;
}

class defines {
	
	public static function init() {
		$all = modules::manifest_get('define');
		if(!is_array($all)) {
			return;
		}
		$keys = array_keys($all);
		$vals = array_values($all);
		for($i=0;$i<sizeof($keys);$i++) {
			if(!defined($keys[$i])) {
				define($keys[$i], $vals[$i]);
			}
		}
	}
	
	public static function all() {
		$all = modules::manifest_get('define');
		if(!is_array($all)) {
			return array();
		}
		return $all;
	}
	
	public static function add($name, $val) {
		modules::manifest_set(array('define', $name), $val);
	}
	
}

?>