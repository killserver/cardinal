<?php

class Users extends Core {
	
	private function Add() {
		$name = Saves::SaveOld(Arr::get($_POST, 'name'), true);
		$password = Saves::SaveOld(Arr::get($_POST, 'password'), true);
		$pass = create_pass($password);
		define("IS_ADMIN_PASS", true);
		$admin_pass = create_pass($password);
		$level = intval(Arr::get($_POST, 'level', 0));
		$email = Saves::SaveOld(Arr::get($_POST, 'email'), true);
		if(isset($_POST['activ']) && $_POST['activ']=="on") {
			$activ = "yes";
		} else {
			$activ = "no";
		}
		User::create(array(
			"username" => $name,
			"alt_name" => ToTranslit($name),
			"light" => $password,
			"time_reg" => time(),
			"pass" => $pass,
			"admin_pass" => $admin_pass,
			"level" => $level,
			"email" => $email,
			"activ" => $activ
		));
		cardinal::RegAction("Добавление нового пользователя \"".$name."\"");
		location("./?pages=Users");
		return;
	}
	
	private function Edit($row, $id) {
		$name = Saves::SaveOld(Arr::get($_POST, 'name'), true);
		$password = Saves::SaveOld(Arr::get($_POST, 'password'), true);
		$pass = create_pass($password);
		define("IS_ADMIN_PASS", true);
		$admin_pass = create_pass($password);
		$level = intval(Arr::get($_POST, 'level', 0));
		$email = Saves::SaveOld(Arr::get($_POST, 'email'), true);
		if(isset($_POST['activ']) && ($_POST['activ']=="on" || $_POST['activ']=="1")) {
			$activ = "yes";
		} else {
			$activ = "no";
		}
		User::update(array(
			"username" => $name,
			"alt_name" => ToTranslit($name),
			"light" => $password,
			"time_reg" => time(),
			"pass" => $pass,
			"admin_pass" => $admin_pass,
			"level" => $level,
			"email" => $email,
			"activ" => $activ
		));
		cardinal::RegAction("Обновление данных пользователя \"".$name."\"");
		location("./?pages=Users");
		return;
	}
	
	function __construct() {
	global $user;
		if(isset($_GET['mod']) && $_GET['mod']=="Add") {
			if(sizeof($_POST)>0) {
				$this->Add();
				return;
			}
			templates::assign_vars(array(
				"is_edit" => "2",
				"name" => "",
				"light" => "",
				"level" => LEVEL_USER,
				"email" => "",
				"activ" => "yes",
			));
			$this->Prints("Users");
			return;
		}
		if(isset($_GET['mod']) && $_GET['mod']=="Delete" && isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id']>0) {
			if($_GET['id']==$user['id']) {
				templates::error("Не возможно удалить аккаунт из которого Вы работаете!");
				return;
			}
			$load = User::All();
			$load = array_values($load);
			$id = intval($_GET['id']-1);
			User::remove($load[$id]['username']);
			cardinal::RegAction("Удаление пользователя с ИД \"".intval($_GET['id'])."\"");
			location("./?pages=Users");
			return;
		}
		if(isset($_GET['mod']) && $_GET['mod']=="Edit" && isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id']>0) {
			$load = User::All();
			$load = array_values($load);
			$row = $load[$_GET['id']-1];
			if(sizeof($_POST)>0) {
				$this->Edit($row, intval($_GET['id']));
				return;
			}
			templates::assign_vars(array(
				"is_edit" => "1",
				"name" => $row['username'],
				"light" => $row['light'],
				"level" => $row['level'],
				"email" => $row['email'],
				"activ" => (isset($row['activ']) ? $row['activ'] : "yes"),
			));
			$this->Prints("Users");
			return;
		}
		templates::assign_var("is_edit", "0");
		$searchIP = "";
		if(isset($_GET['search'])) {
			$searchIP = Arr::get($_GET, 'ip', "");
			if(!Validate::ip($searchIP)) {
				$searchIP = "";
			}
		}
		templates::assign_var("search_ip", $searchIP);
		$load = User::All($searchIP);
		$i = 1;
		foreach($load as $k => $row) {
			if(!isset($row['id'])) {
				$row['id'] = $i;
			}
			$row['avatar'] = (isset($row['email']) && !empty($row['email']) ? "http://www.gravatar.com/avatar/".md5(strtolower(trim($row['email'])))."?&s=103" : (!empty($row['avatar']) ? $row['avatar'] : "http://www.splayn.com/pic/no_ava.gif"));
			if(!isset($row['email'])) {
				$row['email'] = "";
			}
			templates::assign_vars($row, "users", $k);
			if($row['level']>=LEVEL_ADMIN) {
				templates::assign_vars($row, "usersAdmin", $k);
			}
			if($row['level']==LEVEL_MODER) {
				templates::assign_vars($row, "usersModer", $k);
			}
			if(!isset($row['level']) || $row['level']==LEVEL_USER) {
				templates::assign_vars($row, "usersUser", $k);
			}
			$i++;
		}
		$this->Prints("Users");
	}
	
}