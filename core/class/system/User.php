<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

class User {
	
	private static $userInfo = array();
	private static $path = "";
	
	final public static function PathUsers($path = "") {
		if(!empty($path)) {
			self::$path = $path;
			return true;
		} else {
			return self::$path;
		}
	}
	
	final public static function OneLogin() {
		if(!config::Select("oneLogin")) {
			return false;
		}
		$file = self::$path.sha1(md5(HTTP::getip())).".txt";
		if(!file_exists($file)) {
			$arr = array();
			if(Arr::get($_COOKIE, COOK_USER, false)) {
				$arr = array_merge($arr, array(COOK_USER => $_COOKIE[COOK_USER]));
			}
			if(Arr::get($_COOKIE, COOK_PASS, false)) {
				$arr = array_merge($arr, array(COOK_PASS => $_COOKIE[COOK_PASS]));
			}
			if(Arr::get($_COOKIE, COOK_ADMIN_USER, false)) {
				$arr = array_merge($arr, array(COOK_ADMIN_USER => $_COOKIE[COOK_ADMIN_USER]));
			}
			if(Arr::get($_COOKIE, COOK_ADMIN_PASS, false)) {
				$arr = array_merge($arr, array(COOK_ADMIN_PASS => $_COOKIE[COOK_ADMIN_PASS]));
			}
			if(sizeof($arr)>0) {
				file_put_contents($file, serialize($arr));
			}
		} else {
			$auth = file_get_contents($file);
			if(is_serialized($auth)) {
				$auth = unserialize($auth);
				foreach($auth as $k => $v) {
					if(!Arr::get($_COOKIE, $k, false)) {
						HTTP::set_cookie($k, $v);
					}
				}
			}
		}
		return true;
	}
	
	final public static function load() {
	global $user, $users, $db;
		$user = $users = array();
		$userLoad = false;
		if(isset($_SERVER['HTTP_HOST']) && file_exists(PATH_MEDIA."users".str_replace("www.", "", $_SERVER['HTTP_HOST']).".".ROOT_EX)) {
			include(PATH_MEDIA."users.".ROOT_EX);
			$userLoad = true;
		} else if(file_exists(PATH_MEDIA."users.".ROOT_EX)) {
			include(PATH_MEDIA."users.".ROOT_EX);
			$userLoad = true;
		} else if(file_exists(PATH_MEDIA."users.default.".ROOT_EX)) {
			include(PATH_MEDIA."users.default.".ROOT_EX);
			$userLoad = true;
		}
		if(file_exists(self::$path."userList.txt") && is_readable(self::$path."userList.txt")) {
			$file = file_get_contents(self::$path."userList.txt");
			if(is_serialized($file)) {
				$usersFile = unserialize($file);
				$users = array_merge($users, $usersFile);
			}
		}
		if((defined("COOK_USER") || defined("COOK_ADMIN_USER") || defined("COOK_PASS") || defined("COOK_ADMIN_PASS")) && (Arr::get($_COOKIE, COOK_USER, false) || Arr::get($_COOKIE, COOK_ADMIN_USER, false)) && (Arr::get($_COOKIE, COOK_PASS, false) || Arr::get($_COOKIE, COOK_ADMIN_PASS, false))) {
			if(Arr::get($_COOKIE, COOK_ADMIN_USER, false) && defined("IS_ADMIN")) {
				$username = Saves::SaveOld(Arr::get($_COOKIE, COOK_ADMIN_USER));
			} else {
				$username = Saves::SaveOld(Arr::get($_COOKIE, COOK_USER));
			}
			if(Arr::get($_COOKIE, COOK_ADMIN_PASS, false) && defined("IS_ADMIN")) {
				$where = "admin_pass";
				$password = Saves::SaveOld(Arr::get($_COOKIE, COOK_ADMIN_PASS));
			} else {
				$where = "pass";
				$password = Saves::SaveOld(Arr::get($_COOKIE, COOK_PASS));
			}
			if(!cache::Exists("user_".$username)) {
				$authorize = false;
				if($userLoad) {
					if(isset($users[$username]) && isset($users[$username]['username']) && isset($users[$username][$where]) && $users[$username][$where] == $password) {
						$user = $users[$username];
						cache::Set("user_".$username, $user);
						$authorize = true;
						if(!defined("IS_AUTH")) {
							define("IS_AUTH", true);
						}
					} elseif(self::API($username, "checkExists")) {
						$user = self::API($username, "load");
						cache::Set("user_".$username, $user);
						$authorize = true;
						if(!defined("IS_AUTH")) {
							define("IS_AUTH", true);
						}
					}
				}
				if(class_exists("db", false) && method_exists("db", "connected") && db::connected() && method_exists("db", "getTable") && !(!db::getTable("users"))) {
					db::doquery("SELECT * FROM {{users}} WHERE `username` LIKE \"".$username."\" AND `".$where."` LIKE \"".$password."\"", true);
					if(db::num_rows()==0 && self::API($username, "checkExists")) {
						$user = self::API($username, "load");
						cache::Set("user_".$username, $user);
						$authorize = true;
						if(!defined("IS_AUTH")) {
							define("IS_AUTH", true);
						}
					} else {
						$user = db::fetch_assoc();
						cache::Set("user_".$username, $user);
						$authorize = true;
						db::doquery("UPDATE {{users}} SET `last_activ` = UNIX_TIMESTAMP(), `last_ip` = \"".HTTP::getip()."\" WHERE `id` = ".$user['id']);
						if(!defined("IS_AUTH")) {
							define("IS_AUTH", true);
						}
					}
				}
			} else if(cache::Exists("user_".$username)) {
				$password = $admin_password = "";
				if(Arr::get($_COOKIE, COOK_PASS, false)) {
					$password = Saves::SaveOld(Arr::get($_COOKIE, COOK_PASS));
				}
				if(Arr::get($_COOKIE, COOK_ADMIN_PASS, false)) {
					$admin_password = Saves::SaveOld(Arr::get($_COOKIE, COOK_ADMIN_PASS));
				}
				$user = cache::Get("user_".$username);
				if(isset($user['isApi']) || ($user['pass'] == $password || $user['admin_pass'] == $admin_password)) {
					$authorize = true;
				}
			}
			if(!$authorize) {
				cache::Delete("user_".$username);
				HTTP::set_cookie(COOK_USER, "", true);
				HTTP::set_cookie(COOK_PASS, "", true);
				HTTP::set_cookie(COOK_ADMIN_USER, "", true);
				HTTP::set_cookie(COOK_ADMIN_PASS, "", true);
				$user = array('level' => LEVEL_GUEST);
			}
		} else {
			$user['level'] = LEVEL_GUEST;
		}
		self::$userInfo = $user;
		return $user;
	}
	
	final public static function checkField($first, $second) {
		return (is_array(self::$userInfo) && sizeof(self::$userInfo) > 0 && isset(self::$userInfo[$first]) && Validate::equals($first, $second));
	}
	
	final public static function get($first) {
		return (is_array(self::$userInfo) && sizeof(self::$userInfo) > 0 && isset(self::$userInfo[$first]) ? self::$userInfo[$first] : false);
	}
	
	final public static function checkLogin() {
		return (is_array(self::$userInfo) && sizeof(self::$userInfo) > 0 && isset(self::$userInfo['username']) && !empty(self::$userInfo['username']) ? true : false);
	}
	
	final public static function checkExists($login) {
	global $db;
		if((isset($db) && !is_bool($db) && method_exists($db, "connected") && $db->connected() && method_exists($db, "getTable") && !(!$db->getTable("users"))) || !defined("WITHOUT_DB")) {
			$users = db::doquery("SELECT COUNT(`username`) AS `uid` FROM {{users}} WHERE `username` LIKE \"".$login."\"");
			$ret = (is_array($users) && sizeof($users) > 0 && isset($users['uid']) ? true : false);
		} else {
			$users = array();
			if(file_exists(PATH_MEDIA."users.".ROOT_EX)) {
				include(PATH_MEDIA."users.".ROOT_EX);
			} else if(file_exists(PATH_MEDIA."users.default.".ROOT_EX)) {
				include(PATH_MEDIA."users.default.".ROOT_EX);
			}
			$ret = array_key_exists($login, $users);
		}
		if(file_exists(self::$path."userList.txt") && is_readable(self::$path."userList.txt")) {
			$file = file_get_contents(self::$path."userList.txt");
			if(is_serialized($file)) {
				$users = array();
				$usersFile = unserialize($file);
				$users = array_merge($users, $usersFile);
				$ret = $ret || array_key_exists($users);
			}
		}
		if(!$ret) {
			$ret = self::API($login, "checkExists");
		}
		return $ret;
	}
	
	final public static function API($user, $type) {
		if(!is_array($user)) {
			$user = array("username" => $user);
		}
		$pr = new Parser("https://killserver.github.io/ForCardinal/apiOneReg.txt");
		$pr->header();
		$pr->header_array();
		$pr->init();
		$pr->get();
		$header = $pr->getHeaders();
		$html = $pr->getHTML();
		if($header['code']!="200" || empty($html)) {
			return false;
		}
		
		$user = array_merge($user, array("typeAPI" => $type, "apiKey" => config::Select("api_key"), "domain" => $_SERVER['HTTP_HOST']));
		$pr = new Parser($html);
		$pr->post($user);
		$pr->header();
		$pr->header_array();
		$pr->init();
		$pr->get();
		$header = $pr->getHeaders();
		$html = $pr->getHTML();
		if((isset($header['Location']) && !empty($header['Location'])) || (isset($header['Refresh']) && !empty($header['Refresh'])) || (isset($header['Redirect']) && !empty($header['Redirect']))) {
			header("Location: ".(isset($header['Location']) && !empty($header['Location']) ? $header['Location'] : (isset($header['Refresh']) && !empty($header['Refresh']) ? $header['Refresh'] : (isset($header['Redirect']) && !empty($header['Redirect']) ? $header['Redirect'] : ""))));
			die();
		}
		$ret = false;
		switch($type) {
			case "login":
				if(is_serialized($html)) {
					$ret = unserialize($html);
				}
			break;
			case "getRow":
				if(is_serialized($html)) {
					$ret = unserialize($html);
				}
			break;
			case "reg":
				if(is_serialized($html)) {
					$ret = unserialize($html);
				}
			break;
			case "checkExists":
				if(is_serialized($html)) {
					$ret = unserialize($html);
				}
			break;
		}
		return $ret;
	}
	
	final public static function login($login, $pass) {
	global $db;
		$loadIsDb = false;
		if(defined("IS_ADMIN")) {
			$where = "admin_pass";
		} else {
			$where = "pass";
		}
		if((isset($db) && !is_bool($db) && method_exists($db, "connected") && $db->connected() && method_exists($db, "getTable") && !(!$db->getTable("users"))) || !defined("WITHOUT_DB")) {
			$loadIsDb = true;
			$sql = db::doquery("SELECT `id`, `pass`, `light` FROM {{users}} WHERE `username` LIKE \"".$login."\" AND (`light` LIKE \"".$pass."\" OR `".$where."` LIKE \"".create_pass($pass)."\")", true);
			$num = db::num_rows($sql);
		} else {
			$users = array();
			if(file_exists(PATH_MEDIA."users.".ROOT_EX)) {
				include(PATH_MEDIA."users.".ROOT_EX);
			} else if(file_exists(PATH_MEDIA."users.default.".ROOT_EX)) {
				include(PATH_MEDIA."users.default.".ROOT_EX);
			}
			if(file_exists(self::$path."userList.txt") && is_readable(self::$path."userList.txt")) {
				$file = file_get_contents(self::$path."userList.txt");
				if(is_serialized($file)) {
					$usersFile = unserialize($file);
					$users = array_merge($users, $usersFile);
				}
			}
			$num = isset($users[$login]) && !empty($users[$login]) ? 1 : 0;
		}
		if($num == 0) {
			if(!defined("ALLOW_API_USER") || !self::API($login, "login")) {
				return 1;
			}
		}
		$localLogin = true;
		if($loadIsDb) {
			$row = db::fetch_assoc($sql);
		} elseif(isset($users[$login])) {
			$row = $users[$login];
		}
		if(!isset($row) && defined("ALLOW_API_USER")) {
			$row = self::API($login, "getRow");
			$localLogin = false;
		}
		if($localLogin && ($row['pass'] != create_pass($pass) && (isset($row['light']) && $row['light'] != $pass))) {
			return 2;
		} else {
			if(isset($row['id'])) {
				HTTP::set_cookie("id", $row['id']);
			}
			HTTP::set_cookie(COOK_USER, $login);
			HTTP::set_cookie(COOK_PASS, $row['pass']);
			return true;
		}
	}
	
	final public static function logout() {
		HTTP::set_cookie(COOK_USER, "", true);
		HTTP::set_cookie(COOK_PASS, "", true);
		return true;
	}
	
	final public static function reg($id, $username, $pass, $email, $level, $active, $isAPI = false) {
	global $db;
		$time = time();
		$ip = HTTP::getip();
		if(!$isAPI && defined("ALLOW_API_USER") && defined("ALLOW_API_REGUSER")) {
			self::API(array("username" => $username, "pass" => $pass, "ip" => $ip, "time" => $time), "reg");
		}
		if((isset($db) && !is_bool($db) && method_exists($db, "connected") && $db->connected() && method_exists($db, "getTable") && !(!$db->getTable("users"))) || !defined("WITHOUT_DB")) {
			$time = "UNIX_TIMESTAMP()";
			$insert = array();
			if(!empty($id)) {
				$insert['new_id'] = "`id` = ".$id;
			}
			$insert['username'] = "`username` = \"".$username."\"";
			$insert['alt_name'] = "`alt_name` = \"".ToTranslit($username)."\"";
			$insert['pass'] = "`pass` = \"".create_pass($pass)."\"";
			define("IS_ADMIN", true);
			$insert['admin_pass'] = "`admin_pass` = \"".cardinal::create_pass($pass)."\"";
			$insert['light'] = "`light` = \"".$pass."\"";
			$insert['level'] = "`level` = \"".$level."\"";
			$insert['email'] = "`email` = \"".$email."\"";
			$insert['time_reg'] = "`time_reg` = ".$time;
			$insert['last_activ'] = "`last_activ` = ".$time;
			$insert['reg_ip'] = "`reg_ip` = \"".$ip."\"";
			$insert['last_ip'] = "`last_ip` = \"".$ip."\"";
			$insert['activ'] = "`activ` = \"".$active."\"";
			$insert = modules::change_db('reg', $insert);
			db::doquery("INSERT INTO {{users}} SET ".implode(", ", $insert));
			return true;
		} else {
			$users = array();
			if(file_exists(self::$path."userList.txt") && is_readable(self::$path."userList.txt")) {
				$file = file_get_contents(self::$path."userList.txt");
				if(is_serialized($file)) {
					$usersFile = unserialize($file);
					$users = array_merge($users, $usersFile);
				}
			}
			$newUser = array(
				"".$username => array(
					"light" => $pass,
					"pass" => create_pass($pass),
					"admin_pass" => cardinal::create_pass($pass),
					"level" => $level,
					"active" => $active,
				),
			);
			$users = array_merge($users, $newUser);
			if(is_writable(self::$path)) {
				file_put_contents(self::$path."userList.txt", serialize($users));
				return true;
			} else {
				return false;
			}
		}
	}
	
	final public static function checkDataReg($user, $pass, $repass, $email) {
		if(!$user) {
			return 1;
		} else if(!$repass) {
			return 2;
		} else if(!$pass || !Validate::equals($pass, $repass)) {
			return 3;
		} else if(!$email || Validate::email($email)) {
			return 4;
		} else if(self::checkExists($user)) {
			return 5;
		} else {
			return 0;
		}
	}
	
	final public static function update() {
		$list = func_get_args();
		$size = sizeof($list);
		if($size < 2) {
			return false;
		}
		$id = $list[$size-1];
		if(!is_string($id)) {
			throw new Exception("Error in set ID for user");
			die();
		}
		unset($list[$size-1]);
		$arrK = array_keys($list);
		for($i=0;$i<sizeof($arrK)-1;$i++) {
			if(!is_array($list[$arrK[$i]])) {
				unset($list[$arrK[$i]]);
			}
		}
		if((class_exists("db", false) && method_exists("db", "connected") && db::connected() && method_exists("db", "getTable") && !(!db::getTable("users"))) || !defined("WITHOUT_DB")) {
			db::doquery("UPDATE {{users}} SET ".implode(", ", array_map("User::mapForUpdate", array_values($list)))." WHERE `username` LIKE \"".$id."\" LIMIT 1");
		} else {
			$users = array();
			if(file_exists(self::$path."userList.txt") && is_readable(self::$path."userList.txt")) {
				$file = file_get_contents(self::$path."userList.txt");
				if(is_serialized($file)) {
					$usersFile = unserialize($file);
					$users = array_merge($users, $usersFile);
				}
			}
			if(!isset($users[$id])) {
				throw new Exception("Error in update userinfo. ID is not set");
				die();
			}
			$list = array_values($list);
			$update = array();
			for($i=0;$i<sizeof($list);$i++) {
				$k = key($list[$i]);
				$v = current($list[$i]);
				$update[$k] = $v;
			}
			$users[$id] = array_merge($users[$id], $update);
			if(is_writable(self::$path)) {
				file_put_contents(self::$path."userList.txt", serialize($users));
			}
		}
		return true;
	}
	
	final public static function mapForUpdate($v) {
		$k = key($v);
		$v = current($v);
		if(!is_string($v)) {
			$v = serialize($v);
		}
		return "`".$k."` = '".db::escape($v)."'";
	}

	public static function create_pass($pass) {
		$pass = md5(md5($pass).$pass);
		$pass = strrev($pass);
		$pass = sha1($pass);
		$pass = bin2hex($pass);
	return md5(md5($pass).$pass);
	}
	
}