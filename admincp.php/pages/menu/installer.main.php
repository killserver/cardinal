<?php
$arr = array();
if(!function_exists("getVersionForModule")) {
	function getVersionForModule($module) {
		if(file_exists(PATH_MODULES.$module.".class.".ROOT_EX)) {
			include_once(PATH_MODULES.$module.".class.".ROOT_EX);
		}
		return (class_exists($module) && property_exists($module, "version") ? $module::$version : "0.0");
	}
}
if(is_writable(PATH_CACHE_SYSTEM) && (!file_exists(PATH_CACHE_SYSTEM."installer.txt") || (fileatime(PATH_CACHE_SYSTEM."installer.txt")-(24*60*60))>=time())) {
	$configs = array("https://raw.githubusercontent.com/killserver/modulesForCardinal/master/list.min.json");
	$configs = execEvent("installer_servers", $configs);
	$listAll = array();
	for($i=0;$i<sizeof($configs);$i++) {
		$listMirror = new Parser($configs[$i]."?".time());
		$listMirror->timeout(3);
		$listMirror = $listMirror->get();
		$listMirror = json_decode($listMirror, true);
		if($listMirror!==null) {
			$listAll = array_merge($listAll, $listMirror);
		}
	}
	$listAllNew = array();
	foreach($listAll as $name => $data) {
		$listAllNew[$name] = $data['version'];
	}
	$listAll = $listAllNew;
	unset($listAllNew);
	$dt = read_dir(PATH_MODULES, ".class.".ROOT_EX);
	for($i=0;$i<sizeof($dt);$i++) {
		if("SEOBlock.class.php"!==$dt[$i] && "ArcherExample.class.php"!==$dt[$i] && "base.class.php"!==$dt[$i] && "changelog.class.php"!==$dt[$i] && "mobile.class.php"!==$dt[$i]) {
			$dt[$i] = nsubstr($dt[$i], 0, -nstrlen(".class.".ROOT_EX));
			$name = $dt[$i];
			if(isset($listAll[$name]) && getVersionForModule($name)<$listAll[$name]) {
				$arr[$name] = $listAll[$name];
			}
		}
	}
	$size = sizeof($arr);
	file_put_contents(PATH_CACHE_SYSTEM."installer.txt", $size);
} else {
	$size = file_get_contents(PATH_CACHE_SYSTEM."installer.txt");
}


$links['Installer']["cat"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Installer",
'title' => ($size>0 ? "<span class=\"badge pull-right badge-red\">".$size."</span>" : "")."{L_'Установщик модулей'}",
'type' => "cat",
'access' => userlevel::get("installer"),
'icon' => 'fa-list-alt',
);
$links['Installer']["item"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Installer",
'title' => ($size>0 ? "<span class=\"badge pull-right badge-red\">".$size."</span>" : "")."{L_'Установщик модулей'}",
'type' => "item",
'access' => userlevel::get("installer"),
'icon' => '',
);
?>