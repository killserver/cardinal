<?php

class Yui extends Core {

	function __construct() {
		if(Arr::get($_GET, "show", false)) {
			$data = array();
			if(file_exists(PATH_CACHE."yui.txt")) {
				$datas = file_get_contents(PATH_CACHE."yui.txt");
				$data = json_decode($datas, true);
				if(Arr::get($_POST, "parent", false) && Arr::get($_POST, "parent")=="1") {
					$arr = array();
					for($i=0;$i<sizeof($data);$i++) {
						if(isset($data[$i]['link']) && Arr::get($_POST, 'nowUri')==$data[$i]['link']) {
							$arr[] = $data[$i];
						}
					}
					$data = $arr;
				}
			}
			cardinal::RegAction("Обновлены инструкции для помощи пользователям в разделе YUI");
			Debug::activShow(false);
			templates::$gzip=false;
			echo json_encode($data);
			die();
		}
		if(Arr::get($_GET, "save", false)) {
			$arr = $this->rebuilder($_POST);
			file_put_contents(PATH_CACHE."yui.txt", json_encode($arr));
			location("{C_default_http_local}{D_ADMINCP_DIRECTORY}/?pages=Yui");
			die();
		}
		templates::assign_var("dataList", "");
		if(file_exists(PATH_CACHE."yui.txt")) {
			$datas = file_get_contents(PATH_CACHE."yui.txt");
			templates::assign_var("dataList", $datas);
		}
		$this->Prints("Yui");
	}

	function rebuilder($data) {
		$arr = array();
		foreach($data as $k => $v) {
			for($i=0;$i<sizeof($v);$i++) {
				if(!isset($arr[$i])) {
					$arr[$i] = array();
				}
				$arr[$i][$k] = $v[$i];
			}
		}
		return $arr;
	}

}

?>