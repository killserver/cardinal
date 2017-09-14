<?php

class Archer_Edit {
	
	public function __construct() {
		KernelArcher::callback("Add", "TraceOn", array(&$this, "Headers"));
		KernelArcher::callback("Edit", "TraceOn", array(&$this, "Headers"));
	}
	
	public function Headers($table, $page, $models, $tpl) {
		$getExclude = KernelArcher::excludeField("get", "Edit");
		$list = $models->getArray();
		$first = $models->getFirst();
		$isId = $list[$first];
		unset($list[$first]);
		if(isset(KernelArcher::$editModel["Edit"])) {
			for($i=0;$i<sizeof(KernelArcher::$editModel["Edit"]);$i++) {
				$list = call_user_func_array(KernelArcher::$editModel["Edit"][$i], array($list, $getExclude));
			}
		}
		$where = $whereData = "";
		$addition = "";
		if(Arr::get($_GET, "Where", false)) {
			$where = Arr::get($_GET, "Where");
			$addition .= "&Where=".$where;
		}
		if(Arr::get($_GET, "WhereData", false)) {
			$whereData = Arr::get($_GET, "WhereData");
			$addition .= "&WhereData=".$whereData;
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
		$body = "";
		foreach($list as $k => $v) {
			if(in_array($k, $getExclude)) {
				continue;
			}
			$l = $models->getAttribute($k, 'type');
			$default = ($where==$k ? $whereData : "");
			$typeData = $models->getAttribute($k, "typeData");
			if(is_array($v) && (!empty($where) && !empty($whereData) && $where==$k && isset($v[$whereData]) || isset($v['default']))) {
				$default = (!empty($where) && !empty($whereData) && $where==$k && isset($v[$whereData]) ? $v[$whereData] : $v['default']);
				if(isset($v['default'])) {
					unset($v['default']);
				}
				$v = array_values($v);
			} elseif(is_array($typeData) && sizeof($typeData)>0) {
				$default = (!empty($where) && !empty($whereData) && $where==$k && isset($v[$whereData]) ? $v[$whereData] : $v);
				$v = implode(",", $typeData);
			}
			$body .= KernelArcher::Viewing($l, $k, $v, $default);
		}
		$tpl = str_replace("{addition}", $addition, $tpl);
		$tpl = str_replace("{ArcherPage}", $page.($page!="Add" ? "&viewId=".$isId : ""), $tpl);
		$tpl = str_replace("{ArcherPath}", str_replace(PREFIX_DB, "", $table), $tpl);
		$tpl = str_replace("{ArcherMind}", "{L_".$page."}&nbsp;{L_".str_replace(PREFIX_DB, "", $table)."}", $tpl);
		$tpl = str_replace("{ArcherData}", "\n".$body, $tpl);
		//var_dump($tpl);die();
		return $tpl;
	}
	
}