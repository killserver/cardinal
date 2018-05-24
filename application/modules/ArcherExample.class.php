<?php

class ArcherExample extends modules {
	
	function __construct() {
		KernelArcher::excludeField("add", "Shield", array()); // поля, которые нужно исключить из просмотра списка добавленных данных
		KernelArcher::excludeField("add", "Edit", array()); // поля, которые нужно исключить из добавления/редактирования
		KernelArcher::excludeField("add", "TakeDelete", array()); // поля, которые нужно исключить из удаления
		KernelArcher::excludeField("add", "Show", array()); // поля, которые нужно исключить из раздела просмотра добавленных данных
		KernelArcher::excludeField("add", "Sorting", array()); // поля, которые нужно исключить из сортировки
		KernelArcher::callback("Shield", "TraceOn", array(&$this, "RebuildShields")); // функция, которая вызывается при просмотре списка добавленных данных
		KernelArcher::callback("ShieldFunc", "ArcherExample::RebuildShield"); // функция, которая вызывается для каждого элемента при выводе его в виде списка добавленных данных
		KernelArcher::callback("AddModel", array(&$this, "RebuildAddModel")); // функция, которая вызывается при добавлении данных
		KernelArcher::callback("Show", array(&$this, "RebuildShow")); // функция, которая вызывается при добавлении данных
		KernelArcher::callback("EditModel", array(&$this, "RebuildEditModel")); // функция, которая вызывается при редактировании данных
		KernelArcher::callback("TakeUpload", array(&$this, "RebuildTakeUpload")); // функция, которая вызывается при загрузке файлов
		KernelArcher::callback("TakeDelete", array(&$this, "RebuildTakeDelete")); // функция, которая вызывается при удалении данных
		KernelArcher::callback("TakeAddModel", array(&$this, "RebuildTakeAddModel")); // функция, которая вызывается перед тем, как данные будут добавлены в базу данных
		KernelArcher::callback("TakeEditModel", array(&$this, "RebuildTakeEditModel")); // функция, которая вызывается перед тем, как данные будут отредактированны в базе данных
	}

	public function RebuildTakeDelete($model, $models) {
		return array("model" => $model, "models" => $models);
	}

	public function RebuildShields($table, $page, $model, $tpl) {
		return array($table, $page, $model, $tpl);
	}

	public function RebuildShow($table, $tpl, $model) {
		$model->SetTable($table);
		return array($table, $tpl, $model);
	}

	public static function RebuildShield($row) {
		return array($row);
	}

	public function RebuildTakeUpload($model, $field, $id, $file, $path, $type = "", $i = -1) {
		return array($model, $field, $id, $file, $path, $type, "fileName" => $id.($i>=0 ? "_".$i : ""));
	}

	public static function RebuildEditModel($model, &$exc = array()) {
		return array($model);
	}

	function RebuildAddModel($model, &$exc = array()) {
		return array($model);
	}

	function RebuildTakeAddModel($model, $id, $countCall) {
		return array($model, $id, $countCall);
	}

	function RebuildTakeEditModel($model, $id, $countCall) {
		return array($model, $id, $countCall);
	}
	
}