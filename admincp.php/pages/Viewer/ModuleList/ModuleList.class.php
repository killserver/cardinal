<?php

class ModuleList extends Core {
	
	private function ToString($str) {
		$str = (string) $str;
		return trim($str);
	}
	
	private function version_compare($version, $ser, $mod = ">=") {
		$version = str_ireplace(array("a", "b", "rc", "."), array("1", "2", "3", "4"), $version);
		$ser = str_ireplace(array("a", "b", "rc", "."), array("1", "2", "3", "4"), $ser);
		$ver_len = strlen($version);
		$ser_len = strlen($ser);
		if($ver_len>$ser_len) {
			$ser = str_pad($ser, $ver_len, "0", STR_PAD_RIGHT);
		} else if($ser_len>$ver_len) {
			$version = str_pad($version, $ser_len, "0");
		}
		return version_compare($version, $ser, $mod);
	}
	
	private function ReadModules() {
		$path = PATH_MODULES."xml".DS;
		$arr = read_dir($path, ".xml");
		$modules = array();
		for($i=0;$i<sizeof($arr);$i++) {
			$core_ver = $name = $image = $description = "";
			$comp = false;
			if(!file_exists($path.$arr[$i])) {
				continue;
			}
			try {
				$file = simplexml_load_string(file_get_contents($path . $arr[$i]));
			} catch(Exception $ex) {
				continue;
			}
			$first = "";
			$files = array();
			if(isset($file->files) && isset($file->files->file)) {
				for($is=0;$is<sizeof($file->files->file);$is++) {
					if(!isset($file->files->file[$is]) || !isset($file->files->file[$is]->attributes()->path)) {
						continue;
					}
					if(isset($file->files->file[$is]->attributes()->primary)) {
						$first = $this->ToString($file->files->file[$is]->attributes()->path);
					} else {
						$files[] = $this->ToString($file->files->file[$is]->attributes()->path);
					}
				}
			}
			if(!empty($first)) {
				$files = array_merge(array($first), $files);
			}
			if(isset($file->info->attributes()->module)) {
				$fmod = $this->ToString($file->info->attributes()->module);
			} else {
				$fmod = str_replace(".xml", "", $arr[$i]);
			}
			if(isset($file->info->image) && strpos($file->info->image, "base64")!==false) {
				$image = $this->ToString($file->info->image);
			}
			if(isset($file->info->name)) {
				$name = $this->ToString($file->info->name);
			}
			if(isset($file->info->description)) {
				$description = $this->ToString($file->info->description);
			}
			$dependency = array();
			if(isset($file->info->dependency->module)) {
				$dpm = $file->info->dependency->module;
				for($z=0;$z<sizeof($dpm);$z++) {
					$dependency[] = $this->ToString($dpm[0]);
				}
			}
			if(isset($file->info->dependency->engine)) {
				$core_ver = $this->ToString($file->info->dependency->engine);
				$comp = $this->version_compare(VERSION, $core_ver);
			}
			$modules[$fmod]['name'] = $name;
			$modules[$fmod]['alt_name'] = $fmod;
			$modules[$fmod]['update'] = modules::CheckNewVersion($fmod);
			$modules[$fmod]['image'] = $image;
			$modules[$fmod]['description'] = $description;
			$modules[$fmod]['core_ver'] = $core_ver;
			$modules[$fmod]['comp'] = $comp;
			$modules[$fmod]['dependency'] = $dependency;
			$modules[$fmod]['files'] = $files;
			$modules[$fmod]['active'] = "no";
			$modules[$fmod]['module'] = $fmod;
		}
		return $modules;
	}
	
	function sorts(&$arr) {
		$arrs = array_values($arr);
		$new = array();
		for($u=0;$u<sizeof($arrs);$u++) {
			$new[$arrs[$u]['id']] = $arrs[$u];
		}
		sort($new);
		$arr = $new;
	}
	
	function __construct() {
		if(isset($_GET['uninstall']) && is_string($_GET['uninstall'])) {
			$res = modules::UnInstall($_GET['uninstall']);
			if($res) {
				cache::Delete("load_modules");
				if(ajax_check()=="ajax") {
					HTTP::echos("Done");die();
				} else {
					location("{C_default_http_host}".ADMINCP_DIRECTORY."/?pages=ModuleList&action=UnDone");
				}
			} else {
				if(ajax_check()=="ajax") {
					HTTP::echos("Fail");die();
				} else {
					location("{C_default_http_host}".ADMINCP_DIRECTORY."/?pages=ModuleList&action=UnFail");
				}
			}
			return;
		}
		if(isset($_GET['unactive']) && is_string($_GET['unactive'])) {
			cardinal::RegAction("Отключение модуля ".$_GET['unactive']);
			db::doquery("UPDATE `".PREFIX_DB."modules` SET `activ` = \"no\" WHERE `module` = \"".saves($_GET['unactive'], true)."\"");
			cache::Delete("load_modules");
			location("{C_default_http_host}".ADMINCP_DIRECTORY."/?pages=ModuleList");
			return;
		}
		if(isset($_GET['active']) && is_string($_GET['active'])) {
			cardinal::RegAction("Включение модуля ".$_GET['active']);
			db::doquery("UPDATE `".PREFIX_DB."modules` SET `activ` = \"yes\" WHERE `module` = \"".saves($_GET['active'], true)."\"");
			cache::Delete("load_modules");
			location("{C_default_http_host}".ADMINCP_DIRECTORY."/?pages=ModuleList");
			return;
		}
		$modules = $this->ReadModules();
		$sort = 0;
		db::doquery("SELECT `id`, `file`, `module`, `activ` FROM `".PREFIX_DB."modules` ORDER BY `type` ASC, `id` ASC", true);
		while($row = db::fetch_assoc()) {
			$modules[$row['module']]['id'] = $sort;
			$modules[$row['module']]['module'] = $row['module'];
			$modules[$row['module']]['active'] = $row['activ'];
			$sort++;
		}
		$this->sorts($modules);
		for($i=0;$i<sizeof($modules);$i++) {
			if(!isset($modules[$i]['name']) || !isset($modules[$i]['image']) || !isset($modules[$i]['files']) || !isset($modules[$i]['active']) || !isset($modules[$i]['module'])) {
				continue;
			}
			if(isset($modules[$i]['description']) && !empty($modules[$i]['description'])) {
				$is_descr = "1";
			} else {
				$is_descr = "0";
			}
			templates::assign_vars(array("name" => $modules[$i]['name'], "image" => $modules[$i]['image'], "file" => $modules[$i]['files'][0], "active" => $modules[$i]['active'], "module" => $modules[$i]['module'], "is_descr" => $is_descr, "description" => $modules[$i]['description']), "ListModules", "list".$i);
		}
		$this->Prints("Module");
	}
	
}

?>