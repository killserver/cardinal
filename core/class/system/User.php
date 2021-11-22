<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."UserSystem".DIRECTORY_SEPARATOR."UserDB.php");
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."UserSystem".DIRECTORY_SEPARATOR."UserFile.php");

class User {
	
	public static $loadUserFromFile = true;
	public static $callbackLoadUser = false;
	private static $userInfo = array();
	private static $callLogin = array();
	private static $path = "";
	private static $typeSystem = "file";

	final public static function changeSystemSave($type) {
		self::$typeSystem = $type;
	}
	
	private static function caller($fn, $args = array()) {
		$system = self::$typeSystem;
		return call_user_func_array("User".ucfirst($system)."::".$fn, $args);
	}

	final public static function PathUsers($path = "") {
		return self::caller(__FUNCTION__, func_get_args());
	}

	final public static function getUserData($username, $field, $default = false) {
		return self::caller(__FUNCTION__, func_get_args());
	}

	final public static function getInfo($username, $default = array()) {
		return self::caller(__FUNCTION__, func_get_args());
	}

	final public static function All($onlyUsers = true) {
		return self::caller(__FUNCTION__, func_get_args());
	}

	final public static function loadUsers() {
		return self::All(false);
	}
	
	final public static function load() {
		return self::caller(__FUNCTION__, func_get_args());
	}
	
	final public static function checkField($first, $second) {
		return self::caller(__FUNCTION__, func_get_args());
	}
	
	final public static function get($first, $default = false) {
		return self::caller(__FUNCTION__, func_get_args());
	}
	
	final public static function set($first, $second) {
		return self::caller(__FUNCTION__, func_get_args());
	}
	
	final public static function checkLogin() {
		return self::caller(__FUNCTION__, func_get_args());
	}
	
	final public static function checkExists($login, $default = false) {
		return self::caller(__FUNCTION__, func_get_args());
	}

	final public static function addToLogin($fn) {
		return self::caller(__FUNCTION__, func_get_args());
	}
	
	final public static function login($login, $pass) {
		return self::caller(__FUNCTION__, func_get_args());
	}
	
	final public static function logout() {
		return self::caller(__FUNCTION__, func_get_args());
	}
	
	final public static function update($list) {
		return self::caller(__FUNCTION__, func_get_args());
	}
	
	final public static function create($list = array()) {
		return self::caller(__FUNCTION__, func_get_args());
	}

	final public static function remove($username) {
		return self::caller(__FUNCTION__, func_get_args());
	}

	public static function create_pass($pass) {
		$pass = md5(md5($pass).$pass);
		$pass = strrev($pass);
		$pass = sha1($pass);
		$pass = bin2hex($pass);
		return md5(md5($pass).$pass);
	}

	final public static function getUserById($id) {
		return self::caller(__FUNCTION__, func_get_args());
	}
	
}