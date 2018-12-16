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

	private function ordering($orderByName, $nowField) {
		$ret = false;
		if(is_array($orderByName)) {
			$arr = array_keys($orderByName);
			for($i=0;$i<sizeof($arr);$i++) {
				$name = $orderByName[$arr[$i]];
				if(isset($name[0]) && ($name[0]==$nowField || "{L_\"".$name[0]."\"}"==$nowField || "{L_'".$name[0]."'}"==$nowField)) {
					$ret = $name[1];
					break;
				} else if(isset($name['name']) && ($name['name']==$nowField || "{L_\"".$name['name']."\"}"==$nowField || "{L_'".$name['name']."'}"==$nowField)) {
					$ret = $name['order'];
					break;
				}
			}
			return $ret;
		} else if($orderByName==$nowField || "{L_\"".$orderByName."\"}"==$nowField || "{L_'".$orderByName."'}"==$nowField) {
			return $orderByName[1];
		} else {
			return false;
		}
	}
	
	public function Headers($table, $page, $model, $tpl) {
		$modelName = get_class($model);
		$getExclude = KernelArcher::excludeField("get", "Shield");
		/*$first = $model->getFirst();*/
		$h = $model->getComments(true);
		$h = array_values($h);
		$head = "";
		$sortBy = array();
		if(isset(KernelArcher::$sortBy) && is_array(KernelArcher::$sortBy) && sizeof(KernelArcher::$sortBy)>0) {
			KernelArcher::$sortBy = array_values(KernelArcher::$sortBy);
			for($i=0;$i<sizeof(KernelArcher::$sortBy);$i++) {
				KernelArcher::$sortBy[$i] = "{L_\"".KernelArcher::$sortBy[$i]."\"}";
			}
		}
		$orderById = 0;
		$orderBySort = "desc";
		$myOrderName = "";
		$myOrder = false;
		if(isset(KernelArcher::$orderBy) && is_array(KernelArcher::$orderBy)) {
			$myOrder = (isset(KernelArcher::$orderBy['name']) || isset(KernelArcher::$orderBy[0]) ? true : false);
			if(isset(KernelArcher::$orderBy['name'])) {
				$myOrderName = KernelArcher::$orderBy['name'];
			} else {
				if(isset(KernelArcher::$orderBy[0]) && is_array(KernelArcher::$orderBy[0])) {
					$myOrderName = KernelArcher::$orderBy;
					//$orderBySort = "";
				} else if(isset(KernelArcher::$orderBy[0])) {
					$myOrderName = KernelArcher::$orderBy[0];
				}
			}
			//$orderBySort = (isset(KernelArcher::$orderBy['order']) ? KernelArcher::$orderBy['order'] : (isset(KernelArcher::$orderBy[1]) ? KernelArcher::$orderBy[1] : ""));
		}
		$counts = 0;
		for($i=0;$i<sizeof($h);$i++) {
			$altName = str_replace(array("{L_\"", "\"}"), "", $h[$i]);
			if($this->in_array_strpos($altName, $getExclude, true)) {
				continue;
			}
			if($myOrder && ($res = $this->ordering($myOrderName, $h[$i]))!==false) {
				$orderById = $i;
				$orderBySort = $res;
			}
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
			if($this->in_array_strpos($d[$i], $getExclude, true)) {
				continue;
			}
			$type = $model->getAttribute($d[$i], "type", $table, true);
			$infoField = "infoField";
            $activeQ = false;
			$activeQuickEditor = (is_bool(KernelArcher::$disabledQuickEditor) && KernelArcher::$disabledQuickEditor === false);
			if(!$activeQuickEditor) {
				$activeQ = false;
				if(is_array(KernelArcher::$disabledQuickEditor) && isset(KernelArcher::$disabledQuickEditor[$d[$i]]) && (
					(is_bool(KernelArcher::$disabledQuickEditor[$d[$i]]) && KernelArcher::$disabledQuickEditor[$d[$i]]===true)
					||
					(is_string(KernelArcher::$disabledQuickEditor[$d[$i]]) && KernelArcher::$disabledQuickEditor[$d[$i]]==="yes")
					||
					(is_numeric(KernelArcher::$disabledQuickEditor[$d[$i]]) && KernelArcher::$disabledQuickEditor[$d[$i]]===1)
				)) {
					$activeQ = true;
				}
			}
			if($type=="date") {
				$val = "{S_langdata=\"{".$modelName.".".$d[$i]."}\",\"d F Y\"}";
			} else if($type=="time") {
				$val = "{S_langdata=\"{".$modelName.".".$d[$i]."}\",\"H:i:s\"}";
			} else if($type=="datetime") {
				$val = "{S_langdata=\"{".$modelName.".".$d[$i]."}\",\"d F Y H:i:s\"}{".$modelName.".".$d[$i]."}";
			} else {
				$val = "{L_\"{".$modelName.".".$d[$i]."}\"}";
			}
			$quickEditor = "";
			if($i!=0 && $activeQ) {
				$quickEditor = " data-pk=\"{".$modelName.".".$first."}\" data-name=\"".$d[$i]."\"";
				if($type=="select" || $type=="array" || $type=="enum") {
					$infoField = "";
					//$quickEditor .= " data-type=\"select\" data-source=\"{C_default_http_local}{D_ADMINCP_DIRECTORY}/?pages=Archer&type=".$modelName."&pageType=QuickEdit&loadSelect=".$d[$i]."\"";
				} else if($type=="date") {
					$quickEditor .= " data-type=\"date\"";
				} else if($type=="time") {
					$quickEditor .= " data-type=\"time\"";
				} else if($type=="datetime") {
					$quickEditor .= " data-type=\"datetime\"";
				} else if($type=="int" || $type=="price" || $type=="tinyint" || $type=="smallint" || $type=="mediumint" || $type=="bigint") {
					$quickEditor .= " data-type=\"number\"";
				} else if($type=="shorttext" || $type=="mediumtext" || $type=="text" || $type=="longtext") {
				} else if($type=="varchar") {
					$quickEditor .= " data-type=\"text\"";
				} else {
					$quickEditor .= " data-type=\"text\"";
				}
				$infoField .= " quickEdit";
			}
			execEventRef("KernelArcher::Shield::Element-before", $modelName, $first, $d[$i], $infoField);
			$data .= execEvent("KernelArcher::Shield::Element-before", "", $modelName, $first, $d[$i], $infoField);
			$data .= execEvent("KernelArcher::Shield::Element-".$i."-before", "", $modelName, $first, $d[$i], $infoField);
			$data .= "<td data-id=\"{".$modelName.".".$first."}\" data-table=\"".$modelName."\" data-name=\"".$d[$i]."\" class=\"".$infoField."\"".$quickEditor."><span".$quickEditor.">".execEvent("KernelArcher::Shield::Element", $val, $modelName, $first, $d[$i], $infoField)."</span></td>";
			$data .= execEvent("KernelArcher::Shield::Element-".$i."-after", "", $modelName, $first, $d[$i], $infoField);
			$data .= execEvent("KernelArcher::Shield::Element-after", "", $modelName, $first, $d[$i], $infoField);
			execEventRef("KernelArcher::Shield::Element-after", $modelName, $first, $d[$i], $infoField);
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
		$tpl = str_replace("{orderById}", ($orderById+1), $tpl);
		$tpl = str_replace("{orderBySort}", $orderBySort, $tpl);
		$tpl = str_replace("{addition}", $addition, $tpl);
		$tpl = str_replace("{ArcherFirst}", $first, $tpl);
		$tpl = str_replace("{ArcherMind}", $head, $tpl);
		$tpl = str_replace("{ArcherData}", $data, $tpl);
		$tpl = str_replace("{ArcherPage}", $modelName, $tpl);
		$tpl = str_replace("{ArcherSort}", implode(",", $sortBy), $tpl);
		$tpl = str_replace("{ArcherTable}", str_replace(PREFIX_DB, "", $table), $tpl);
		$countAll = $counts+1;
		if(config::Select("disableMassAction")!==false) {
			$countAll++;
		}
		$tpl = str_replace("{ArcherAll}", ($countAll+1), $tpl);
		$tpl = str_replace("{ArcherNotTouch}", ($countAll), $tpl);
		return $tpl;
	}
	
}