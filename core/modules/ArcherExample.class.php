<?php

class ArcherExample extends modules {
	
	function __construct() {
		KernelArcher::excludeField("add", "Shield", array());
		KernelArcher::excludeField("add", "Edit", array());
		KernelArcher::excludeField("add", "TakeDelete", array());
		KernelArcher::excludeField("add", "Show", array());
		KernelArcher::excludeField("add", "Sorting", array());
		KernelArcher::callback("Shield", "TraceOn", array(&$this, "RebuildShields"));
		KernelArcher::callback("ShieldFunc", "led::RebuildShield");
		KernelArcher::callback("AddModel", array(&$this, "RebuildAddModel"));
		KernelArcher::callback("Show", array(&$this, "RebuildShow"));
		KernelArcher::callback("EditModel", array(&$this, "RebuildEditModel"));
		KernelArcher::callback("TakeUpload", array(&$this, "RebuildTakeUpload"));
		KernelArcher::callback("TakeDelete", array(&$this, "RebuildTakeDelete"));
		KernelArcher::callback("TakeAddModel", array(&$this, "RebuildTakeAddModel"));
		KernelArcher::callback("TakeEditModel", array(&$this, "RebuildTakeEditModel"));
	}

	public function RebuildTakeDelete($model, $models) {
		return array("model" => $model, "models" => $models);
	}

	public function RebuildShields($table, $page, $model, $tpl) {
		defines::add("DisableSort", "0");
		return $tpl;
	}

	public function RebuildShow($table, $tpl, $model) {
		$model->SetTable($table);
		return array($table, $tpl, $model);
	}

	public static function RebuildShield($row) {
		return $row;
	}

	public function RebuildTakeUpload($model, $field, $id, $file, $path, $type = "", $i = -1) {
		return array($model, $field, $id, $file, $path, $type, "fileName" => $id.($i>=0 ? "_".$i : ""));
	}

	public static function RebuildEditModel($model, &$exc = array()) {
		return $model;
	}

	function RebuildAddModel($model, &$exc = array()) {
		return $model;
	}

	function RebuildTakeAddModel($model, $id, $countCall) {
		return $model;
	}

	function RebuildTakeEditModel($model, $id, $countCall) {
		return $model;
	}
	
}