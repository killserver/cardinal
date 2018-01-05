<?php

class Archer_Shield {
	
	private function in_array_strpos($str, $arr, $rebuild = false) {
		$ret = false;
		$arr = array_values($arr);
		for($i=0;$i<sizeof($arr);$i++) {
			if($rebuild) {
				$res = strpos($arr[$i], $str)!==false;
			} else {
				$res = strpos($str, $arr[$i])!==false;
			}
			if($res) {
				$ret = true;
				break;
			}
		}
		return $ret;
	}
	
	public function __construct() {
		KernelArcher::callback("Shield", "TraceOn", array(&$this, "Headers"));
	}
	
	public function Headers($table, $page, $model, $tpl) {
		$modelName = get_class($model);
		$getExclude = KernelArcher::excludeField("get", "Shield");
		$first = $model->getFirst();
		$h = $model->getComments();
		$h = array_values($h);
		$head = "";
		$sortBy = array();
		if(isset(KernelArcher::$sortBy) && is_array(KernelArcher::$sortBy) && sizeof(KernelArcher::$sortBy)>0) {
			KernelArcher::$sortBy = array_values(KernelArcher::$sortBy);
			for($i=0;$i<sizeof(KernelArcher::$sortBy);$i++) {
				KernelArcher::$sortBy[$i] = "{L_\"".KernelArcher::$sortBy[$i]."\"}";
			}
		}
		$counts = 0;
		for($i=0;$i<sizeof($h);$i++) {
			if($this->in_array_strpos($h[$i], $getExclude)) {
				continue;
			}
			$altName = str_replace(array("{L_\"", "\"}"), "", $h[$i]);
			if(isset(KernelArcher::$sortBy) && is_array(KernelArcher::$sortBy) && sizeof(KernelArcher::$sortBy)>0 && in_array($h[$i], KernelArcher::$sortBy)) {
				$sortBy[] = "'".$altName."'";
			}
			$head .= "<th data-altName=\"".$altName."\">".$h[$i]."</th>";
			$counts++;
		}
		$head .= (!defined("ADMINCP_DIRECTORY") ? "<th>Options</th>" : "<th>{L_\"options\"}</th>");
		$d = $model->getArray();
		$first = $model->getFirst();
		$d = array_keys($d);
		$data = "";
		for($i=0;$i<sizeof($d);$i++) {
			if($this->in_array_strpos($d[$i], $getExclude)) {
				continue;
			}
			$data .= "<td data-id=\"{".$modelName.".".$first."}\" data-table=\"".$modelName."\" data-name=\"".$d[$i]."\" class=\"infoField\"><span>{".$modelName.".".$d[$i]."}</span></td>";
		}
		$addition = "";
		if(Arr::get($_GET, "Where", false)) {
			$addition .= "&Where=".Arr::get($_GET, "Where");
		}
		if(Arr::get($_GET, "WhereData", false)) {
			$addition .= "&WhereData=".Arr::get($_GET, "WhereData");
		}
		if(Arr::get($_GET, "ShowPages", false)) {
			$addition .= "&ShowPages=".Arr::get($_GET, "ShowPages");
		}
		if(Arr::get($_GET, "orderBy", false)) {
			$addition .= "&orderBy=".Arr::get($_GET, "orderBy");
		}
		if(Arr::get($_GET, "orderTo", false)) {
			$addition .= "&orderTo=".Arr::get($_GET, "orderTo");
		}
		$tpl = str_replace("{addition}", $addition, $tpl);
		$tpl = str_replace("{ArcherFirst}", $first, $tpl);
		$tpl = str_replace("{ArcherMind}", $head, $tpl);
		$tpl = str_replace("{ArcherData}", $data, $tpl);
		$tpl = str_replace("{ArcherPage}", $modelName, $tpl);
		$tpl = str_replace("{ArcherSort}", implode(",", $sortBy), $tpl);
		$tpl = str_replace("{ArcherTable}", str_replace(PREFIX_DB, "", $table), $tpl);
		$tpl = str_replace("{ArcherNotTouch}", $counts, $tpl);
		return $tpl;
	}
	
}