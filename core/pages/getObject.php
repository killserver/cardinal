<?php

class page {

	function __construct() {
		if(Arr::get($_GET, "obj", false)===false || Arr::get($_GET, "byId", false)===false || Arr::get($_GET, "tmp", false)===false) {
			return false;
		}
		$obj = Arr::get($_GET, "obj");
		$getFirst = false;
		if(Arr::get($_GET, "by", false)===false) {
			$by = Arr::get($_GET, "by");
		} else {
			$getFirst = true;
		}
		$byId = Arr::get($_GET, "byId");
		$tmp = Arr::get($_GET, "tmp");
		$db = modules::loadModel($obj);
		if($getFirst) {
			$db->Where($db->getFirst(), $byId);
		} else {
			$db->Where($by, $byId);
		}
		$row = $db->Select();
		$tmp->loadObject($row);
		$tpl = $tmp->completed_assign_vars($tmp);
		if(ajax_check()=="ajax") {
			callAjax();
			HTTP::echos($tmp->view($tpl));
			return false;
		} else {
			$tmp->completed($tpl);
			$tmp->display();
		}
	}

}