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
		$counts = 0;
		for($i=0;$i<sizeof($h);$i++) {
			if($this->in_array_strpos($h[$i], $getExclude)) {
				continue;
			}
			$head .= "<th>".$h[$i]."</th>";
			$counts++;
		}
		$head .= (!defined("ADMINCP_DIRECTORY") ? "<th>Options</th>" : "<th>{L_\"options\"}</th>");
		$d = $model->getArray();
		$d = array_keys($d);
		$data = "";
		for($i=0;$i<sizeof($d);$i++) {
			if($this->in_array_strpos($d[$i], $getExclude)) {
				continue;
			}
			$data .= "<td>{".$modelName.".".$d[$i]."}</td>";
		}
		$tpl = str_replace("{ArcherFirst}", $first, $tpl);
		$tpl = str_replace("{ArcherMind}", $head, $tpl);
		$tpl = str_replace("{ArcherData}", $data, $tpl);
		$tpl = str_replace("{ArcherPage}", $modelName, $tpl);
		$tpl = str_replace("{ArcherTable}", $table, $tpl);
		$tpl = str_replace("{ArcherNotTouch}", $counts, $tpl);
		return $tpl;
	}
	
}