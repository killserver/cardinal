<?php

class UserLevels extends Core {
	
	function __construct() {
		$userlevels = userlevel::all();
		$myLevel = User::get("level");
		if(Arr::get($_GET, 'mod', false) && Arr::get($_GET, 'mod')=="Add") {
			$userlevels = current($userlevels);
			templates::assign_var("name", "");
			templates::assign_var("typePage", "Add");
			$i = 0;
			foreach($userlevels as $key => $value) {
				templates::assign_var("level", str_replace("access_", "", $key), "levelChange", $i);
				templates::assign_var("checked", "no", "levelChange", $i);
				$i++;
			}
			$this->Prints("UserLevels");
			return false;
		}
		if(Arr::get($_GET, 'mod', false) && Arr::get($_GET, 'mod')=="Edit" && isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id']>=0) {
			if(!isset($userlevels[$_GET['id']]) || $myLevel<$_GET['id']) {
				new Errors();
				return false;
			}
			$defs = get_defined_constants(true);
			$defs = $defs['user'];
			$levels = array();
			foreach($defs as $k => $v) {
				if(strpos($k, "LEVEL_")!==false) {
					$levels[$k] = $v;
				}
			}
			$levels = array_flip($levels);
			$userlevels = $userlevels[$_GET['id']];
			templates::assign_var("name", $levels[$_GET['id']]);
			templates::assign_var("isSystem", "yes");
			templates::assign_var("typePage", "Add");
			$i = 0;
			foreach($userlevels as $key => $value) {
				templates::assign_var("level", str_replace("access_", "", $key), "levelChange", "lvl".($i+1));
				templates::assign_var("checked", ($value=="yes" ? "yes" : "no"), "levelChange", "lvl".($i+1));
				$i++;
			}
			$this->Prints("UserLevels");
			return false;
		}
		$arrK = array_keys($userlevels);
		$arrK = array_reverse($arrK);
		$arrV = array_values($userlevels);
		$arrV = array_reverse($arrV);
		$defs = get_defined_constants(true);
		$defs = $defs['user'];
		$levels = array();
		foreach($defs as $k => $v) {
			if(strpos($k, "LEVEL_")!==false) {
				$levels[$k] = $v;
			}
		}
		$levels = array_flip($levels);
		for($i=0;$i<sizeof($arrV);$i++) {
			if($arrK[$i]>=$myLevel) {
				continue;
			}
			templates::assign_vars(array(
				"id" => $arrK[$i],
				"name" => $levels[$i],
				"orName" => $levels[$i],
				"counts" => sizeof($arrV[$i]),
			), "userlevelsList", "level".$i);
		}
		$this->Prints("UserLevelsMain");
	}
	
}