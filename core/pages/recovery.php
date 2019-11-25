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
			foreach($f as $k => &$v) {
                if(isset($data['data'][$k])) {
                    $v = array_merge($data['data'][$k], $v);
                }
            }
		} else {
            $data['data'] = array_Values($data['data']);
			$f = $data;
		}
		return $f;
	}

	function __construct() {
        $req = new Request();
		$level = User::get("level");
		if($level>=LEVEL_USER) {
			location("{C_default_http_local}");
		}
        $tmp = "";
        $args = $userFind = array();
        $send = true;
        if(isset($_GET['step']) && $_GET['step']=="4") {
            if(!isset($req->session["recovery"])) {
                location("./?recovery");
            }
            if(sizeof($_POST)>0) {
                $user = $req->session['recovery_users'];
                $user = current($user);
                $user['pass'] = create_pass($_POST['recovery_step_4']);
                $user['admin_pass'] = create_pass($_POST['recovery_step_4']);
                $user['light'] = $_POST['recovery_step_4'];
                User::update($user);
                if(isset($req->session['recovery_users'])) { unset($req->session['recovery_users']); }
                if(isset($req->session['recovery'])) { unset($req->session['recovery']); }
                if(isset($req->session['recovery_send'])) { unset($req->session['recovery_send']); }
                if(isset($req->session['key'])) { unset($req->session['key']); }
                location("./?login");
            }
            if($req->session['success']) {
                $arrViewing = array("varchar", "recovery_step_4", "", "", true, false, false, "", "", false, $args);
                $tmp .= call_user_func_array("KernelArcher::Viewing", $arrViewing);
            } else {
                $arrViewing = array("varchar", "recovery_step_4", "", "", true, false, false, "", "", false, $args);
                $tmp .= lang::get_lang("recovery_step_4_error");
            }
        } else if(isset($_GET['step']) && $_GET['step']=="3") {
            if(!isset($req->session["recovery"])) {
                location("./?recovery");
            }
            if(sizeof($_POST)>0) {
                $req->session['success'] = $req->session['key']===$_POST["recovery_step_3"];
                location("./?recovery&step=4");
            } else {
                if(isset($req->session['recovery_users'])) { unset($req->session['recovery_users']); }
                if(isset($req->session['recovery'])) { unset($req->session['recovery']); }
                if(isset($req->session['recovery_send'])) { unset($req->session['recovery_send']); }
                if(isset($req->session['key'])) { unset($req->session['key']); }
                $send = false;
                location("./?recovery", 3, false);
                $arrViewing = array("varchar", "recovery_step_3", "", "", true, false, false, "", "", false, $args);
                $tmp .= call_user_func_array("KernelArcher::Viewing", $arrViewing);
            }
        } else if(isset($_GET['step']) && $_GET['step']=="2") {
            if(!isset($req->session["recovery"])) {
                location("./?recovery");
            }
            if(isset($_POST) && sizeof($_POST)>0 && isset($_POST['recovery_step_2'])) {
                $users = User::all(true);
                $find = $_POST['recovery_step_2'];
                $emails = array();
                foreach($req->session['recovery_users'] as $k => $v) {
                    if(isset($v['email']) && !empty($v['email'])) {
                        $emails = $v['email'];
                        break;
                    }
                }
                if(sizeof($emails)==0) {
                    $send = false;
                    $tmp .= "<div>{L_\"У найденых пользователей не указана почта\"}</div>";
                } else if(!isset($req->session["recovery_send"])) {
                    $send = false;
                    $user = key($emails);
                    $emails = current($emails);
                    $mess = lang::get_lang("recovery_mail");
                    $key = generate_uuid4();
                    $data = array(
                        "{username}" => $user,
                        "{host}" => config::Select("default_http_host"),
                        "{key}" => $key,
                        "{time}" => date("d-m-Y H:i:s"),
                        "{ip}" => HTTP::getip(),
                    );
                    $mess = str_replace(array_keys($data), array_values($data), $mess);
                    $resp = nmail($emails, $mess, "Recovery access from ".config::Select("default_http_hostname"));
                    $req->session['key'] = $key;
                    $req->session["recovery_send"] = true;
                    location("./?recovery&step=3", 3, false);
                    $tmp .= "<div>{L_\"Найден пользователь\"}:&nbsp;<b>".$user."</b></div><div>{L_\"Сообщение для восстановления отправлено\"}:&nbsp;<b>".$emails."</b></div><div>{L_'Через 3 секунды - Вы сможете продолжить'}</div>";
                }
            } else {
                $arrViewing = array("varchar", "recovery_step_2", "", "", true, false, false, "", "", false, $args);
                $tmp .= call_user_func_array("KernelArcher::Viewing", $arrViewing);
            }
        } else {
            if(isset($_POST) && sizeof($_POST)>0) {
                $users = User::all(true);
                $find = $_POST['recovery_step_1'];
                foreach($users as $username => $data) {
                    if(isset($data['pass'])) { unset($data['pass']); }
                    if(isset($data['admin_pass'])) { unset($data['admin_pass']); }
                    if(isset($data['light'])) { unset($data['light']); }
                    if(strpos($username, $find)!==false) {
                        $userFind[$username] = $data;
                    }
                    $v = array_values($data);
                    for($i=0;$i<sizeof($v);$i++) {
                        if(strpos($v[$i], $find)!==false) {
                            $userFind[$username] = $data;
                        }
                    }
                }
                $req->session['recovery_users'] = $userFind;
                $req->session["recovery"] = $find;
                location("./?recovery&step=2");
            } else {
                $arrViewing = array("varchar", "recovery_step_1", "", "", true, false, false, "", "", false, $args);
                $tmp .= call_user_func_array("KernelArcher::Viewing", $arrViewing);
            }
        }
		$tpl = ($send ? '<form method="post" class="form-horizontal" enctype="multipart/form-data">' : "").$tmp.($send ? '<button class="btn btn-single"><span>{L_"Сохранить"}</span></button></form>' : "");
		$tpl = execEvent("page_recovery_tpl", $tpl);
		templates::completed($tpl);
		templates::display();
	}

}