<?php

class page {

	function __construct($lang, $langDB) {
		global $model, $by, $byId, $tmp;
		$routeName = Route::Name();
		$model = execEvent("getObject_model", $model);
		$model = execEvent("getObject_model__".$routeName, $model);
		$tmp = execEvent("getObject_tmp", $tmp);
		$tmp = execEvent("getObject_tmp_".$routeName, $tmp);
		$by = execEvent("getObject_by", $by);
		$by = execEvent("getObject_by_".$routeName, $by);
		$byId = execEvent("getObject_byId", $byId);
		$byId = execEvent("getObject_byId_".$routeName, $byId);
		$model = urldecode($model);
		$tmp = urldecode($tmp);
		$by = urldecode($by);
		$byId = urldecode($byId);
		if($model===null || $byId===null || $tmp===null) {
			return false;
		}
		$getFirst = false;
		if($by!==null) {
			$getFirst = true;
		}
		$db = modules::loadModel($model);
		$whereNeed = execEvent("getObject_where_need", true);
		$whereNeed = execEvent("getObject_where_need_".$routeName, $whereNeed);
		if($whereNeed) {
			if($getFirst) {
				$db->Where($db->getFirst(), $byId);
			} else {
				$db->Where($by, $byId);
			}
		}
		$db = execEvent("getObject_db", $db);
		$db = execEvent("getObject_db_".$routeName, $db);
		$db = $db->Select();
		$row = modules::getDataLang($db, $langDB);
		$row = execEvent("getObject_data", $row);
		$row = execEvent("getObject_data_".$routeName, $row);
		templates::assign_vars($row);
		$tpl = templates::completed_assign_vars($tmp);
		if(ajax_check()=="ajax") {
			callAjax();
			HTTP::echos(templates::view($tpl));
			return false;
		} else {
			templates::completed($tpl);
			templates::display();
		}
	}

}