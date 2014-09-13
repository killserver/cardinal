<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die;
}

final class config {

	private $config = array();

	function __construct() {
	global $config, $db, $cache;
		$this->config = array();
		if(!$cache->exists("config")) {
			$configs = array();
			$db->doquery("SELECT config_name, config_value FROM config", true);
			while($conf = $db->fetch_array()) {
				if(strpos($conf['config_value'], ":-:")!==false) {
					$vals = array();
					if(strpos($conf['config_value'], ";-;")!==false) {
						$exp = explode(";-;", $conf['config_value']);
						for($i=0;$i<sizeof($exp);$i++) {
							$ex = explode(":-:", $exp[$i]);
							$vals[$ex[0]] = $ex[1];
						}
						$configs[$conf['config_name']] = $vals;
					} else {
						$exp = explode(":-:", $conf['config_value']);
						$vals[$exp[0]] = $exp[1];
						$configs[$conf['config_name']] = $vals;
					}
				} else {
					$configs[$conf['config_name']] = $conf['config_value'];
				}
			}
			$this->config = $configs;
			$cache->set("config", $configs);
			unset($configs);
		} else {
			$this->config = $cache->get("config");
		}
		$this->config = (sizeof($this->config) > 0 ? array_merge($config, $this->config) : $config);
	}

	function all() {
		return $this->config;
	}

	function select($data) {
		if(isset($this->config[$data])) {
			return $this->config[$data];
		} else {
			return false;
		}
	}

	function update($name, $data=null) {
	global $db, $cache;
		$db->doquery("UPDATE config SET config_value = \"".$data."\" WHERE config_name = \"".$name."\"");
		$cache->delete("config");
		if(!empty($this->config[$data])) {
			return $this->config[$data];
		} else {
			return true;
		}
	}

	function __destruct() {
		unset($this);
	}

}

?>