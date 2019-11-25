<?php

class page {

	private function getUserFields($d) {
		$data = array(
			"mode" => "add",
			"data" => array(
				"username" => array(
					"beforeAltName" => "username",
					"depth" => "0",
					"parent_id" => "0",
					"name" => "Логин",
					"altName" => "username",
					"type" => "varchar",
					"default" => "",
					"placeholder" => "",
					"alttitle" => "username",
					"translate" => "1",
					"id" => 0,
					"onlyAttr" => "onlyAttr",
					"applyInLogin" => "applyInLogin",
				),
				"pass" => array(
					"beforeAltName" => "pass",
					"depth" => "0",
					"parent_id" => "0",
					"name" => "Пароль",
					"altName" => "pass",
					"type" => "password",
					"default" => "",
					"placeholder" => "",
					"alttitle" => "pass",
					"translate" => "1",
					"id" => 0,
					"onlyAttr" => "onlyAttr",
					"hideOnMain" => "yes",
					"applyInLogin" => "applyInLogin",
				),
				"level" => array(
					"beforeAltName" => "level",
					"depth" => "0",
					"parent_id" => "0",
					"name" => "Уровнь доступа",
					"altName" => "level",
					"type" => "array",
					"selectedData" => "dataOnInput",
					"field" => $d,
					"default" => "",
					"placeholder" => "",
					"alttitle" => "level",
					"translate" => "1",
					"id" => 0,
					"onlyAttr" => "onlyAttr",
					"disApplyInReg" => "disApplyInReg",
				),
				"email" => array(
					"beforeAltName" => "email",
					"depth" => "0",
					"parent_id" => "0",
					"name" => "Почта",
					"altName" => "email",
					"type" => "email",
					"default" => "",
					"placeholder" => "",
					"alttitle" => "email",
					"translate" => "1",
					"id" => 0,
					"onlyAttr" => "onlyAttr",
				),
			),
		);
		if(file_exists(PROFILE_SETTINGS) && is_readable(PROFILE_SETTINGS)) {
			$f = file_get_contents(PROFILE_SETTINGS);
			$f = str_replace('<?php die(); ?>', "", $f);
			$f = json_decode($f, true);
			foreach($f['data'] as $k => &$v) {
                if(isset($data['data'][$v["altName"]])) {
                    $v = array_merge($data['data'][$v["altName"]], $v);
                }
            }
		} else {
            $data['data'] = array_Values($data['data']);
			$f = $data;
		}
		return $f;
	}

	function __construct() {
		$level = User::get("level");
		if($level>=LEVEL_USER) {
			location("{C_default_http_local}");
		}
		define("PROFILE_SETTINGS", PATH_CACHE_USERDATA."userSettings.php");
		$d = array();
		$s = userlevel::all();
		$s = array_keys($s);
		for($i=0;$i<sizeof($s);$i++) {
			$d[$s[$i]] = lang::get_lang("level", $s[$i]);
		}
		$f = $this->getUserFields($d);

		if(sizeof($_POST)>0) {
			$data = $f['data'];
			$data = execEvent("page_login_data", $data);
			foreach($data as $v) {
				if(isset($v['disApplyInReg'])) continue;
				if(isset($_POST[$v['name']])) {
					$_POST[$v['altName']] = $_POST[$v['name']];
					unset($_POST[$v['name']]);
				}
			}
			HTTP::ajax(array("success" => call_user_func_array("User::create", $_POST)));
		}
		$tmp = "";
		$data = $f['data'];
		$data = execEvent("page_reg_data", $data);
		foreach($data as $v) {
			if(isset($v['disApplyInReg'])) continue;
			$arrViewing = array($v['type'], $v['name'], $value, $v['default'], isset($v['required']), false, false, "", "", false, $args);
			$tmp .= call_user_func_array("KernelArcher::Viewing", $arrViewing);
		}
		$tpl = '<form method="post" class="form-horizontal" enctype="multipart/form-data">'.$tmp.'<button class="btn btn-single"><span>{L_"Сохранить"}</span></button></form>';
		$tpl = execEvent("page_reg_tpl", $tpl);
		templates::completed($tpl);
		templates::display();
	}

}