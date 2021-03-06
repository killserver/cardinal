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
		db::doquery("INSERT INTO `users` SET `username` = \"".$name."\", `alt_name` = \"".ToTranslit($name)."\", `pass` = \"".$pass."\", `admin_pass` = \"".$admin_pass."\", `email` = \"".$email."\", `light` = \"".$password."\", `level` = \"".$level."\", `activ` = \"".$activ."\", `time_reg` = UNIX_TIMESTAMP()");
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
		if(isset($_POST['activ']) && $_POST['activ']=="on") {
			$activ = "yes";
		} else {
			$activ = "no";
		}
		db::doquery("UPDATE `users` SET `username` = \"".$name."\", `alt_name` = \"".ToTranslit($name)."\", `pass` = \"".$pass."\", `admin_pass` = \"".$admin_pass."\", `email` = \"".$email."\", `light` = \"".$password."\", `level` = \"".$level."\", `activ` = \"".$activ."\" WHERE `id` = ".$id);
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
			db::doquery("DELETE FROM `users` WHERE `id` = ".intval($_GET['id']));
			location("./?pages=Users");
			return;
		}
		if(isset($_GET['mod']) && $_GET['mod']=="Edit" && isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id']>0) {
			$row = db::doquery("SELECT `username`, `light`, `level`, `email`, `activ` FROM `users` WHERE `id` = ".intval($_GET['id']));
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
				"activ" => $row['activ'],
			));
			$this->Prints("Users");
			return;
		}
		templates::assign_var("is_edit", "0");
		$searchIP = "";
		if(isset($_GET['search'])) {
			$searchIP = Arr::get($_GET, 'ip', "");
			if(!Validate::ip($search)) {
				$searchIP = "";
			}
		}
		templates::assign_var("search_ip", $search);
		db::doquery("SELECT `id`, `username`, `level`, `email`, `activ` FROM `users`".(!empty($searchIP) ? " WHERE `reg_ip` LIKE \"%".$searchIP."%\" OR `last_ip` LIKE \"%".$searchIP."%\"" : ""), true);
		while($row = db::fetch_assoc()) {
			$row['avatar'] = "http://www.gravatar.com/avatar/".md5(strtolower(trim($row['email'])))."?&s=103";
			templates::assign_vars($row, "users", $row['id']);
			if($row['level']==LEVEL_ADMIN) {
				templates::assign_vars($row, "usersAdmin", $row['id']);
			}
			if($row['level']==LEVEL_MODER) {
				templates::assign_vars($row, "usersModer", $row['id']);
			}
			if($row['level']==LEVEL_USER) {
				templates::assign_vars($row, "usersUser", $row['id']);
			}
		}
		$this->Prints("Users");
	}
	
}