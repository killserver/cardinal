<?php

class Archer_Sorting {
	
	public function __construct() {
		KernelArcher::callback("Sorting", "TraceOn", array(&$this, "Headers"));
	}
	
	private function rebuildName($arr, $modelName) {
		$arrs = array_keys($arr);
		for($i=0;$i<sizeof($arrs);$i++) {
			$arr[$arrs[$i]] = "<b>".$arrs[$i]."</b>: {".$modelName.".".$arrs[$i]."}";
		}
		return $arr;
	}
	
	public function Headers($table, $page, $model, $tpl) {
		if(is_array($model)) {
			$model = current($model);
		}
		$objName = get_class($model);
		$model = $model->getArray();
		$firstId = key($model);
		$getExclude = KernelArcher::excludeField("get", "Sorting");
		$models = array_keys($model);
		for($i=0;$i<sizeof($models);$i++) {
			if(in_array($models[$i], $getExclude)) {
				unset($model[$models[$i]]);
			}
		}
		$model = $this->rebuildName($model, $objName);
		$tpl = str_replace("{ShowPage}", str_replace(PREFIX_DB, "", $table), $tpl);
		$tpl = str_replace("{ShowSort}", $objName, $tpl);
		$tpl = str_replace("{ShowID}", $firstId, $tpl);
		$tpl = str_replace("{ShowName}", implode(" ", $model), $tpl);
		return $tpl;
	}
	
}